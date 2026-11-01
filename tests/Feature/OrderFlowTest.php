<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.server_key' => 'SB-test-server-key']);

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer', 'phone' => '081234567890']);

        $category = Category::create(['name' => 'Lenovo ThinkPad', 'slug' => 'lenovo-thinkpad']);
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'ThinkPad T480',
            'processor' => 'Core i5-8350U',
            'ram_gb' => 8,
            'storage' => '256GB SSD',
            'battery_condition' => '85%',
            'physical_condition' => 'Mulus',
            'price' => 3500000,
            'stock' => 2,
        ]);
    }

    private function makeOrder(string $status = Order::STATUS_PENDING): Order
    {
        $order = Order::create([
            'user_id' => $this->customer->id,
            'order_code' => 'FC-TEST-'.uniqid(),
            'total_amount' => 3500000,
            'shipping_address' => 'Jl. Contoh No. 1',
            'status' => $status,
            'paid_at' => $status === Order::STATUS_PAID ? now() : null,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 3500000,
            'subtotal' => 3500000,
        ]);

        return $order;
    }

    private function sendCallback(Order $order, string $status): \Illuminate\Testing\TestResponse
    {
        $gross = '3500000.00';
        $statusCode = '200';

        return $this->postJson(route('midtrans.callback'), [
            'order_id' => $order->order_code,
            'status_code' => $statusCode,
            'gross_amount' => $gross,
            'transaction_status' => $status,
            'payment_type' => 'bank_transfer',
            'signature_key' => hash('sha512', $order->order_code.$statusCode.$gross.'SB-test-server-key'),
        ]);
    }

    public function test_full_flow_from_payment_to_completion(): void
    {
        $order = $this->makeOrder();

        $this->sendCallback($order, 'settlement')->assertOk();
        $order->refresh();
        $this->assertSame(Order::STATUS_PAID, $order->status);
        $this->assertNotNull($order->paid_at);
        $this->assertSame('bank_transfer', $order->payment_type);
        $this->assertSame(1, $this->product->fresh()->stock);

        $this->actingAs($this->admin)->get(route('admin.dashboard'))->assertOk()->assertSee($order->order_code);
        $this->actingAs($this->admin)->get(route('admin.orders.index', ['status' => 'paid']))->assertOk()->assertSee($order->order_code);
        $this->actingAs($this->admin)->get(route('admin.orders.show', $order))->assertOk()->assertSee('Tandai Dikirim');

        $this->actingAs($this->admin)->post(route('admin.orders.ship', $order), [
            'courier' => 'JNE',
            'tracking_number' => 'JNE123456',
        ])->assertRedirect();
        $this->assertSame(Order::STATUS_SHIPPED, $order->fresh()->status);

        $this->actingAs($this->customer)->get(route('orders.index'))->assertOk()->assertSee('JNE123456')->assertSee('Pesanan Diterima');

        $this->actingAs($this->customer)->post(route('orders.received', $order))->assertRedirect();
        $this->assertSame(Order::STATUS_COMPLETED, $order->fresh()->status);
    }

    public function test_ship_requires_tracking_and_paid_status(): void
    {
        $pending = $this->makeOrder();

        $this->actingAs($this->admin)->post(route('admin.orders.ship', $pending), ['courier' => 'JNE'])
            ->assertSessionHasErrors('tracking_number');

        $this->actingAs($this->admin)->post(route('admin.orders.ship', $pending), ['courier' => 'JNE', 'tracking_number' => 'X1'])
            ->assertSessionHas('error');
        $this->assertSame(Order::STATUS_PENDING, $pending->fresh()->status);
    }

    public function test_admin_cancel_paid_order_restores_stock(): void
    {
        $order = $this->makeOrder();
        $this->sendCallback($order, 'settlement');
        $this->assertSame(1, $this->product->fresh()->stock);

        $this->actingAs($this->admin)->post(route('admin.orders.cancel', $order), ['reason' => 'Unit cacat'])->assertRedirect();

        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(2, $this->product->fresh()->stock);
    }

    public function test_expired_payment_cancels_order(): void
    {
        $order = $this->makeOrder();
        $this->sendCallback($order, 'expire')->assertOk();
        $this->assertSame(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertSame(2, $this->product->fresh()->stock);
    }

    public function test_access_control(): void
    {
        $order = $this->makeOrder(Order::STATUS_PAID);
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($this->customer)->get(route('admin.orders.index'))->assertForbidden();
        $this->actingAs($this->customer)->post(route('admin.orders.ship', $order), ['courier' => 'A', 'tracking_number' => 'B'])->assertForbidden();
        $this->actingAs($otherCustomer)->post(route('orders.received', $order))->assertForbidden();
        $this->actingAs($otherCustomer)->post(route('orders.cancel', $order))->assertForbidden();
    }

    public function test_admin_sidebar_differs_from_customer_view(): void
    {
        $this->makeOrder(Order::STATUS_PAID);

        $this->actingAs($this->admin)->get(route('admin.orders.index'))
            ->assertSee('MODE ADMIN')
            ->assertSee('Perlu Diproses')
            ->assertDontSee('Konsultasi WA CS');

        $this->actingAs($this->admin)->get(route('home'))
            ->assertSee('Masuk Panel Admin')
            ->assertDontSee('MODE ADMIN');

        $this->actingAs($this->customer)->get(route('home'))
            ->assertDontSee('Masuk Panel Admin');
    }

    public function test_existing_admin_product_pages_still_render(): void
    {
        $this->actingAs($this->admin)->get(route('admin.products.index'))->assertOk()->assertSee('MODE ADMIN');
        $this->actingAs($this->admin)->get(route('admin.products.create'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.products.edit', $this->product))->assertOk();
    }
}
