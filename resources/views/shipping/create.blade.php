{{Form::open(array('url'=>'shipping','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div class="d-flex justify-content-end">
    @php
        $plan = \App\Models\Plan::find(\Auth::user()->plan);
    @endphp
    @if($plan->enable_chatgpt == 'on')
        <a href="#" class="btn btn-primary btn-sm" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['shipping']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i> {{ __('Generate with AI') }}
        </a>
    @endif
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label')) }}<x-required></x-required>
            {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('price',__('Price'),array('class'=>'form-label')) }}<x-required></x-required>
            {{Form::text('price',null,array('class'=>'form-control','placeholder'=>__('Enter Price'),'required'=>'required'))}}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('Location',__('Location'),array('class'=>'form-label')) }}
            {{ Form::select('location[]', $locations,null, array('class' => 'form-control multi-select','id'=>'choices-multiple','multiple'=>'')) }}
        </div>

    </div>
    <div class="form-group py-0 mb-0 col-12 d-flex justify-content-end form-label">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
