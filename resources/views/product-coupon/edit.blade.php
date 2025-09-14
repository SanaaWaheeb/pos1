<form method="post" action="{{ route('product-coupon.update', $productCoupon->id) }}" id="product-coupon-store" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="d-flex justify-content-end">
        @php
            $plan = \App\Models\Plan::find(\Auth::user()->plan);
        @endphp
        @if($plan->enable_chatgpt == 'on')
            <a href="#" class="btn btn-primary btn-sm" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['coupan']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
                <i class="fas fa-robot"></i> {{ __('Generate with AI') }}
            </a>
        @endif
    </div>
    <div class="row">
        <div class="form-group col-md-12">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}<x-required></x-required>
            {{Form::text('name',$productCoupon->name,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="enable_flat" id="enable_flat" {{ ($productCoupon['enable_flat'] == 'on') ? 'checked=checked' : '' }}>
                {{-- <label class="custom-control-label form-label" for="enable_flat"></label> --}}
                {{Form::label('enable_flat',__('Flat Discount'),array('class'=>'form-check-label')) }}
            </div>
        </div>

        <div class="form-group col-md-6 nonflat_discount">
            {{Form::label('discount',__('Discount') ,array('class'=>'form-label')) }}<x-required></x-required>
            {{Form::number('discount',$productCoupon->discount,array('class'=>'form-control','min'=>'0','step'=>'0.01','placeholder'=>__('Enter Discount'),'required'=>'required'))}}
            <span class="small">{{__('Note: Discount in Percentage')}}</span>
        </div>
        <div class="form-group col-md-6 flat_discount" style="display: none;">
            {{Form::label('pro_flat_discount',__('Flat Discount') ,array('class'=>'form-label')) }}<x-required></x-required>
            {{Form::number('pro_flat_discount',$productCoupon->flat_discount,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Flat Discount')))}}
            <span class="small">{{__('Note: Discount in Value')}}</span>
        </div>
        <div class="form-group col-md-6">
            <label for="limit" class="form-label">{{__('Limit')}}</label><x-required></x-required>
            <input type="number" name="limit" class="form-control" required value="{{$productCoupon->limit}}" placeholder="{{ __('Enter Limit') }}">
        </div>
        <div class="form-group col-md-12" id="auto">
            {{Form::label('limit',__('Code') ,array('class'=>'form-label'))}}<x-required></x-required>
            <div class="input-group">
                {{Form::text('code',$productCoupon->code,array('class'=>'form-control','id'=>'auto-code','required'=>'required','placeholder'=>__('Enter Code')))}}
                <button class="btn btn-outline-secondary" type="button" id="code-generate"><i class="fa fa-history pr-1"></i>{{__(' Generate')}}</button>
            </div>
        </div>
        <div class="form-group col-12 py-0 mb-0 d-flex justify-content-end form-label">
            <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
            <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
        </div>
    </div>
</form>

