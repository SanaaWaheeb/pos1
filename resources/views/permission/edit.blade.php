
{{Form::model($permission, array('route' => array('permissions.update', $permission->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate')) }}
<div class="card-body p-0">
    <div class="form-group">
        {{Form::label('name',__('Name'), ['class' => 'form-label']) }}<x-required></x-required>
        {{Form::text('name',null,array('class'=>'form-control','placeholder'=>__('Enter Permission Name'),'required'=>'required'))}}
        @error('name')
        <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
        @enderror
    </div>
</div>
<div class="modal-footer pr-0">
    <button type="button" class="btn dark btn-outline" data-bs-dismiss="modal">{{__('Cancel')}}</button>
    {{Form::submit(__('Update'),array('class'=>'btn btn-primary'))}}
</div>
{{Form::close()}}
