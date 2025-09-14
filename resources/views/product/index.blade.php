@extends('layouts.admin')
@section('page-title')
    {{ __('Products') }}
@endsection
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ __('Products') }}</li>
@endsection
@section('action-btn')
@php
    $user = \Auth::user()->currentuser();
@endphp
<div class="pr-2">
    <a class="btn btn-sm btn-icon  bg-light-secondary me-2" href="{{ route('product.export') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Export') }}"> 
        <i  data-feather="download"></i>
    </a>
    @can('Create Products')
        <a href="#!" class="btn btn-sm btn-icon  bg-primary text-white me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Import') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Import Product CSV File') }}" data-url="{{ route('product.file.import') }}">
            <i  data-feather="upload"></i>
        </a>
    @endcan

    <a class="btn btn-sm btn-icon  bg-primary text-white me-2" href="{{ route('product.grid') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Grid View') }}">
        <i  class="ti ti-grid-dots f-30"></i>
    </a>
    @can('Create Products')
        @if (count($user->stores) > 1)
        <div class="dropdown dash-h-item" style="display: inline-block">
            <a class="btn btn-sm btn-icon btn-primary me-2 dash-head-link dropdown-toggle arrow-none me-0 cust-btn" data-bs-toggle="dropdown" href="" role="button" aria-haspopup="false" aria-expanded="false" title="{{ __('Create') }}">
                <i  data-feather="plus"></i>
            </a>
            <ul class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                <li> <a href="{{ route('product.create') }}" class="dropdown-item"> {{__('Add new product')}} </a></li>
                <li> 
                    <a href="#!" class="dropdown-item" data-ajax-popup="true" data-size="lg" data-url="{{ route('copy-products.modal') }}" data-title="{{ __('Copy from my other stores') }}"> {{__('Copy from my other stores')}} </a>
                </li>
            </ul>
        </div>
        @else
        <a class="btn btn-sm btn-icon  btn-primary me-2" href="{{ route('product.create') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}">
            <i  data-feather="plus"></i>
        </a>
        @endif
    @endcan

</div>
@endsection
@php
    $logo=\App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
@section('filter')
@endsection
@push('css-page')
    <link rel="stylesheet" href="{{ asset('custom/libs/summernote/summernote-bs4.css') }}">
@endpush
@push('script-page')
    <script src="{{ asset('custom/libs/summernote/summernote-bs4.js') }}"></script>
@endpush
@section('content')
<div class="row">
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pb-0 table-border-style">
                <div class="table-responsive">
                    <table class="table dataTable" id="pc-dt-satetime-sorting">
                        <thead>
                            <tr>
                                <th>{{ __('Products') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Quantity') }}</th>
                                <th>{{ __('Stock') }}</th>
                                <th>{{ __('Created at') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if (!empty($product->is_cover))
                                                <img src="{{$logo.(isset($product->is_cover) && !empty($product->is_cover)?$product->is_cover:'default.jpg')}}" alt="" class="theme-avtar border border-2 border-primary rounded">
                                            @else
                                                <img src="{{$logo.(isset($product->is_cover) && !empty($product->is_cover)?$product->is_cover:'default.jpg')}}" alt="" class="theme-avtar border border-2 border-primary rounded">
                                            @endif
                                            <div class="ms-3">
                                                <a href="{{ route('product.show', $product->id) }}" class="text-dark f-w-700">{{ $product->name }}</a>
                                                <div class="mt-2 d-flex align-items-center">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        @php
                                                            $icon = 'fa-star';
                                                            $color = '';
                                                            $newVal1 = $i - 0.5;
                                                            if ($product->product_rating() < $i && $product->product_rating() >= $newVal1) {
                                                                $icon = 'fa-star-half-alt';
                                                            }
                                                            if ($product->product_rating() >= $newVal1) {
                                                                $color = 'text-success';
                                                            }
                                                        @endphp
                                                        <i class="fa fa-solid  {{ $icon . ' ' . (!empty($color) ? $color : 'text-secondary') }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ !empty($product->product_category()) ? $product->product_category() : '-' }}</td>
                                    <td>
                                        @if ($product->enable_product_variant == 'on')
                                            {{ __('In Variant') }}
                                        @else
                                            <!-- {{ \App\Models\Utility::priceFormat($product->price) }} -->
                                            <div class="d-flex align-items-center" style="gap: 5px">
                                                <input type="number" class="form-control editable-price" data-id="{{ $product->id }}" value="{{ $product->price }}" min="0" style="max-width: 85px; min-width: 50px"> 
                                                <span> {{ $store->currency }} </span>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($product->enable_product_variant == 'on')
                                            {{ __('In Variant') }}
                                        @else
                                            <!-- {{ $product->quantity }} -->
                                            <input type="number" class="form-control editable-quantity" data-id="{{ $product->id }}" value="{{ $product->quantity }}" min="0" style="max-width: 85px">
                                        @endif
                                    </td>
                                    <td class="product-stock" data-id="{{ $product->id }}">
                                        @if ($product->enable_product_variant == 'on')
                                        <span class="badge tbl-btn-w p-2 f-w-600 common-lbl-radius border border-1 border-primary bg-light-primary">{{ __('In Variant') }}</span>
                                        @else
                                            @if ($product->quantity == 0)
                                                <span class="badge tbl-btn-w p-2 f-w-600 common-lbl-radius border border-1 border-danger bg-light-danger">  {{ __('Out of stock') }}</span>
                                            @else
                                                <span class="badge tbl-btn-w p-2 f-w-600 common-lbl-radius border border-1 border-primary bg-light-primary"> {{ __('In stock') }}</span>
                                            @endif
                                        @endif

                                    </td>
                                    <td>
                                        {{ \App\Models\Utility::dateFormat($product->created_at) }}
                                    </td>
                                    <td>
                                        <div class="d-flex action-btn-wrapper">
                                            @can('Show Products')
                                                <a href="{{ route('product.show', $product->id) }}" class="btn btn-sm btn-icon  bg-warning text-white me-2" data-toggle="tooltip" data-original-title="{{ __('View') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('View') }}" data-tooltip="View">
                                                    <i  class="ti ti-eye f-20"></i>
                                                </a>
                                            @endcan
                                            @can('Edit Products')
                                                <a class="btn btn-sm btn-icon  bg-info text-white me-2" href="{{ route('product.edit', $product->id) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
                                                    <i  class=" ti ti-pencil f-20"></i>
                                                </a>
                                            @endcan
                                            @can('Delete Products')
                                                <a class="bs-pass-para btn btn-sm btn-icon bg-danger text-white" href="#"
                                                    data-title="{{ __('Delete Lead') }}"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $product->id }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash f-20"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id], 'id' => 'delete-form-' . $product->id]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
@endsection
@push('script-page')
    <script>
        $(document).on('click', '#billing_data', function() {
            $("[name='shipping_address']").val($("[name='billing_address']").val());
            $("[name='shipping_city']").val($("[name='billing_city']").val());
            $("[name='shipping_state']").val($("[name='billing_state']").val());
            $("[name='shipping_country']").val($("[name='billing_country']").val());
            $("[name='shipping_postalcode']").val($("[name='billing_postalcode']").val());
        });

        // Track changes in product quantity
        $(document).on('change', '.editable-quantity', function() {
            const quantity = $(this).val();
            const productId = $(this).data('id');

            $.ajax({
                url: '{{ route("product.updateQuantity") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: productId,
                    quantity: quantity
                },
                success: function(data) {
                    if (data.flag == "success") {
                        show_toastr('success', data.msg, 'success');

                        // Find the row for this product
                        var row = $('.editable-quantity[data-id="'+ productId +'"]').closest('tr');
                        // Determine the new stock badge based on quantity
                        var stockHtml = '';
                        if (quantity == 0) {
                            stockHtml = '<span class="badge rounded p-2 f-w-600 bg-light-danger">{{ __("Out of stock") }}</span>';
                        } else {
                            stockHtml = '<span class="badge rounded p-2 f-w-600 bg-light-primary">{{ __("In stock") }}</span>';
                        }
                        // Update the stock cell
                        row.find('.product-stock').html(stockHtml);
                    } else {
                        show_toastr('Error', data.msg, 'error');
                        // Refresh page after showing error for 3 seconds
                        setTimeout(function(){
                            window.location.reload();
                        }, 1500);
                    }
                },
                error: function(data) {
                    if (data.error) {
                        show_toastr('Error', data.error, 'error');
                    } else {
                        show_toastr('Error', data, 'error');
                    }
                },
            })
        });

        // Track changes in product price
        $(document).on('change', '.editable-price', function() {
            const price = $(this).val();
            const productId = $(this).data('id');

            $.ajax({
                url: '{{ route("product.updatePrice") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    id: productId,
                    price: price
                },
                success: function(data) {
                    if (data.flag == "success") {
                        show_toastr('success', data.msg, 'success');
                    } else {
                        show_toastr('Error', data.msg, 'error');
                        // Refresh page after showing error for 3 seconds
                        setTimeout(function(){
                            window.location.reload();
                        }, 1500);
                    }
                },
                error: function(data) {
                    if (data.error) {
                        show_toastr('Error', data.error, 'error');
                    } else {
                        show_toastr('Error', data, 'error');
                    }
                },
            })
        });
    </script>

    <!-- Logic for display products in modal of copy products -->
    <script>
        var productImagePath = "{{ $logo }}";
        document.addEventListener('DOMContentLoaded', function() {
            $('body').on('shown.bs.modal', '#commonModal', function () {
                var storeSelector = document.getElementById("store-selector");
                var productList = document.getElementById("product-list");
                var selectAllCheckboxContainer = document.getElementById('select-all-products');
                var selectAllCheckbox = document.querySelector('#select-all-products input[type="checkbox"]');
                var allProducts = @json($storesWithProducts);

                // Function to update product list
                function updateProductList(storeId) {
                    productList.innerHTML = ""; // Clear existing products
                    let selectedStore = allProducts.find(store => store.id == storeId);

                    if (selectedStore && selectedStore.products.length > 0) {
                        selectedStore.products.forEach(product => {
                            let imagePath = product.is_cover ? `${productImagePath}${product.is_cover}` : 'default-image.jpg';
                            selectAllCheckbox.checked = false; // Reset checkbox state
                            let productHtml = `
                                <div class="d-flex justify-content-between align-items-center" style="padding: 10px 0;">
                                    <div class="d-flex align-items-center" style="gap: 10px">
                                         <img src="${imagePath}" alt="img" class="theme-avtar">
                                        <span>${product.name}</span>
                                    </div>
                                    <input type="checkbox" class="form-check-input product-checkbox" name="selected_products[]" value="${product.id}">
                                </div>`;
                            
                            productList.innerHTML += productHtml;
                            selectAllCheckboxContainer.style.display = "flex";
                        });
                    } else {
                        productList.innerHTML = `<p class="text-center text-muted">{{ __('No products available for this store') }}</p>`;
                        selectAllCheckboxContainer.style.display = "none";
                    }
                }

                // Trigger update on store selection change
                storeSelector.addEventListener('change', function() {
                    updateProductList(this.value);
                });

                // Trigger choosing "Select All"
                selectAllCheckbox.addEventListener('change', function() {
                    let productCheckboxes = document.querySelectorAll(".product-checkbox");
                    productCheckboxes.forEach(checkbox => {
                        checkbox.checked = selectAllCheckbox.checked;
                    });
                });
            })
        })
    </script>
@endpush
