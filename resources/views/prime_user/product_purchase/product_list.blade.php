@extends('layouts.master')

@section('title', 'Products')
@section('page_title', 'Products')

@section('head')
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        @media (min-width: 768px) {
            .sidebar {
                width: 250px;
                transition: all 0.3s ease;
            }
        }

        .nav-tabs{
            border: none;
            background-color: #fff;
            padding:0;
            margin:0;
            display:flex;
        }

        .nav-tabs .nav-link {
            font-weight: bold;
        }
        .nav-tabs .nav-link:hover {
            box-shadow:none;
            border:none;
            border-radius: 4px;
        }
        .nav-tabs .nav-link.active {
            background-color: #fff;
            color: #252F51;
            border-radius: 4px;
            border: none;
            font-weight: bold;
            box-shadow:inset 0 0 4px #ccc;
        }
    </style>
@endsection

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-body px-0 pb-4 pt-0">
                <div class="flex flex-col md:flex-row gap-6">
                    <!-- Sidebar -->
                    <div id="filterSidebar" class="sidebar bg-[gray-50] p-4 rounded-xl shadow-sm  border-[#252f51] sm:mt-0 md:mt-24 mb-24 ml-6 relative md:sticky top-20">
                        <h4 class="text-lg font-semibold mb-3">Filter</h4>

                            <!-- Brands -->
                            <div class="mb-4">
                                <h3 class="font-bold text-[18px] mb-2">Brands</h3>
                                <hr>
                                @foreach ($brands as $brand)
                                    <div class="form-check my-3">
                                        <input class="form-check-input" type="checkbox" name="brands[]"
                                            value="{{ $brand->id }}" id="brand-{{ $brand->id }}"
                                            {{ in_array($brand->id, request()->get('brands', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="brand-{{ $brand->id }}">
                                            {{ $brand->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Categories -->
                            <div class="mb-4">
                                <h3 class="font-bold text-[18px] mb-2">Categories</h3>
                                <hr>
                                @foreach ($categories as $category)
                                    <div class="form-check my-3">
                                        <input class="form-check-input" type="checkbox" name="categories[]"
                                            value="{{ $category->id }}" id="category-{{ $category->id }}"
                                            {{ in_array($category->id, request()->get('categories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="category-{{ $category->id }}">
                                            {{ $category->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Subcategories -->
                            <div class="mb-4">
                                <h3 class="font-bold text-[18px] mb-2">Subcategories</h3>
                                <hr>
                                @foreach ($subcategories as $subcategory)
                                    <div class="form-check my-3">
                                        <input class="form-check-input" type="checkbox" name="subcategories[]"
                                            value="{{ $subcategory->id }}" id="subcategory-{{ $subcategory->id }}"
                                            {{ in_array($subcategory->id, request()->get('subcategories', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="subcategory-{{ $subcategory->id }}">
                                            {{ $subcategory->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                    </div>
                    <div class="gap-6 px-4 py-2 mt-6 w-full product_list">
                        @include('prime_user.product_purchase.product_view')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            function getCheckedValues(name) {
                let values = [];
                $(`input[name="${name}[]"]:checked`).each(function() {
                    values.push($(this).val());
                });
                return values;
            }

            function loadProducts() {
                $.ajax({
                    url: "{{ route('products.filter') }}",
                    method: "GET",
                    data: {
                        brand_ids: getCheckedValues('brands'),
                        category_ids: getCheckedValues('categories'),
                        subcategory_ids: getCheckedValues('subcategories'),
                    },
                    beforeSend: function() {
                        $('.product_list').html(`
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 animate-pulse">
                    ${Array(8).fill(`
                            <div class="card bg-white shadow-xl">
                                <figure class="px-4 pt-10 flex justify-center">
                                    <div class="bg-gray-300 rounded-xl w-full h-40"></div>
                                </figure>
                                <div class="card-body flex flex-col items-center text-center">
                                    <div class="h-4 bg-gray-300 rounded w-3/4 mb-4"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/2 mb-2"></div>
                                    <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
                                    <div class="card-actions mt-3 flex gap-2">
                                        <div class="bg-gray-300 rounded w-24 h-8"></div>
                                        <div class="bg-gray-300 rounded w-24 h-8"></div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                </div>
            `);
                    },
                    success: function(response) {
                        setTimeout(function() {
                            $('.product_list').html(response.html);
                        }, 500);
                    },
                    error: function() {
                        $('.product_list').html('<p class="text-red-500">Error loading products.</p>');
                    }
                });
            }


            // Event binding
            $('input[type=checkbox]').on('change', function() {
                loadProducts();
            });

            // Initial load (optional)
            // loadProducts();


        });

        

    </script>

@endsection
