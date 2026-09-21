<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubCategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('categories')->get();
        return view('admin.subcategories.index', compact('subcategories'));
    }

    public function create()
    {
        $categories = Category::all(); // Get all categories to use in the form dropdown
        return view('admin.subcategories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories,slug',
            'category_id' => 'required|exists:categories,id',
        ]);

        SubCategory::create($validatedData);

        return redirect()->route('subcategory.list')->with('success', 'SubCategory added successfully');
    }

    public function edit($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $categories = Category::all();
        return view('admin.subcategories.edit', compact('subcategory', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:sub_categories,slug,' . $id,
            'category_id' => 'required|exists:categories,id',
        ]);

        $subcategory = SubCategory::findOrFail($id);
        $subcategory->update($validatedData);

        return redirect()->route('subcategory.list')->with('success', 'SubCategory updated successfully');
    }

    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $subcategory->delete();

        return redirect()->route('subcategory.list')->with('success', 'SubCategory deleted successfully');
    }
}
