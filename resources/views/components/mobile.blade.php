<div class="{{ $divClass }}">
    <div class="form-group">
        {{Form::label($name,$label,['class'=>'form-label'])}}@if($required) <x-required></x-required> @endif
        {{Form::text($name,$value,array('class'=>$class,'placeholder'=>$placeholder,'pattern' => '^\+966\d{9}','id'=>$id,'required'=>$required))}}
        <div class=" text-xs text-danger"  style="font-size: 0.825rem !important; color: rgba(255, 58, 110, 1) !important;">
            {{ __('Please use with country code. (ex: +966)') }}
        </div>
    </div>
</div>
