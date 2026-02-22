<input type="text" name="value" class="form__input form__input--large @error('value'){{ 'is-invalid' }}@enderror" id="value" value="{{ old('value') ?? $model->value }}">
