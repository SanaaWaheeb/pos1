{{Form::open(array('url'=>'subscriptions','method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::email('email',null,array('class'=>'form-control','placeholder'=>__('Enter Email'),'required'=>'required'))}}
        </div>
    </div>
    <div class="form-group py-0 mb-0 col-12 d-flex justify-content-end col-form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Save')}}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
