<?php

namespace App\Http\Controllers;

use App\Models\ShippingRule;
use Illuminate\Http\Request;

class ShippingRuleController extends Controller
{
    public function index()
    {
        $rules = ShippingRule::latest()->get();
        return view('admin.shipping_rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.shipping_rules.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'max_subtotal' => 'required|numeric|min:0',
            'min_subtotal' => 'required|numeric|min:0',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        ShippingRule::create($request->all());

        return redirect()->route('shipping-rule.index')->with('success', 'Shipping Rule created successfully.');
    }

    public function edit($id)
    {
        $shipping_rule = ShippingRule::findOrFail($id);
        return view('admin.shipping_rules.edit', compact('shipping_rule'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'max_subtotal' => 'required|numeric|min:0',
            'min_subtotal' => 'required|numeric|min:0',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $shipping_rule = ShippingRule::findOrFail($id);

        $shipping_rule->update($request->all());

        return redirect()->route('shipping-rule.index')->with('success', 'Shipping Rule updated successfully.');
    }

    public function destroy($id)
    {
        $shipping_rule = ShippingRule::findOrFail($id);
        $shipping_rule->delete();
        return redirect()->route('shipping-rule.index')->with('success', 'Shipping Rule deleted successfully.');
    }
}
