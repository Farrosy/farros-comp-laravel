<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->after('snap_token');
            $table->string('courier')->nullable()->after('payment_type');
            $table->string('tracking_number')->nullable()->after('courier');
            $table->text('admin_note')->nullable()->after('tracking_number');
            $table->timestamp('paid_at')->nullable()->after('admin_note');
            $table->timestamp('shipped_at')->nullable()->after('paid_at');
            $table->timestamp('completed_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_type',
                'courier',
                'tracking_number',
                'admin_note',
                'paid_at',
                'shipped_at',
                'completed_at',
                'cancelled_at',
            ]);
        });
    }
};
