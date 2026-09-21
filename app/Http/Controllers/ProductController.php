<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\SaleLog;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\NotificationService;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'subCategory', 'brand', 'saleLogs']) // Eager load relations
            ->withSum('purchaseItems as total_purchased', 'quantity')
            ->withSum('orderItems as total_sold', 'quantity')
            // Filtering Sale Log by ID
            ->when($request->has('sale_log_id') && $request->sale_log_id != '', function ($query) use ($request) {
                return $query->whereHas('saleLogs', function ($q) use ($request) {
                    // Filter products that have a sale log with the given ID
                    $q->where('sale_logs.id', $request->sale_log_id);
                });
            })
            // Filtering SKU
            ->when($request->has('sku') && $request->sku != '', function ($query) use ($request) {
                return $query->where('sku', 'like', '%' . $request->sku . '%');
            })
            // Filtering Product Name
            ->when($request->has('name') && $request->name != '', function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->name . '%');
            })
            // Filtering Price Range
            ->when($request->has('price_min') && $request->price_min != '', function ($query) use ($request) {
                return $query->where('price', '>=', $request->price_min);
            })
            ->when($request->has('price_max') && $request->price_max != '', function ($query) use ($request) {
                return $query->where('price', '<=', $request->price_max);
            })
            // Filtering by Category
            ->when($request->has('category') && $request->category != '', function ($query) use ($request) {
                return $query->where('category_id', $request->category);
            })
            // Filtering by Sub Category
            ->when($request->has('sub_category') && $request->sub_category != '', function ($query) use ($request) {
                return $query->where('sub_category_id', $request->sub_category);
            })
            // Filtering by Brand
            ->when($request->has('brand') && $request->brand != '', function ($query) use ($request) {
                return $query->where('brand_id', $request->brand);
            })
            // Paginate the results
            ->paginate(10);

        // Get all categories, subcategories, and brands for filter dropdowns
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $brands = Brand::all();
        $saleLogs = SaleLog::all();

        foreach ($products as $product) {
            $purchased = $product->total_purchased ?? 0;
            $sold = $product->total_sold ?? 0;
            $product->calculated_stock = $purchased - $sold;
        }


        // Return the view with the filtered products and filter options
        return view('admin.products.index', compact('products', 'categories', 'subCategories', 'brands','saleLogs'));
    }

    public function create()
    {
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        $saleLogs = SaleLog::all();
        return view('admin.products.create', compact('categories', 'subcategories','brands','saleLogs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|unique:products,slug',
            'price' => 'required|numeric',
            'image' => 'nullable|image',
            'gallery_images.*' => 'nullable|image',
        ]);
    
        $data = $request->all();
    
        // Slug auto-generate if not provided
        $data['slug'] = $request->slug ?: Str::slug($request->name) . '-' . uniqid();

        $data['sku'] = $request->category_id 
              . ($request->sub_category_id ?? '0') 
              . ($request->brand_id ?? '0');
        // Image Upload
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $filename);
            $data['image'] = 'uploads/products/' . $filename;
        }
    
        // Gallery Images Upload
        if ($request->hasFile('gallery_images')) {
            $gallery = [];
            foreach ($request->file('gallery_images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products/gallery'), $filename);
                $gallery[] = 'uploads/products/gallery/' . $filename;
            }
            $data['gallery_images'] = json_encode($gallery);
        }
    
        // Create Product First
        $product = Product::create($data);

        $product->sku = $product->category_id 
              . ($product->sub_category_id ?? '0') 
              . ($product->brand_id ?? '0') 
              . '.' 
              . $product->id;

        $product->save();
    
        // Attach Sale Logs (if any selected)
        if ($request->has('sale_logs')) {
            $syncData = [];
    
            foreach ($request->sale_logs as $saleLogId => $logData) {
                if (isset($logData['selected'])) {  // Checkbox was ticked
                    $syncData[$saleLogId] = [
                        'unit' => $logData['unit'] ?? null,
                        'price' => $logData['price'] ?? null,
                    ];
                }
            }
    
            $product->saleLogs()->sync($syncData);
        }

        $notificationData = [
                'product_name' => $product->name,
                'product_id' => $product->id,
            ];
        $notificationService = new NotificationService();    
        $notificationService->create('product_added', null, $notificationData, 0);
    
        return redirect()->route('product.list')->with('success', 'Product created successfully.');
    }
    

    public function edit($id)
    {
        $product = Product::with(['category', 'subCategory', 'brand', 'saleLogs'])->findOrFail($id);
        $categories = Category::all();
        $subcategories = SubCategory::all();
        $brands = Brand::all();
        $saleLogs = SaleLog::all();
    
        // Decode sale logs from the product if stored as JSON, adjust according to your DB structure
        $selectedSaleLogs = [];
        if ($product->sale_logs) {
            $selectedSaleLogs = json_decode($product->sale_logs, true);
        }
    
        return view('admin.products.edit', compact(
            'product',
            'categories',
            'subcategories',
            'brands',
            'saleLogs',
            'selectedSaleLogs'
        ));
    }
    

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
    
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'slug' => 'nullable|unique:products,slug,' . $product->id,
            // 'sku' => 'required|unique:products,sku,' . $product->id,
            'price' => 'required|numeric',
            'image' => 'nullable|image',
            'gallery_images.*' => 'nullable|image',
        ]);
    
        $data = $request->all();
    
        $data['slug'] = $request->slug ?: Str::slug($request->name) . '-' . uniqid();
        $data['sku'] = $product->slug;

        // Replace Cover Image
        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/products'), $filename);
            $data['image'] = 'uploads/products/' . $filename;
        }
    
        // Replace Gallery Images
        if ($request->hasFile('gallery_images')) {
            if ($product->gallery_images) {
                foreach (json_decode($product->gallery_images) as $oldImage) {
                    if (file_exists(public_path($oldImage))) {
                        unlink(public_path($oldImage));
                    }
                }
            }
    
            $gallery = [];
            foreach ($request->file('gallery_images') as $image) {
                $filename = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('uploads/products/gallery'), $filename);
                $gallery[] = 'uploads/products/gallery/' . $filename;
            }
            $data['gallery_images'] = json_encode($gallery);
        }
    
        // Update Product
        $product->update($data);
    
        // Sync Sale Logs (if any selected)
        if ($request->has('sale_logs')) {
            $syncData = [];
    
            foreach ($request->sale_logs as $saleLogId => $logData) {
                if (isset($logData['selected'])) {  // Checkbox was ticked
                    $syncData[$saleLogId] = [
                        'unit' => $logData['unit'] ?? null,
                        'price' => $logData['price'] ?? null,
                    ];
                }
            }
    
            $product->saleLogs()->sync($syncData);
        } else {
            // If no sale_logs selected — detach all
            $product->saleLogs()->detach();
        }
    
        return redirect()->route('product.list')->with('success', 'Product updated successfully.');
    }
    

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
    
        // Delete main image
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
    
        // Delete gallery images
        if ($product->gallery_images) {
            foreach (json_decode($product->gallery_images) as $img) {
                if (file_exists(public_path($img))) {
                    unlink(public_path($img));
                }
            }
        }
    
        // Detach related sale logs from pivot table
        $product->saleLogs()->detach();
    
        // Delete the product
        $product->delete();
    
        return redirect()->route('product.list')->with('success', 'Product deleted successfully.');
    }

    public function getProducts(Request $request)
    {
        $query = Product::query();

        if ($request->has('category_id') && $request->category_id !== null) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('sub_category_id') && $request->sub_category_id !== null) {
            $query->where('sub_category_id', $request->sub_category_id);
        }
        if ($request->has('brand_id') && $request->brand_id !== null) {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->get();

        return response()->json([
            'products' => $products
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.show', compact('product'));
    }


    
}
