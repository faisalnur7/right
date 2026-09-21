<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|max:255',
            'contact_person' => 'required|max:255',
            'mobile_number' => 'required|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $data = $request->only('company_name', 'contact_person', 'mobile_number', 'email', 'address');

        // Image Upload
        if ($request->hasFile('image')) {
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/suppliers'), $filename);
            $data['image'] = 'uploads/suppliers/' . $filename;
        }

        Supplier::create($data);

        return redirect()->route('supplier.list')->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'company_name' => 'required|max:255',
            'contact_person' => 'required|max:255',
            'mobile_number' => 'required|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($supplier->image && file_exists(public_path($supplier->image))) {
                unlink(public_path($supplier->image));
            }
    
            $filename = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('uploads/suppliers'), $filename);
            $data['image'] = 'uploads/suppliers/' . $filename;
        }

        $supplier->update($data);

        return redirect()->route('supplier.list')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->image && \Storage::disk('public')->exists($supplier->image)) {
            \Storage::disk('public')->delete($supplier->image);
        }

        $supplier->delete();
        return redirect()->route('supplier.list')->with('success', 'Supplier deleted successfully.');
    }
}
