@extends('storefront.layout.theme6')
@section('page-title')
    {{__('Register')}}
@endsection
@php
if (!empty(session()->get('lang'))) {
    $currantLang = session()->get('lang');
} else {
    $currantLang = $store->lang;
}
\App::setLocale($currantLang);
@endphp
@push('css-page')

@endpush
@section('content')
  <div class="wrapper">
        <section class="login-section padding-top padding-bottom ">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-7 col-md-12 col-12">
                        <div class="login-form">
                            <div class="section-title">
                                <h2>Customer Register</h2>
                            </div>
                           {!! Form::open(['route' => ['store.userstore', $slug]], ['method' => 'post']) !!}
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Full Name')}}</label><x-required></x-required>
                                    <input name="name" class="form-control" type="text" placeholder="Full Name *" required="required">
                                </div>
                                @error('name')
                                    <span class="error invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Email')}}</label><x-required></x-required>
                                    <input name="email" class="form-control" type="email" placeholder="Email *" required="required">
                                </div>
                                @error('email')
                                    <span class="error invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-group">
                                    {{-- <label for="exampleInputEmail1">Number</label>
                                    <input name="phone_number" class="form-control" type="text" placeholder="Number *" required="required"> --}}
                                    <x-mobile divClass="form-group" class="form-control" name="phone_number" label="{{__('Number')}}" placeholder="{{__('Number *')}}" required="true"></x-mobile>
                                </div>
                                @error('number')
                                    <span class="error invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Password')}}</label><x-required></x-required>
                                    <input name="password" class="form-control" type="password" placeholder="Password *" required="required">
                                </div>
                                @error('password')
                                    <span class="error invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="form-group">
                                    <label for="exampleInputEmail1">{{__('Confirm Password')}}</label><x-required></x-required>
                                    <input name="password_confirmation" class="form-control" type="password" placeholder="Confirm Password *" required="required">
                                </div>
                                @error('password_confirmation')
                                    <span class="error invalid-email text-danger" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <div class="last-btns">
                                    <button class="login-btn btn" type="submit">{{__('Register')}}</button>
                                    <p>{{ __('By using the system, you accept the')}} 
                                        <a href="">  {{__('Privacy Policy')}}</a> {{ __('and') }} <a href=""> {{ __('System Regulations.') }} </a>
                                    </p>
                                </div>
                                <p class="register-btn">{{__('Already registered ?')}} <a href="{{ route('customer.loginform', $slug) }}">{{__('Log in')}}</a></p>
                             {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@push('script-page')
@endpush
