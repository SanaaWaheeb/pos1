@extends('layouts.admin')

@section('page-title')
    {{ __('Testimonial') }}
@endsection

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{__('Home')}}</a></li>
<li class="breadcrumb-item active" aria-current="page">{{__('Testimonial')}}</li>
@endsection

@section('action-btn')

    @can('Create Testimonial')
        <a href="#" class="btn btn-sm btn-icon  btn-primary" data-ajax-popup="true" data-url="{{ route('testimonial.create') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create') }}" data-title="{{ __('Create New Testimonial') }}">
            <i  data-feather="plus"></i>
        </a>
    @endcan

@endsection

@php
    $testimonial_image = \App\Models\Utility::get_file('uploads/testimonial_image/');
@endphp

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-body pb-0 table-border-style">
                <div class="table-responsive order-table-wrp">
                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Sub Title') }}</th>
                                <th>{{ __('Ratting') }}</th>
                                <th>{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($testimonials as $testimonial)
                                <tr data-name="{{ $testimonial->title }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if ($testimonial->image)
                                                <img src="{{ $testimonial_image }}/{{ $testimonial->image }}" alt="" class="theme-avtar border border-2 border-primary rounded">
                                            @else
                                                <img src="{{ $testimonial_image }}/avatar.png" alt="" class="theme-avtar border border-2 border-primary rounded">
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $testimonial->title }}</td>
                                    <td>{{ $testimonial->sub_title }}</td>
                                    <td>
                                        @for ($i = 0; $i < 5; $i++)
                                            <i class="fas fa-star {{ $testimonial->ratting > $i ? 'text-warning' : 'text-secondary' }}"></i>
                                        @endfor
                                    </td>
                                    <td>
                                        <div class="d-flex action-btn-wrapper">
                                            @can('Edit Testimonial')
                                                <a href="#!" class="btn btn-sm btn-icon  bg-info text-white me-2" data-url="{{ route('testimonial.edit', $testimonial->id) }}"  data-ajax-popup="true" data-title="{{ __('Edit Testimonial') }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}" data-tooltip="Edit">
                                                    <i class=" ti ti-pencil f-20"></i>
                                                </a>
                                            @endcan
                                            @can('Delete Testimonial')
                                                <a href="#!" class="bs-pass-para btn btn-sm btn-icon bg-danger text-white" data-title="{{ __('Delete Testimonial') }}" data-confirm="{{ __('Are You Sure?') }}"  data-text="{{ __('This action can not be undone. Do you want to continue?') }}" data-confirm-yes="delete-form-{{ $testimonial->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash f-20"></i>
                                                </a>
                                                {!! Form::open(['method' => 'DELETE', 'route' => ['testimonial.destroy', $testimonial->id], 'id' => 'delete-form-' . $testimonial->id]) !!}
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
</div>
@endsection
@push('script-page')
    
@endpush
