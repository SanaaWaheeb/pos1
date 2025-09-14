{{ Form::open(array('route' => array('store.language'), 'class'=>'needs-validation', 'novalidate')) }}
<div class="form-group">
    {{ Form::label('code', __('Language Code'),array('class'=>'form-label'))}}<x-required></x-required>
    {{ Form::text('code', '', array('class' => 'form-control', 'placeholder' => __('Enter Language Code'),'required'=>'required')) }}
    @error('code')
    <span class="invalid-code" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group">
    {{ Form::label('fullname', __('Language Fullname'),array('class'=>'form-label'))}}<x-required></x-required>
    {{ Form::text('fullname', '', array('class' => 'form-control', 'placeholder' => __('Enter Language Full Name'),'required'=>'required')) }}
    @error('fullname')
    <span class="invalid-code" role="alert">
            <strong class="text-danger">{{ $message }}</strong>
        </span>
    @enderror
</div>
<div class="form-group col-12 d-flex justify-content-end col-form-label">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Save')}}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}

