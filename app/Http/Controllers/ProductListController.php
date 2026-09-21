<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SaleLog;
use Illuminate\Http\Request;

class ProductListController extends Controller
{
    public function products(Request $request)
    {
        // Start the base SaleLog query with a conditional product relationship
        $saleLogQuery = SaleLog::whereHas('products', function ($query) use ($request) {
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('subcategory_id')) {
                $query->where('sub_category_id', $request->subcategory_id);
            }
        });

        // Apply the same filters to the eager-loaded products relationship
        $saleLogQuery->with(['products' => function ($query) use ($request) {
            if ($request->has('brand_id')) {
                $query->where('brand_id', $request->brand_id);
            }

            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->has('subcategory_id')) {
                $query->where('sub_category_id', $request->subcategory_id);
            }
        }]);

        $data['saleLogs'] = $saleLogQuery->get();
        $data['brands'] = Brand::all();
        $data['categories'] = Category::all();
        $data['subcategories'] = SubCategory::all();

        return view('prime_user.product_purchase.product_list', $data);
    }

    public function filterProducts(Request $request)
    {
        $saleLogQuery = SaleLog::whereHas('products', function ($query) use ($request) {
            if ($request->has('brand_ids')) {
                $query->whereIn('brand_id', $request->brand_ids);
            }

            if ($request->has('category_ids')) {
                $query->whereIn('category_id', $request->category_ids);
            }

            if ($request->has('subcategory_ids')) {
                $query->whereIn('sub_category_id', $request->subcategory_ids);
            }
        });

        // Apply the same filters to the eager-loaded products relationship
        $saleLogQuery->with(['products' => function ($query) use ($request) {
            if ($request->has('brand_ids')) {
                $query->whereIn('brand_id', $request->brand_ids);
            }

            if ($request->has('category_ids')) {
                $query->whereIn('category_id', $request->category_ids);
            }

            if ($request->has('subcategory_ids')) {
                $query->whereIn('sub_category_id', $request->subcategory_ids);
            }
        }]);

        $data['saleLogs'] = $saleLogQuery->get();

        return response()->json([
            'html' => view('prime_user.product_purchase.product_view', $data)->render(),
        ]);
    }



    public function product_details($id, $log_id){
        $data['product'] = Product::findOrFail($id);
        $data['log'] = SaleLog::findOrFail($log_id);
        return view('prime_user.product_purchase.product_details', $data);
    }
}
