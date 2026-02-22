<textarea name="value" class="form__tinymce @error('value'){{ 'is-invalid' }}@enderror" id="value">{{ old('value') ?? $model->value }}</textarea>
