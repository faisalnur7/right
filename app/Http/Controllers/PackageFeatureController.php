<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PackageFeature;

class PackageFeatureController extends Controller
{
    public function index()
    {
        $packageFeatures = PackageFeature::orderBy('order')->get();
        return view('admin.package_feature.index', compact('packageFeatures'));
    }

    public function create()
    {
        return view('admin.package_feature.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        PackageFeature::create($request->only('name', 'order'));

        return redirect()->route('package_feature.list')->with('success', 'Feature created');
    }

    public function edit($id)
    {
        $packageFeature = PackageFeature::findOrFail($id);
        return view('admin.package_feature.edit', compact('packageFeature'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order' => 'required|integer',
        ]);

        $feature = PackageFeature::findOrFail($id);
        $feature->update($request->only('name', 'order'));

        return redirect()->route('package_feature.list')->with('success', 'Feature updated');
    }

    public function destroy($id)
    {
        PackageFeature::destroy($id);
        return redirect()->route('package_feature.list')->with('success', 'Feature deleted');
    }
}
