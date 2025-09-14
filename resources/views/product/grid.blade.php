@extends('layouts.admin')
@section('page-title')
    {{ __('Products') }}
@endsection
@section('title')
    <div class="d-inline-block">
        <h5 class="h4 d-inline-block text-white font-weight-bold mb-0 ">{{ __('Products') }}</h5>
    </div>
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Home') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ __('Products') }}</li>
@endsection
@section('action-btn')
    <a class="btn btn-sm btn-icon  bg-primary text-white me-2" href="{{ route('product.export') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Export') }}">
        <i  data-feather="download"></i>
    </a>
    @can('Create Products')
    <a href="#!" class="btn btn-sm btn-icon  bg-primary text-white me-2" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Import') }}" data-ajax-popup="true" data-size="lg" data-title="{{ __('Import Product CSV File') }}" data-url="{{ route('product.file.import') }}">
        <i  data-feather="upload"></i>
    </a>
    @endcan
    <a href="{{ route('product.index') }}" class="btn btn-sm btn-icon  bg-primary text-white me-2" data-bs-toggle="tooltip"
    data-bs-placement="top" title="{{ __('List View') }}"><i class="fas fa-list"></i></a>
    @can('Create Products')
    <a class="btn btn-sm btn-icon  btn-primary me-2" href="{{ route('product.create') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}">
        <i  data-feather="plus"></i>
    </a>
    @endcan
@endsection
@php
    $logo=\App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
@section('content')
    <div class="row row-gap">
        @foreach ($products as $product)
            <div class="col-xl-3 col-lg-4 col-sm-6 col-md-6 d-flex flex-column">
                <div class="card text-white text-center  mb-0 h-100">
                    <div class="card-header border-0 pb-0">
                        <div class="card-header-right">
                            <div class="btn-group card-option">
                                <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    @can('Show Products')
                                        <a href="{{ route('product.show', $product->id) }}" class="dropdown-item d-flex align-items-center gap-2" 
                                            title="{{ __('View') }}"><i
                                                class="fas fa-eye"></i>
                                            <span>{{ __('View') }}</span></a>
                                    @endcan
                                    @can('Edit Products')
                                        <a href="{{ route('product.edit', $product->id) }}" class="dropdown-item d-flex align-items-center gap-2" 
                                            title="{{ __('Edit') }}"><i
                                                class="ti ti-pencil "></i>
                                            <span>{{ __('Edit') }}</span></a>
                                    @endcan
                                    @can('Delete Products')
                                        <a class="bs-pass-para dropdown-item d-flex align-items-center gap-2 trigger--fire-modal-1" href="#"
                                            data-title="{{ __('Delete Lead') }}" data-confirm="{{ __('Are You Sure?') }}"
                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                            data-confirm-yes="delete-form-{{ $product->id }}" title="{{ __('Delete') }}">
                                            <i class="ti ti-trash"></i><span>{{ __('Delete') }} </span>

                                        </a>
                                        {!! Form::open(['method' => 'DELETE', 'route' => ['product.destroy', $product->id], 'id' => 'delete-form-' . $product->id]) !!}
                                        {!! Form::close() !!}
                                    @endcan
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body product_card h-100">
                        @if (!empty($product->is_cover))
                        <a href="{{ $logo.(isset($product->is_cover) && !empty($product->is_cover)?$product->is_cover:'default.png') }}" target="_blank">
                            <img alt="Image placeholder"
                                src="{{ $logo.(isset($product->is_cover) && !empty($product->is_cover)?$product->is_cover:'default.png') }}"
                                class="img-fluid border border-1 border-primary rounded" alt="images" width="70px" height="100%">
                        </a>
                        @else
                        <a href="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}" target="_blank">
                            <img alt="Image placeholder"
                                src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}"
                                class="img-fluid border border-1 border-primary rounded" alt="images" width="70px" height="100%">
                        </a>
                        @endif
                        <h4 class="text-primary mt-2"> <a
                                href="{{ route('product.show', $product->id) }}">{{ $product->name }}</a></h4>
                        <h4 class="text-muted">
                            <small>
                                @if ($product->enable_product_variant == 'on')
                                    {{ __('In Variant') }}
                                @else
                                    {{ \App\Models\Utility::priceFormat($product->price) }}
                                @endif
                            </small>
                        </h4>
                        @if ($product->enable_product_variant != 'on')
                            @if ($product->quantity == 0)
                                <span class="badge bg-light-primary border border-1 border-primary p-2 px-3 tbl-btn-w">
                                    {{ __('Out of stock') }}
                                </span>
                            @else
                                <span class="badge bg-light-primary border border-1 border-primary p-2 px-3 tbl-btn-w">
                                    {{ __('In stock') }}
                                </span>
                            @endif
                        @endif
                        <div class="row mt-2">
                            <div class="col-12 col-sm-12">
                                <div>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @php
                                            $icon = 'fa-star';
                                            $color = '';
                                            $newVal1 = $i - 0.5;
                                            if ($product->product_rating() < $i && $product->product_rating() >= $newVal1) {
                                                $icon = 'fa-star-half-alt';
                                            }
                                            if ($product->product_rating() >= $newVal1) {
                                                $color = 'text-warning';
                                            } else {
                                                $color = 'text-black';
                                            }
                                        @endphp
                                        <i class="fas {{ $icon . ' ' . $color }}"></i>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        @can('Create Products')
            <div class="col-lg-3 col-md-6 col-sm-6 create-user-card d-flex flex-column">
                <a href="{{ route('product.create') }}" class="btn-addnew-project border-primary h-100">
                    <div class="bg-primary proj-add-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Create Product')}}">
                        <i class="ti ti-plus my-2"></i>
                    </div>
                    <h6 class="mt-2 mb-2">{{ __('New Product') }}</h6>
                    <p class="text-muted text-center  mb-0">{{ __('Click here to add New Product') }}</p>
                </a>
            </div>
        @endcan
    </div>

@endsection
