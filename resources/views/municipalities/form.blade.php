<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="type">@lang("{$entity}.type")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <select name="type" id="type" class="form__select form__select--large @error('type'){{ 'is-invalid' }}@enderror" required>
                                <option value="">@lang("common.select")</option>
                                @foreach ($types as $key => $type)
                                <option value="{{ $key }}" @if ((string) $model->type === (string) $key || (string) $key === (string) old('type')){{ 'selected' }}@endif>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                        @error('type')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="name">@lang("{$entity}.name")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="name" class="form__input form__input--large @error('name'){{ 'is-invalid' }}@enderror" id="name" value="{{ old('name') ?? $model->name }}" required autofocus>
                        </div>
                        <div class="col-xs-12">
                        @error('name')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="year_of_competition">@lang("{$entity}.year_of_competition")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="number" maxlength="4" name="year_of_competition" class="form__input form__input--large @error('year_of_competition'){{ 'is-invalid' }}@enderror" id="year_of_competition" value="{{ old('year_of_competition') ?? $model->year_of_competition }}" required autofocus>
                        </div>
                        <div class="col-xs-12">
                        @error('year_of_competition')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="end_date_active">@lang("{$entity}.end_date_active")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="end_date_active" id="end_date_active" class="form__input form__input--large form__date @error('end_date_active'){{ 'is-invalid' }}@enderror" value="{{ old('end_date_active') ?? $model->end_date_active }}">
                        </div>
                        <div class="col-xs-12">
                        @error('end_date_active')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="is_active">@lang("{$entity}.is_active")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <label class="form__toggle @error('is_active'){{ 'is-invalid' }}@enderror">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form__toggle-input" type="checkbox" id="is_active" name="is_active" value="1" @if ($model->is_active){{ 'checked' }}@endif>
                                <span class="form__toggle-icon"></span>
                            </label>
                        </div>
                        <div class="col-xs-12">
                        @error('is_active')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
