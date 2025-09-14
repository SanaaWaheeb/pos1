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
<li class="breadcrumb-item"><a href="{{ route('product.index') }}">{{ __('Products') }}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
@endsection
@section('action-btn')
    <div class="d-flex rating-btn-wrapper align-items-center justify-content-end">
        @can('Create Ratting')
            <a class="btn btn-primary me-2 text-white d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-size="md" data-toggle="modal" data-url="{{ route('rating', [$store->slug, $product->id]) }}"  data-ajax-popup="true" data-title="{{ __('Create New Rating') }}" data-bs-placement="top" title="{{ __('Create New Rating') }}">
                {{-- <i data-feather="star" class="me-2"></i> {{ __('Add Ratting') }} --}}
                <i class="ti ti-star f-20"></i> {{ __('Add Rating') }}
            </a>
        @endcan
        @can('Edit Products')
            <a href="{{ route('product.edit', $product->id) }}" class="btn btn-primary d-flex align-items-center justify-content-center ms-1" data-bs-placement="top"  data-bs-toggle="tooltip" title="{{ __('Edit Product') }}">
                {{-- <i data-feather="edit" class="me-2"></i> {{ __('Edit Product') }} --}}
                <i class="ti ti-pencil f-20"></i> {{ __('Edit Product') }}
            </a>
        @endcan
    </div>
@endsection
@section('filter')
@endsection
@php
    $logo=\App\Models\Utility::get_file('uploads/is_cover_image/');
    $p_logo=\App\Models\Utility::get_file('uploads/product_image/');
@endphp
@section('content')

<!-- [ sample-page ] start -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card border border-primary shadow-none">
                <div class="card-body">
                    <div class="d-flex mb-3 align-items-center gap-2 flex-sm-row flex-column justify-content-between">
                        <h4>{{ $product->name }}</h4>
                        <div class="ps-3 d-flex align-items-center ">
                                @if($product->enable_product_variant =='on')
                                <span class="badge p-2 bg-light-primary border border-1 border-primary common-lbl-radius "><span class="variant_qty">0</span>  {{ __('Total Avl.Quantity') }}</span>
                                @else
                                <span class="badge p-2 bg-light-primary border border-1 border-primary common-lbl-radius"> {{$product->quantity}}  {{ __('Total Avl.Quantity') }}</span>
                                @endif
                            <div class="text-end ms-3">
                                <span>{{ __('Price') }}:</span>
                                <h5 class="variasion_price mt-1">
                                @if ($product->enable_product_variant == 'on')
                                    {{ \App\Models\Utility::priceFormat(0) }}
                                @else
                                    {{ \App\Models\Utility::priceFormat($product->price) }}
                                @endif
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3 flex-lg-row flex-column align-items-center justify-content-between ">
                        <p class="mb-0"><b>{{ __('Categories') }}: {{  $product->categories->name ?? ''  }} </b></p>
                        <p class="mb-0"><b>{{ __('SKU') }}: {{ $product->SKU }}</b></p>
                        <p class="d-inline-flex mb-0 align-items-center">

                            @for ($i = 1; $i <= 5; $i++)
                                @php
                                    $icon = 'fa-star';
                                    $color = 'text-secondary';
                                    $newVal1 = $i - 0.5;
                                    if ($avg_rating < $i && $avg_rating >= $newVal1) {
                                        $icon = 'fa-star-half-alt';
                                    }
                                    if ($avg_rating >= $newVal1) {

                                        $color = 'text-success';

                                    }
                                @endphp

                                <i class="fas {{ $icon . ' ' . $color }}"></i>
                            @endfor
                            <span class="ms-2 d-block">{{ __('Rating') }}: {{ $avg_rating }} ({{ $user_count }} {{ __('reviews') }})</span>

                        </p>
                    </div>
                    <div class="border mb-4 rounded border-primary product_image">
                        @if (!empty($product->is_cover))
                            <img src="{{$logo.(isset($product->is_cover) && !empty($product->is_cover)?$product->is_cover:'default.png')}}" alt="" class="w-100">
                        @else
                            <img src="{{ asset(Storage::url('uploads/is_cover_image/default.jpg')) }}" alt="" class="w-100">
                        @endif
                    </div>
                    <h6 class="mb-2">{{ __('Description') }}:</h6>
                    <p>{!! $product->description !!}</p>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex gap-2 justify-content-between align-items-center">
                        <div>
                            <h5>{{ __('Express Checkout') }}</h5>
                            <small class="text-dark font-weight-bold">{{ __('Note:Create Express Checkout Url For Direct Order') }}</small>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-url="{{ route('expresscheckout.create',[$product->id]) }}" data-title="{{ __('Add Product') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}" data-tooltip="Create">
                            <i class="ti ti-plus f-20"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body pb-0 table-border-style mt-3">
                    <div class="table-responsive">
                        <table class="table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Quantity') }}</th>
                                    <th>{{ __('Variant Name') }}</th>
                                    <th>{{ __('URL') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($expresscheckout) > 0)
                                    @foreach($expresscheckout as $key => $value)
                                        <tr>
                                            <td>{{ $value->product->name }}</td>
                                            <td>{{ $value->quantity }}</td>
                                            <td>{{ isset($value->variant_name) ? $value->variant_name : '-' }}</td>
                                            <td><a href="#" class="btn btn-light-primary border border-1 border-primary cp_link common-lbl-radius" data-link="{{ env('APP_URL') . '/' . $value->url }}" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ __('Click to copy Checkout link') }}">{{ __('Copy Link') }}</a></td>
                                            <td class="d-flex action-btn-wrapper">
                                                <a href="#" class="btn bg-info text-white btn-icon btn-sm me-2" data-ajax-popup="true" data-url="{{ route('expresscheckout.edit',[$value->id]) }}" data-title="{{ __('Edit Expresscheckout') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}"><i class="ti ti-pencil "></i></a>
                                                <a class="bs-pass-para btn btn-sm btn-icon bg-danger text-white" href="#"
                                                    data-title="{{ __('Delete Checkout Link') }}"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $value->id }}"
                                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash f-20"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['expresscheckout.destroy', $value->id], 'id' => 'delete-form-' . $value->id]) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row row-gap mt-3">
                @if ($product->enable_product_variant == 'on')
                    <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 col-12">
                        <div class="card  mb-0 h-100">
                            <div class="card-header">
                                <h5 class="fs-4">{{ __('Variants') }}</h4>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <input type="hidden" id="product_id" value="{{ $product->id }}">
                                    <input type="hidden" id="variant_id" value="">
                                    <input type="hidden" id="variant_qty" value="">
                                    @foreach ($product_variant_names as $key => $variant)
                                        <div class="col-sm-6 mb-4 mb-sm-0">
                                            <span class="d-block h6 mb-0">
                                                <th>
                                                    <label for="" class="col-form-label"> {{ ucfirst($variant->variant_name) }}</label>
                                                </th>
                                                <select name="product[{{$key}}]" id='choices-multiple-2-{{$key}}'  class="form-control pro_variants_name{{$key}} change_price">
                                                <option value="">{{ __('Select')  }}</option>
                                                    @foreach ($variant->variant_options as $key => $values)
                                                    <option value="{{$values}}">{{$values}}</option>
                                                @endforeach
                                            </select>
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 col-12">
                    <div class="card mb-0  h-100">
                        <div class="card-header">
                            <h5 class="fs-4">{{ __('Ratings') }}</h4>
                        </div>

                        <div class="card-body">
                            @foreach ($product_ratings as $product_key => $product_rating)
                                <div class="border-bottom pb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="mb-0">{{ $product_rating->title }}</h3>
                                        <div class="d-flex action-btn-wrapper align-items-center">
                                            <div class="form-check form-switch mb-0">
                                                <input type="checkbox" class="form-check-input rating_view me-2" name="rating_view" id="enable_rating{{ $product_key }}" data-id="{{ $product_rating['id'] }}" {{ $product_rating->rating_view == 'on' ? 'checked' : '' }}>
                                                <label class="form-check-label f-w-600 pl-1" for="enable_rating{{ $product_key }}"></label>
                                            </div>
                                            @can('Edit Ratting')
                                                <a href="#!" class="btn btn-icon btn-sm bg-info text-white me-2"  data-url="{{ route('rating.edit', $product_rating->id) }}"  data-ajax-popup="true" data-title="{{ __('Edit Rating') }}"  data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit Rating ') }}">
                                                    <i class="ti ti-pencil f-20"></i>
                                                </a>
                                            @endcan
                                            @can('Delete Ratting')
                                                <a href="#!" class="bs-pass-para btn btn-icon btn-sm bg-danger text-white " data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}" data-title="{{__('Delete Lead')}}" data-confirm="{{__('Are You Sure?')}}" data-text="{{__('This action can not be undone. Do you want to continue?')}}" data-confirm-yes="delete-form-{{$product_rating->id}}">
                                                    <i class="ti ti-trash f-20"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['rating.destroy', $product_rating->id],'id'=>'delete-form-'.$product_rating->id]) !!}
                                                {!! Form::close() !!}
                                            @endcan
                                        </div>
                                    </div>
                                    <p>{{ $product_rating->description }}</p>
                                    <div class="d-flex align-items-center">
                                        <div class="">
                                            <div class="d-flex mb-2">
                                                @for ($i = 0; $i < 5; $i++)
                                                    <i class="fas fa-star {{ $product_rating->ratting > $i ? 'text-success' : 'text-secondary' }}"></i>
                                                @endfor
                                            </div>
                                            <small>{{ __('by') }} {{ $product_rating->name }} : {{ $product_rating->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 col-12">
                    <div class="card mb-0 h-100">
                        <div class="card-header">
                            <h5 class="fs-4">{{ __('Gallery') }}</h4>
                        </div>
                        <div class="card-body">
                            <div class="row gy-3 gx-3">
                                @foreach ($product_image as $key => $products)
                                    <div class="col-sm-6">
                                        <div class="p-2 border border-primary rounded">
                                            @if (!empty($product_image[$key]->product_images))
                                                <img src="{{$p_logo.(isset($product_image[$key]->product_images) && !empty($product_image[$key]->product_images)?$product_image[$key]->product_images:'is_cover_image.png')}}" alt="" class="w-100">
                                            @else
                                                <img src="{{$p_logo.(isset($product_image[$key]->product_images) && !empty($product_image[$key]->product_images)?$product_image[$key]->product_images:'is_cover_image.png')}}" alt="" class="w-100">
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

<!-- [ sample-page ] end -->

@endsection
@push('script-page')
    <script>
        $(document).ready(function() {
            $('.cp_link').on('click', function() {
                var value = $(this).attr('data-link');
                var $temp = $("<input>");
                $("body").append($temp);
                $temp.val(value).select();
                document.execCommand("copy");
                $temp.remove();
                show_toastr('Success', '{{ __('Link copied') }}', 'success')
            });
        });

        $(document).on('change', '.rating_view', function() {
            var id = $(this).attr('data-id');
            var status = 'off';
            if ($(this).is(":checked")) {
                status = 'on';
            }
            var data = {
                id: id,
                status: status
            }

            $.ajax({
                url: '{{ route('rating.rating_view') }}',
                method: 'POST',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    show_toastr('success', data.success, 'success');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                }
            });
        });


        $(document).on('change', '.change_price', function () {
            var variants = [];
            $(".change_price").each(function (index, element) {
                variants.push(element.value);
            });
            if (variants.length > 0) {
                $.ajax({
                    url: '{{route('get.products.variant.quantity')}}',
                    data: {
                        "_token": $('meta[name="csrf-token"]').attr('content'),
                        variants: variants.join(' : '),
                        product_id: $('#product_id').val()
                    },

                    success: function (data) {
                        console.log(data);
                        $('.variasion_price').html(data.price);
                        $('#variant_id').val(data.variant_id);
                        $('.variant_qty').html(data.quantity);
                    }
                });
            }
        });

    </script>
@endpush
