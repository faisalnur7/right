<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Brand;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        // Fetch all suppliers
        $suppliers = Supplier::all();
    
        // Get the filtered purchases based on user input
        $query = Purchase::with(['supplier', 'adminUser'])->latest();
    
        // Apply filters if any
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }
    
        if ($request->filled('date_from')) {
            $query->where('purchase_date', '>=', $request->date_from);
        }
    
        if ($request->filled('date_to')) {
            $query->where('purchase_date', '<=', $request->date_to);
        }
    
        $purchases = $query->paginate(10);
    
        return view('admin.purchases.index', compact('purchases', 'suppliers'));
    }
    
    

    public function create()
    {
        // Fetch all necessary data for the form
        $suppliers = Supplier::all();
        $products = Product::all();
        $categories = Category::all();
        $subCategories = SubCategory::all();
        $brands = Brand::all();

        return view('admin.purchases.create', compact('suppliers', 'products', 'brands', 'categories', 'subCategories'));
    }

    public function store(Request $request)
    {

        // Validate the incoming request
        $request->validate([
            'supplier_id'    => 'required|exists:suppliers,id',
            'reference_no'   => 'nullable|string',
            'sub_total'      => 'required|numeric',
            'discount_value' => 'nullable|numeric',
            'total_amount'   => 'required|numeric',
            'invoice'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // Invoice file validation
            'products'          => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer',
            'products.*.unit_price' => 'required|numeric'
        ]);

        // Prepare data for purchase creation
        $data = $request->except('invoice');
        $data['admin_user_id'] = auth()->user()->id;
        $data['purchase_date'] = Carbon::now();

        // Handle invoice file upload
        if ($request->hasFile('invoice')) {
            $filename = time() . '_' . $request->file('invoice')->getClientOriginalName();
            $request->file('invoice')->move(public_path('uploads/invoices'), $filename);
            $data['invoice'] = 'uploads/invoices/' . $filename;
        }

        // Create the new purchase record
        $purchase = Purchase::create($data);

        // Insert purchase items into purchase_items table
        foreach ($request->input('products') as $item) {
            PurchaseItem::create([
                'purchase_id'  => $purchase->id,
                'product_id'   => $item['id'],
                'quantity'     => $item['quantity'],
                'unit_price'   => $item['unit_price'],
            ]);

            $product = Product::query()->findOrFail($item['id']);
            $product->purchase_price = $item['unit_price'];
            $product->price = $item['mrp'];
            $product->affiliate_price = $item['affiliate_price'];
            $product->save();
        }

        Transaction::create([
            'purchase_id'      => $purchase->id,
            'amount'           => $purchase->total_amount,
            'transaction_date' => $purchase->created_at,
            'type'             => 1
        ]);

        // Return a success response or redirect to a specific route
        return redirect()->route('purchase.list');
    }

    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'purchaseItems.product'])->findOrFail($id);

        return view('admin.purchases.show', compact('purchase'));
    }

}
