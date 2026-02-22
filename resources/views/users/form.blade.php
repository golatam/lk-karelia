<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <div class="form__group form__group--input">
                @if($model->exists)
                    {!! $avatar !!}
                @endif
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="first_name">@lang("{$entity}.first_name")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="first_name" id="first_name" class="form__input form__input--large @error('first_name'){{ 'is-invalid' }}@enderror" value="{{ old('first_name') ?? $model->first_name }}" required autofocus>
                        </div>
                        <div class="col-xs-12">
                        @error('first_name')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="second_name">@lang("{$entity}.second_name")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="second_name" class="form__input form__input--large @error('second_name'){{ 'is-invalid' }}@enderror" id="second_name" value="{{ old('second_name') ?? $model->second_name }}">
                        </div>
                        <div class="col-xs-12">
                        @error('second_name')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="last_name">@lang("{$entity}.last_name")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="last_name" class="form__input form__input--large @error('last_name'){{ 'is-invalid' }}@enderror" id="last_name" value="{{ old('last_name') ?? $model->last_name }}">
                        </div>
                        <div class="col-xs-12">
                        @error('last_name')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="email">@lang("{$entity}.email")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="email" name="email" class="form__input form__input--large @error('email'){{ 'is-invalid' }}@enderror" id="email" value="{{ old('email') ?? $model->email }}" required>
                        </div>
                        <div class="col-xs-12">
                            @error('email')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="phone">@lang("{$entity}.phone")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="phone" class="form__input form__input--large @error('phone'){{ 'is-invalid' }}@enderror" id="phone" value="{{ old('phone') ?? $model->phone }}">
                        </div>
                        <div class="col-xs-12">
                            @error('phone')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="municipality_id">@lang("{$entity}.municipality_id")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <select name="municipality_id" id="municipality_id" class="form__select form__select--large @error('municipality_id'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($municipalities as $key => $municipalityName)
                                    <option value="{{ $key }}" @if ((int) $model->municipality_id === (int) $key || (int) $key === (int) old('municipality_id')){{ 'selected' }}@endif>{{ $municipalityName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                        @error('municipality_id')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="municipality_chief">@lang("{$entity}.municipality_chief")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="municipality_chief" id="municipality_chief" class="form__input form__input--large @error('municipality_chief'){{ 'is-invalid' }}@enderror" value="{{ old('municipality_chief') ?? $model->municipality_chief }}">
                        </div>
                        <div class="col-xs-12">
                        @error('municipality_chief')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="municipality_phone">@lang("{$entity}.municipality_phone")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="municipality_phone" id="municipality_phone" class="form__input form__input--large @error('municipality_phone'){{ 'is-invalid' }}@enderror" value="{{ old('municipality_phone') ?? $model->municipality_phone }}">
                        </div>
                        <div class="col-xs-12">
                        @error('municipality_phone')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="municipality_email">@lang("{$entity}.municipality_email")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="municipality_email" id="municipality_email" class="form__input form__input--large @error('municipality_email'){{ 'is-invalid' }}@enderror" value="{{ old('municipality_email') ?? $model->municipality_email }}">
                        </div>
                        <div class="col-xs-12">
                        @error('municipality_email')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="municipality_address">@lang("{$entity}.municipality_address")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="municipality_address" id="municipality_address" class="form__input form__input--large @error('municipality_address'){{ 'is-invalid' }}@enderror" value="{{ old('municipality_address') ?? $model->municipality_address }}">
                        </div>
                        <div class="col-xs-12">
                        @error('municipality_address')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="executor">@lang("{$entity}.executor")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="executor" id="executor" class="form__input form__input--large @error('executor'){{ 'is-invalid' }}@enderror" value="{{ old('executor') ?? $model->executor }}">
                        </div>
                        <div class="col-xs-12">
                        @error('executor')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="executor_phone">@lang("{$entity}.executor_phone")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="executor_phone" id="executor_phone" class="form__input form__input--large @error('executor_phone'){{ 'is-invalid' }}@enderror" value="{{ old('executor_phone') ?? $model->executor_phone }}">
                        </div>
                        <div class="col-xs-12">
                        @error('executor_phone')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="executor_email">@lang("{$entity}.executor_email")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="text" name="executor_email" id="executor_email" class="form__input form__input--large @error('executor_email'){{ 'is-invalid' }}@enderror" value="{{ old('executor_email') ?? $model->executor_email }}">
                        </div>
                        <div class="col-xs-12">
                        @error('executor_email')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="password">@lang("{$entity}.password")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
                            <input type="password" name="password" class="form__input form__input--large @error('password'){{ 'is-invalid' }}@enderror" id="password" @if (!$model->exists){{ 'required' }}@endif>
                        </div>
                        <div class="col-xs-12">
                        @error('password')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="roles">@lang("{$entity}.roles")</label>
                        </div>
{{--                        @dd($model->roles->toArray(), [old('roles')], array_merge($model->roles->toArray(), [old('roles')])))--}}
                        <div class="col-xs-12 col-sm-9">
                            <select name="roles[]" id="roles" class="form__select form__select--large @error('roles'){{ 'is-invalid' }}@enderror" required multiple>
                                <option value="">@lang("common.select")</option>
                                @foreach ($roles as $key => $roleName)
                                <option value="{{ $key }}" @if (in_array($key, array_merge($model->roles->pluck('id')->toArray(), [old('roles')]))){{ 'selected' }}@endif>{{ $roleName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                        @error('roles')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-3 text-sm-right">
                            <label class="form__label form__label--sm-left" for="is_active">@lang("{$entity}.is_active")</label>
                        </div>
                        <div class="col-xs-12 col-sm-9">
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
