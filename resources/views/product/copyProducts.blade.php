<!-- Copy from Stores Modal Content -->
{{Form::open(array('url'=>route('copy-products.copy'),'method'=>'post', 'class'=>'needs-validation', 'novalidate'))}}
<div>
@php
    $user = \Auth::user()->currentuser();
    $productImg = \App\Models\Utility::get_file('uploads/is_cover_image/');
@endphp
    <!-- Store name selection input -->
    <div class="row"> 
        <div class="col-12">
            <div class="form-group">
                {{ Form::label('stores', __('Select Store'), ['class' => 'col-form-label']) }}
                {{ Form::select('stores[]', $stores, null, ['class' => 'form-control', 'placeholder'=>__('Please Select a store'),'required'=>'required', 'id' => 'store-selector']) }}
            </div>
        </div> 
    </div>

    <!-- Display Products -->
    <div>
        <div id="select-all-products" class="justify-content-end align-items-center" style="gap: 10px; padding: 20px; display: none;">
            <a class="text-dark c-list-title mb-0 cart_word_break"> {{ __('Select All') }} </a>
            <input type="checkbox" class="align-middle ischeck form-check-input" name="checkall" data-id="Role">
        </div>

        <div id="product-list" style="max-height: 40vh; overflow-y: auto; padding: 20px">
            <!-- The store products will be displayed here dynamically -->
        </div>
    </div>

    <!-- Buttons -->
    <div class="form-group col-12 d-flex justify-content-end col-form-label">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary btn-light" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Copy') }}" class="btn btn-primary ms-2">
    </div>
</div>
{{Form::close()}}