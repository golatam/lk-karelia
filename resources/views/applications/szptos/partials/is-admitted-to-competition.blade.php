<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label form__label--sm-left" for="is_admitted_to_competition">@lang("{$entity}_applications.is_admitted_to_competition")</label>
        </div>
        <div class="col-xs-12">
            <label class="form__toggle @error('is_admitted_to_competition'){{ 'is-invalid' }}@enderror">
                <input type="hidden" name="is_admitted_to_competition" value="0">
                <input class="form__toggle-input" type="checkbox" id="is_admitted_to_competition" name="is_admitted_to_competition" value="1" @if ($model->is_admitted_to_competition){{ 'checked' }}@endif>
                <span class="form__toggle-icon"></span>
            </label>
        </div>
        <div class="col-xs-12">
            @error('is_admitted_to_competition')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
