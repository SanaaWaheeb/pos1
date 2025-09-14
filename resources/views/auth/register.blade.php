@extends('layouts.auth')
@section('page-title')
    {{__('Register')}}
@endsection
@section('language-bar')
@php
    $languages = App\Models\Utility::languages();
    $settings = App\Models\Utility::settings();
    if (isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes'){
        config(
            [
                'captcha.secret' => $settings['NOCAPTCHA_SECRET'],
                'captcha.sitekey' => $settings['NOCAPTCHA_SITEKEY'],
                'options' => [
                    'timeout' => 30,
                ],
            ]
        );
    }
    $landingPageSettings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
    $keyArray = [];
    if (
        is_array(json_decode($landingPageSettings['menubar_page'])) ||
        is_object(json_decode($landingPageSettings['menubar_page']))
    ) {
        foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value) {
            if (in_array($value->menubar_page_name, ['Terms and Conditions']) || in_array($value->menubar_page_name, ['Privacy Policy'])) {
                $keyArray[] = $value->menubar_page_name;
            }
        }
    }
@endphp
<div class="lang-dropdown-only-desk">
    <li class="dropdown dash-h-item drp-language">
        <a class="dash-head-link dropdown-toggle btn" href="#"
            data-bs-toggle="dropdown" aria-expanded="false">
            <span class="drp-text"> {{ ucFirst($languages[$lang]) }}
            </span>
        </a>
        <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
            @foreach($languages as $code => $language)
                <a href="{{ route('register',[$ref,$code]) }}" tabindex="0" class="dropdown-item {{ $code == $lang ? 'active':'' }}">
                    <span>{{ ucFirst($language)}}</span>
                </a>
            @endforeach
        </div>
    </li>
</div>
@endsection
@section('content')
@if (session('status'))
    <div class="mb-4 font-medium text-lg text-green-600 text-danger">
        {{ __('Email SMTP settings does not configured so please contact to your site admin.') }}
    </div>
@endif
<div class="card-body">
    <div>
        <h2 class="mb-3 f-w-600">{{ __('Register') }}</h2>
    </div>
    <div class="custom-login-form">
        <form method="POST" id="registerForm" action="{{ route('register',['plan' => $plan]) }}" class="needs-validation" novalidate="">
            @csrf
            <div class="form-group mb-3">
                <label class="form-label">{{ __('Name') }}</label>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="{{ __('Enter Your Name') }}">
                @error('name')
                    <span class="error invalid-name text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label">{{ __('Store Name') }}</label>
                <input id="store_name" type="text" class="form-control @error('store_name') is-invalid @enderror" name="store_name" value="{{ old('store_name') }}" required autocomplete="store_name" placeholder="{{ __('Enter Store Name') }}">
                @error('store_name')
                    <span class="error invalid-name text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label">{{ __('Email') }}</label>
                <input id="email" type="email" class="form-control  @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('Enter Email') }}" required>
                @error('email')
                    <span class="error invalid-email text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label class="form-label">{{ __('Password') }}</label>
                <input id="password" type="password" class="form-control  @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Enter Password') }}">
                @error('password')
                    <span class="error invalid-password text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">{{__('Confirm password')}}</label>
                <input id="password-confirm" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Enter Confirm Password') }}">
                @error('password_confirmation')
                    <span class="error invalid-password_confirmation text-danger" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- @if (isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes')
                <div class="form-group mb-4">
                    {!! NoCaptcha::display($settings['cust_darklayout']=='on' ? ['data-theme' => 'dark'] : []) !!}
                    @error('g-recaptcha-response')
                        <span class="error small text-danger" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            @endif --}}
            @if (isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes')
                @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2')
                    <div class="form-group mb-4">
                        {!! NoCaptcha::display($settings['cust_darklayout']=='on' ? ['data-theme' => 'dark'] : []) !!}
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @else
                    <div class="form-group col-lg-12 col-md-12 mt-3">
                        <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response" class="form-control">
                        @error('g-recaptcha-response')
                            <span class="error small text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                @endif
            @endif

            @if (isset($keyArray) && count($keyArray) > 0)
                <div class="form-check custom-checkbox">
                    <input type="checkbox"
                        class="form-check-input @error('terms_condition_check') is-invalid @enderror"
                        id="termsCheckbox" name="terms_condition_check" required>
                    <input type="hidden" name="terms_condition" id="terms_condition" value="off">

                    <label class="text-sm" for="terms_condition_check">{{ __('I agree to the ') }}
                        @foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value)
                            @if (in_array($value->menubar_page_name, ['Terms and Conditions']) && isset($value->template_name))
                                <a href="{{ $value->template_name == 'page_content' ? route('custom.page', $value->page_slug) : $value->page_url }}"
                                    target="_blank">{{ $value->menubar_page_name }}</a>
                            @endif
                        @endforeach
                        @if (count($keyArray) == 2)
                            {{ __('and the ') }}
                        @endif
                        @foreach (json_decode($landingPageSettings['menubar_page']) as $key => $value)
                            @if (in_array($value->menubar_page_name, ['Privacy Policy']) && isset($value->template_name))
                                <a href="{{ $value->template_name == 'page_content' ? route('custom.page', $value->page_slug) : $value->page_url }}"
                                    target="_blank">{{ $value->menubar_page_name }}</a>
                            @endif
                        @endforeach
                    </label>
                </div>
                @error('terms_condition_check')
                    <span class="error invalid-terms_condition_check text-danger" role="alert">
                        <strong>{{ __('Please check this box if you want to proceed.') }}</strong>
                    </span>
                @enderror
            @endif
            
            <div class="d-grid">
                <input type="hidden" name="ref_code" value="{{$ref}}">
                <button class="btn btn-primary mt-2" type="submit">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
        @if(Utility::getValByName('signup_button')=='on')
            <p class="my-4 text-center">{{ __("Already have an account?") }}
                <a href="{{route('login',$lang)}}" tabindex="0">{{__('Login')}}</a>
            </p>
        @endif  
    </div>
</div>
@endsection
@push('custom-scripts')
<script src="{{ asset('custom/libs/jquery/dist/jquery.min.js') }}"></script>
    {{-- @if (isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes')
        {!! NoCaptcha::renderJs() !!}
    @endif --}}
    @if (isset($settings['RECAPTCHA_MODULE']) && $settings['RECAPTCHA_MODULE'] == 'yes')
        @if (isset($settings['google_recaptcha_version']) && $settings['google_recaptcha_version'] == 'v2')
            {!! NoCaptcha::renderJs() !!}
        @else
            <script src="https://www.google.com/recaptcha/api.js?render={{ $settings['NOCAPTCHA_SITEKEY'] }}"></script>
            <script>
                $(document).ready(function() {
                    grecaptcha.ready(function() {
                        grecaptcha.execute('{{ $settings['NOCAPTCHA_SITEKEY'] }}', {
                            action: 'submit'
                        }).then(function(token) {
                            $('#g-recaptcha-response').val(token);
                        });
                    });
                });
            </script>
        @endif
    @endif
    @if (isset($keyArray) && count($keyArray) > 0)
        <script>
            $('#registerForm').on('submit', function() {
                if ($('#termsCheckbox').prop('checked')) {
                    $('#terms_condition').val('on');
                }
            });
        </script>
    @endif
@endpush
