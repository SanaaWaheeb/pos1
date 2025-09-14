{{ Form::model($productCategorie, ['route' => ['product_categorie.update', $productCategorie->id],'method' => 'PUT','enctype' => 'multipart/form-data', 'class'=>'needs-validation', 'novalidate']) }}
<div class="d-flex justify-content-end">
    @php
        $plan = \App\Models\Plan::find(\Auth::user()->plan);
        $store_logo = \App\Models\Utility::get_file('uploads/product_image/');
    @endphp
    @if($plan->enable_chatgpt == 'on')
        <a href="#" class="btn btn-primary btn-sm" data-size="lg" data-ajax-popup-over="true" data-url="{{ route('generate',['category']) }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Generate') }}" data-title="{{ __('Generate Content With AI') }}">
            <i class="fas fa-robot"></i> {{ __('Generate with AI') }}
        </a>
    @endif
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('name', __('Name'), ['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Product Category'),'required'=>'required']) }}
            @error('name')
                <span class="invalid-name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group">
            <label for="categorie_img" class="form-label">{{ __('Upload Category Image') }}</label>
            {{-- <input type="file" name="categorie_img" id="categorie_img" class="form-control"> --}}
            <input type="file" name="categorie_img" id="categorie_img" class="form-control" onchange="document.getElementById('catImg').src = window.URL.createObjectURL(this.files[0])" multiple>
            <img id="catImg" src="{{ !empty($productCategorie->categorie_img) ? $store_logo .'/'. $productCategorie->categorie_img : '' }}" width="20%" class="mt-2 border-primary border rounded border-2"/>
        </div>
    </div>
</div>
<div class="form-group col-12 py-0 mb-0 d-flex justify-content-end form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}
