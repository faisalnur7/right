<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;
use App\Models\PackageFeature;
use Illuminate\Http\Request;

class SubscriptionPackageController extends Controller
{
    // Display all subscription packages
    public function index()
    {
        $subscriptionPackages = SubscriptionPackage::all();
        return view('admin.subscription_packages.index', compact('subscriptionPackages'));
    }

    // Show form for creating a new subscription package
    public function create()
    {
        $data['features'] = PackageFeature::orderBy('order')->get();
        return view('admin.subscription_packages.create', $data);
    }

    // Store a new subscription package in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer',
            'amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'features' => 'nullable|array',
            'features.*' => 'exists:package_features,id',
        ]);

        $package = SubscriptionPackage::create($request->all());
    
        if ($request->filled('features')) {
            $package->features()->sync($request->features);
        }

        return redirect()->route('subscription_package.list')->with('success', 'Subscription Package created successfully!');
    }

    // Show form for editing a subscription package
    public function edit($id)
    {
        $data['subscriptionPackage'] = SubscriptionPackage::findOrFail($id);
        $data['features'] = PackageFeature::orderBy('order')->get();
        return view('admin.subscription_packages.edit', $data);
    }

    // Update a subscription package
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer',
            'amount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'features' => 'nullable|array',
            'features.*' => 'exists:package_features,id',
        ]);
    
        $package = SubscriptionPackage::findOrFail($id);
        $package->update($request->all());
        $package->features()->sync($request->filled('features') ? $request->features : []);

        return redirect()->route('subscription_package.list')->with('success', 'Subscription Package updated successfully!');
    }

    // Delete a subscription package
    public function destroy($id)
    {
        $subscription = SubscriptionPackage::findOrFail($id);
        $subscription->delete();

        return redirect()->route('subscription_package.list')->with('success', 'Subscription Package deleted successfully!');
    }
}
