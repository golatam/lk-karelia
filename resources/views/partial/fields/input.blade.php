<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12 col-sm-2 text-sm-right">
            <label class="form__label form__label--sm-left" for="{{ $field_name }}">
                @lang("{$entity}.{$field_name}")
            </label>
        </div>
        <div class="col-xs-12 col-sm-10">
            <input type="{{$type}}" name="{{$field_name}}" class="form__input form__input--large
                    @error($field_name){{ 'is-invalid' }}@enderror"
                    id="{{$field_name}}"
                   value="{{ old($field_name) ?? $field_value }}"
                   @if(isset($required) && $required) required @endif
                   @if(isset($autofocus) && $autofocus) autofocus @endif
            >
        </div>
        <div class="col-xs-12">
        @error($field_name)
            <div class="form__message form__message--error">{{ $message }}</div>
        @enderror
        </div>
    </div>
</div>
