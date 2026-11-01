<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('processor', 'like', "%{$search}%")
                    ->orWhere('storage', 'like', "%{$search}%")
                    ->orWhere('physical_condition', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order') === 'asc' ? 'asc' : 'desc';

        if ($sortBy === 'category') {
            $query->join('categories', 'products.category_id', '=', 'categories.id')
                ->orderBy('categories.name', $sortOrder)
                ->select('products.*');
        } elseif (in_array($sortBy, ['name', 'price', 'stock', 'physical_condition', 'created_at'], true)) {
            $query->orderBy('products.'.$sortBy, $sortOrder);
        } else {
            $query->latest('products.created_at');
        }

        $products = $query->withCount('orderItems')->paginate(5)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function show(Product $product): View
    {
        $product->load('category');

        return view('admin.products.show', compact('product'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', compact('categories'));
    }

    public function quickStoreCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);

        $baseSlug = substr(Str::slug($validated['name']) ?: 'kategori', 0, 240);
        $slug = $baseSlug;
        $suffix = 2;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        $category = Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $category,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'price' => preg_replace('/[^0-9]/', '', (string) $request->input('price', '')),
        ]);

        $validated = $this->validatedProductData($request);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Unit laptop baru berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::orderBy('name')->get();
        [$storageCapacity, $storageType] = $this->splitStorage($product->storage);

        return view('admin.products.edit', compact('product', 'categories', 'storageCapacity', 'storageType'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->merge([
            'price' => preg_replace('/[^0-9]/', '', (string) $request->input('price', '')),
        ]);

        $validated = $this->validatedProductData($request);
        $previousImage = $product->image;

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        if ($request->hasFile('image') && $previousImage) {
            Storage::disk('public')->delete($previousImage);
        }

        return redirect()->route('admin.products.index')->with('success', 'Data unit laptop berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Produk yang sudah tercatat dalam pesanan tidak dapat dihapus. Anda dapat mengubah stoknya menjadi 0.');
        }

        $image = $product->image;
        $product->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

        return redirect()->route('admin.products.index')->with('success', 'Unit laptop berhasil dihapus dari sistem.');
    }

    private function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'processor' => ['required', 'string', 'max:255'],
            'ram_gb' => ['required', 'integer', 'min:1', 'max:128'],
            'storage' => ['nullable', 'required_without_all:storage_capacity,storage_type', 'string', 'max:255'],
            'storage_capacity' => ['nullable', 'required_with:storage_type', 'required_without:storage', 'string', 'max:50'],
            'storage_type' => ['nullable', 'required_with:storage_capacity', 'required_without:storage', 'string', 'max:100'],
            'battery_condition' => ['required', 'string', 'max:255'],
            'physical_condition' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:100000', 'max:9999999999'],
            'stock' => ['required', 'integer', 'min:0', 'max:2147483647'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,jpg,webp', 'image', 'max:2048'],
            'description' => ['nullable', 'string', 'max:10000'],
        ];
    }

    private function validatedProductData(Request $request): array
    {
        $validated = $request->validate($this->rules(), [
            'image.file' => 'File yang dipilih harus berupa file gambar.',
            'image.image' => 'File yang dipilih harus berupa file gambar.',
            'image.mimes' => 'Format file gambar hanya boleh JPG, JPEG, PNG, atau WEBP (PDF tidak diperbolehkan).',
            'image.max' => 'Ukuran file gambar maksimal 2MB.',
        ]);

        if (isset($validated['storage_capacity'], $validated['storage_type'])) {
            $validated['storage'] = trim($validated['storage_capacity'].' '.$validated['storage_type']);
        }

        unset($validated['storage_capacity'], $validated['storage_type']);
        $validated['price'] = (int) $validated['price'];

        return $validated;
    }

    private function splitStorage(string $storage): array
    {
        $parts = preg_split('/\s+/', trim($storage), 2);

        return [
            $parts[0] ?? '',
            $parts[1] ?? '',
        ];
    }
}
