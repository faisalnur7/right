@extends('layouts.admin_master')

@section('title', 'Add New Shipping Rule')
@section('page_title', 'Add New Shipping Rule')


@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title">Edit Category</h3>
            </div>
            <div class="card-body">
                <!-- Form to edit category -->
                <form
                    action="{{ route('shipping-rule.update', $shipping_rule->id) }}"
                    method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="min_subtotal">Minimum Subtotal (Tk)</label>
                        <input type="number" name="min_subtotal" id="min_subtotal"
                            class="form-control @error('min_subtotal') is-invalid @enderror"
                            value="{{ old('min_subtotal', $shipping_rule->min_subtotal ?? '') }}" required>
                        @error('min_subtotal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="max_subtotal">Maximum Subtotal (Tk)</label>
                        <input type="number" name="max_subtotal" id="max_subtotal"
                            class="form-control @error('max_subtotal') is-invalid @enderror"
                            value="{{ old('max_subtotal', $shipping_rule->max_subtotal ?? '') }}" required>
                        @error('max_subtotal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="shipping_cost">Shipping Cost (Tk)</label>
                        <input type="number" name="shipping_cost" id="shipping_cost"
                            class="form-control @error('shipping_cost') is-invalid @enderror"
                            value="{{ old('shipping_cost', $shipping_rule->shipping_cost ?? '') }}" required>
                        @error('shipping_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-success">
                            {{ isset($shipping_rule) ? 'Update Rule' : 'Save Rule' }}
                        </button>
                        <a href="{{ route('shipping-rule.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>

            </div>
        </div>
    </div>


@endsection
