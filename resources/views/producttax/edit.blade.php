{{ Form::model($productTax, ['route' => ['product_tax.update', $productTax->id], 'method' => 'PUT', 'class'=>'needs-validation', 'novalidate']) }}
<div class="row">
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('tax_name', __('Tax Name'),['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::text('name', null, ['class' => 'form-control', 'placeholder' => __('Enter Tax Name'),'required'=>'required']) }}
            @error('tax_name')
                <span class="invalid-tax_name" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="form-group">
            {{ Form::label('rate', __('Rate').__(' (%)'),['class' => 'form-label']) }}<x-required></x-required>
            {{ Form::number('rate', null, ['class' => 'form-control', 'placeholder' => __('Enter Rate'),'min'=>'0','required'=>'required']) }}
            @error('rate')
                <span class="invalid-rate" role="alert">
                    <strong class="text-danger">{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
</div>
<div class="form-group py-0 mb-0 col-12 d-flex justify-content-end form-label">
    <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Update') }}" class="btn btn-primary ms-2">
</div>
{{ Form::close() }}
