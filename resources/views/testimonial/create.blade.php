{{Form::open(array('url'=>'testimonial','method'=>'post','enctype'=>'multipart/form-data', 'class'=>'needs-validation', 'novalidate'))}}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('title', __('Title'), array('class' => 'form-label')) }}<x-required></x-required>
            {{ Form::text('title', null, array('class' => 'form-control', 'placeholder' => __('Enter Title'), 'required' => 'required')) }}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('sub_title', __('Sub Title'), array('class' => 'form-label')) }}<x-required></x-required>
            {{ Form::text('sub_title', null, array('class' => 'form-control', 'placeholder' => __('Enter Sub Title'), 'required' => 'required')) }}
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('description', __('Description'), array('class' => 'form-label')) }}<x-required></x-required>
            {{ Form::textarea('description', null, array('class' => 'form-control', 'rows' => 3, 'placeholder' => __('Enter Description'), 'required' => 'required')) }}
        </div>
    </div>
    <div class="col-12 pb-2">
        <div class="form-group">
            {{ Form::label('title', __('Rating'), ['class' => 'form-label']) }}
            <div id="rating_div">
                <div class="rate p-0">
                    <input type="radio" class="rating" id="star5" name="rate" value="5"/>
                    <label for="star5" title="5"></label>
                    <input type="radio" class="rating" id="star4" name="rate" value="4"/>
                    <label for="star4" title="4"></label>
                    <input type="radio" class="rating" id="star3" name="rate" value="3"/>
                    <label for="star3" title="3"></label>
                    <input type="radio" class="rating" id="star2" name="rate" value="2"/>
                    <label for="star2" title="2"></label>
                    <input type="radio" class="rating" id="star1" name="rate" value="1"/>
                    <label for="star1" title="1"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label for="image" class="form-label">{{ __('Upload Image') }}</label>
            <input type="file" name="image" id="image" class="form-control" onchange="document.getElementById('testimonialImg').src = window.URL.createObjectURL(this.files[0])">
            <img id="testimonialImg" src="" width="20%" class="mt-2"/>
        </div>
    </div>
    <div class="form-group col-12 d-flex py-0 mb-0 justify-content-end form-label">
        <input type="button" value="{{__('Cancel')}}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{__('Create')}}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}
