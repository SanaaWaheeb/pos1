@php
    $admin_payments_setting = Utility::getAdminPaymentSetting();
@endphp
@foreach($plans as $plan)
    <div class="list-group-item mb-2">
        <div class="row align-items-center">
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{$plan->name}}</a>
                <div>
                    <span class="text-sm">{{ isset($admin_payments_setting['currency_symbol']) ? $admin_payments_setting['currency_symbol'] : '$' }}{{ $plan->price . ' / '. __(\App\Models\Plan::$arrDuration[$plan->duration])}}</span>
                </div>
            </div>
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{__('Stores')}}</a>
                <div>
                    <span class="text-sm">
                        @if ($plan->max_stores == '-1')
                            {{ __('Unlimited') }}
                        @else
                            {{$plan->max_stores}}
                        @endif
                    </span>
                </div>
            </div>
            <div class="col ml-n2">
                <a href="#!" class="d-block h6 mb-0">{{__('Products')}}</a>
                <div>
                    <span class="text-sm">
                        @if ($plan->max_products == '-1')
                            {{ __('Unlimited') }}
                        @else
                            {{$plan->max_products}}
                        @endif
                    </span>
                </div>
            </div>
            <div class="col-auto">
                @if($user->plan==$plan->id)
                <span class="d-flex align-items-center ">
                    <i class="f-10 lh-1 fas fa-circle text-primary"></i>
                    <span class="ms-2">{{ __('Active')}}</span>
                </span>
                @else
                    <a href="{{route('plan.active',[$user->id,$plan->id])}}" class="btn btn-primary btn-icon" data-toggle="tooltip" data-original-title="{{__('Click to Upgrade Plan')}}" title="{{ __('Click to Upgrade Plan') }}">
                        <span class="btn-inner--icon"><i class="fas fa-cart-plus"></i></span>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endforeach
