{{Form::model($blog, array('route' => array('blog.update', $blog->id), 'method' => 'PUT','enctype'=>'multipart/form-data', 'class'=>'needs-validation', 'novalidate')) }}
<div class="d-flex justify-content-end">
    @php
        $plan = \App\Models\Plan::find(\Auth::user()->plan);
        $store_logo = \App\Models\Utility::get_file('uploads/blog_cover_image/')
    @endphp
    @if($plan->enable_chatgpt == 'on')
        <a href="#" class="btn btn-primary btn-sm" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['blog']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i> {{ __('Generate with AI') }}
        </a>
    @endif
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{Form::label('title',__('Title'),array('class'=>'form-label'))}}<x-required></x-required>
            {{Form::text('title',null,array('class'=>'form-control','placeholder'=>__('Enter Title'), 'required' => 'required'))}}
            @error('title')
            <span class="invalid-title" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            <label for="blog_cover_image" class="col-form-label pt-0">{{ __('Blog Cover image') }}</label>
            {{-- <input type="file" name="blog_cover_image" id="blog_cover_image"  class="form-control"> --}}
            <input type="file" name="blog_cover_image" id="blog_cover_image" class="form-control" onchange="document.getElementById('blogImgEdit').src = window.URL.createObjectURL(this.files[0])" >
            <img id="blogImgEdit" src="{{ !empty($blog->blog_cover_image) ? $store_logo . $blog->blog_cover_image : '' }}" width="20%" class="mt-2"/>
        </div>
    </div>
    <div class="form-group col-md-12 mb-0">
        {{Form::label('detail',__('Detail'),array('class'=>'col-form-label pt-0')) }}
        {{Form::textarea('detail',null,array('class'=>'form-control summernote-simple','rows'=>3,'placeholder'=>__('Detail')))}} {{-- pc-tinymce-2 --}}
        @error('detail')
        <span class="invalid-detail" role="alert">
             <strong class="text-danger">{{ $message }}</strong>
         </span>
        @enderror
    </div>
    <script src="{{ asset('assets/js/plugins/tinymce/tinymce.min.js') }}"></script>

    <script>
        if ($(".pc-tinymce-2").length) {
            tinymce.init({
                selector: '.pc-tinymce-2',
                height: "400",
                content_style: 'body { font-family: "Inter", sans-serif; }',
                menubar:false,
                statusbar: false,
            });
        }
    </script>
</div>
<div class="form-group col-12 py-0 mb-0 d-flex justify-content-end col-form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{Form::close()}}
