<form method="post" action="{{ route('product-coupon.store') }}" id="product-coupon-store" class="needs-validation" novalidate>
    @csrf
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
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12 mb-0">
            <div class="form-check form-switch">
                <input type="checkbox" class="form-check-input" name="enable_flat" id="enable_flat">
                {{Form::label('enable_flat',__('Flat Discount'),array('class'=>'form-check-label mb-0')) }}
            </div>
        </div>
        <div class="form-group col-md-6 nonflat_discount">
            {{Form::label('discount',__('Discount') ,array('class'=>'form-label')) }}<x-required></x-required>
            {{Form::number('discount',null,array('class'=>'form-control','step'=>'0.01','placeholder'=>__('Enter Discount'),'required'=>'required'))}}
            <span class="small">{{__('Note: Discount in Percentage')}}</span>
        </div>
        <div class="form-group col-md-6 flat_discount" style="display: none;">
            {{Form::label('pro_flat_discount',__('Flat Discount') ,array('class'=>'form-label')) }}<x-required></x-required>
            {{Form::number('pro_flat_discount',null,array('class'=>'form-control','min'=>'0','step'=>'0.01','placeholder'=>__('Enter Flat Discount')))}}
            <span class="small">{{__('Note: Discount in Value')}}</span>
        </div>
        <div class="form-group col-md-6">
            {{Form::label('limit',__('Limit') ,array('class'=>'form-label'))}}<x-required></x-required>
            {{Form::number('limit',null,array('class'=>'form-control','placeholder'=>__('Enter Limit'),'required'=>'required'))}}
        </div>
        <div class="form-group col-md-12" id="auto">
            {{Form::label('limit',__('Code') ,array('class'=>'form-label'))}}<x-required></x-required>
            <div class="input-group">
                {{Form::text('code',null,array('class'=>'form-control','id'=>'auto-code','required'=>'required','placeholder'=>__('Enter Code')))}}
                <button class="btn btn-outline-secondary" type="button" id="code-generate"><i class="fa fa-history pr-1"></i>{{__(' Generate')}}</button>
            </div>
        </div>
        <div class="form-group col-12 py-0 mb-0 d-flex justify-content-end form-label">
            <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
            <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
        </div>
    </div>
</form>
