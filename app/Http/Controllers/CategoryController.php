<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Show All Categories
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // Show Create Form
    public function create()
    {
        $categories = Category::all();  // Get all categories for the parent category dropdown
        return view('admin.categories.create', compact('categories'));
    }

    // Store New Category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('category.list')->with('success', 'Category created successfully.');
    }

    // Show Edit Form
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categories = Category::all(); // To populate the parent category dropdown
        return view('admin.categories.edit', compact('category', 'categories'));
    }


    // Update Category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('category.list')->with('success', 'Category updated successfully.');
    }

    // Delete Category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('category.list')->with('success', 'Category deleted successfully.');
    }
}
