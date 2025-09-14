{{Form::model($user,array('route' => array('user.password.update', $user->id), 'method' => 'post', 'class'=>'needs-validation', 'novalidate')) }}
<div class="row">
    <div class="form-group col-12">
        {{ Form::label('password', __('Password'),array('class'=>'form-label'))}}<x-required></x-required>
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="{{ __('Enter Password') }}">
        @error('password')
        <span class="invalid-feedback" role="alert">
               <strong>{{ $message }}</strong>
           </span>
        @enderror
    </div>
    <div class="form-group col-12">
        {{ Form::label('password_confirmation', __('Confirm Password'),array('class'=>'form-label'))}}<x-required></x-required>
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Enter Confirm Password') }}">
    </div>
    <div class="login_field d-none"></div>
</div>
<div class="form-group col-12 py-0 mb-0 d-flex justify-content-end col-form-label">
    <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{__('Update')}}" class="btn btn-primary ms-2">
</div>

{{ Form::close() }}
