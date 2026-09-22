<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentOption;

class PaymentOptionController extends Controller
{
    public function index()
    {
        $paymentOptions = PaymentOption::orderBy('order')->get();
        return view('admin.payment_options.index', compact('paymentOptions'));
    }

    public function create()
    {
        return view('admin.payment_options.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
            'zoomable' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $data = $request->only(['name', 'order', 'account_number']);
        $data['zoomable'] = (int) $request->boolean('zoomable');

        // Image Upload (as per your instruction)
        if ($request->hasFile('logo')) {
            $filename = time() . '_' . $request->file('logo')->getClientOriginalName();
            $request->file('logo')->move(public_path('uploads/payment_options'), $filename);
            $data['logo'] = 'uploads/payment_options/' . $filename;
        }

        PaymentOption::create($data);

        return redirect()->route('payment_option.list')->with('success', 'Payment option added successfully.');
    }


    public function edit($id)
    {
        $paymentOption = PaymentOption::findOrFail($id);
        return view('admin.payment_options.edit', compact('paymentOption'));
    }

    public function update(Request $request, $id)
    {
        $paymentOption = PaymentOption::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
            'zoomable' => 'required|boolean',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
    
        $data = $request->only(['name', 'order', 'account_number']);
        $data['zoomable'] = (int) $request->boolean('zoomable');
    
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($paymentOption->logo && file_exists(public_path($paymentOption->logo))) {
                unlink(public_path($paymentOption->logo));
            }
    
            $filename = time() . '_' . $request->file('logo')->getClientOriginalName();
            $request->file('logo')->move(public_path('uploads/payment_options'), $filename);
            $data['logo'] = 'uploads/payment_options/' . $filename;
        }
    
        $paymentOption->update($data);
    
        return redirect()->route('payment_option.list')->with('success', 'Payment option updated successfully.');
    }
    

    public function destroy($id)
    {
        $paymentOption = PaymentOption::findOrFail($id);

        if ($paymentOption->logo && Storage::disk('public')->exists($paymentOption->logo)) {
            Storage::disk('public')->delete($paymentOption->logo);
        }

        $paymentOption->delete();

        return redirect()->route('payment_option.list')->with('success', 'Payment option deleted.');
    }
}
