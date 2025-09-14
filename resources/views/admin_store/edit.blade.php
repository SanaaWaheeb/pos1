{{Form::model($store, array('route' => array('store-resource.update', $store->id), 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('name',__('Name'),array('class'=>'form-label'))}}<x-required></x-required>
            {{Form::text('name',$user->name,array('class'=>'form-control','placeholder'=>__('Enter Name'),'required'=>'required'))}}
            @error('name')
            <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('store_name',__('Store Name'),array('class'=>'form-label'))}}<x-required></x-required>
            {{Form::text('store_name',$store->name,array('class'=>'form-control','placeholder'=>__('Enter Store Name'),'required'=>'required'))}}
            @error('store_name')
            <span class="invalid-store_name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{Form::label('email',__('Email'),array('class'=>'form-label'))}}<x-required></x-required>
            {{Form::text('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
            @error('email')
            <span class="invalid-email" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    
    <!-- Fridge Locker Switch -->
    <div class="col-12">
        <div class="form-group">
            <label for="door_switch">{{ __('Fridge lock is enable') }}</label>
            <div class="form-check form-switch custom-switch-v1 float-end">
                <input type="checkbox" name="door_switch" class="form-check-input input-primary pointer" value="on" id="door_switch" {{ $store->door == 'on' ? 'checked' : '' }}>
                <label class="form-check-label" for="door_switch"></label>
            </div>
        </div>
    </div>
    <!-- Board ID Input (Initially Hidden if Door is Off) -->
    <div class="col-12 board_id_div {{ $store->door == 'on' ? '' : 'd-none' }}" >
        <div class="form-group">
            {{Form::label('board_id',__('Board ID'),array('class'=>'form-label'))}}<x-required></x-required>
            {{ Form::text('board_id', $store->board_id, [
                'class' => 'form-control',
                'placeholder' => __('Enter Board ID'),
                'id' => 'board_id_input',
                ($store->door == 'on' ? 'required' : '') => ($store->door == 'on' ? 'required' : '') 
            ]) }}
        </div>
    </div>

</div>
<div class="form-group col-12 py-0 mb-0 d-flex justify-content-end col-form-label">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
</div>
{{Form::close()}}
