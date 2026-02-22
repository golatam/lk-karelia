<div class="row">
    <div class="col-xs-12">
        <div class="tabs">
            <ul class="tabs__links">
                <li>
                    <a class="tabs__link active" href="#roles">@lang("{$entity}.role")</a>
                </li>
                <li>
                    <a class="tabs__link" href="#permissions">@lang("{$entity}.permissions_name")</a>
                </li>
            </ul>
            <div class="tabs__items">
                <div class="tabs__item active" id="roles">
                    <div class="panel__body">
                        <div class="panel__content">
                            <div class="row form__group-wrap">
                                <div class="col-xs-12">
                                    <div class="form__group form__group--input">
                                        <div class="row row--small row--ai-center">
                                            <div class="col-xs-12 col-sm-2 text-sm-right">
                                                <label class="form__label form__label--sm-left" for="name">@lang("{$entity}.name")</label>
                                            </div>
                                            <div class="col-xs-12 col-sm-10">
                                                <input type="text" name="name" value="{{ old('name') ?? $model->name }}" class="form__input form__input--large @error('name'){{ 'is-invalid' }}@enderror" id="name" required>
                                            </div>
                                            <div class="col-xs-12">
                                            @error('name')
                                                <span class="form__message form__message--error">{{ $message }}</span>
                                            @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form__group form__group--input">
                                        <div class="row row--small row--ai-center">
                                            <div class="col-xs-12 col-sm-2 text-sm-right">
                                                <label class="form__label form__label--sm-left" for="alias">@lang("{$entity}.alias")</label>
                                            </div>
                                            <div class="col-xs-12 col-sm-10">
                                                <input type="text" name="alias" value="{{ old('alias') ?? $model->alias }}" class="form__input form__input--large @error('alias'){{ 'is-invalid' }}@enderror" id="alias" required>
                                            </div>
                                            <div class="col-xs-12">
                                            @error('alias')
                                                <span class="form__message form__message--error">{{ $message }}</span>
                                            @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form__group form__group--input">
                                        <div class="row row--small row--ai-center">
                                            <div class="col-xs-12 col-sm-2 text-sm-right">
                                                <label class="form__label form__label--sm-left" for="description">@lang("{$entity}.description")</label>
                                            </div>
                                            <div class="col-xs-12 col-sm-10">
                                                <input type="text" name="description" value="{{ old('description')  ?? $model->description }}" class="form__input form__input--large @error('description'){{ 'is-invalid' }}@enderror" id="description">
                                            </div>
                                            <div class="col-xs-12">
                                            @error('description')
                                                <span class="form__message form__message--error">{{ $message }}</span>
                                            @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tabs__item" id="permissions">
                    <div class="panel__body">
                        <div class="panel__content">
                            <div class="row form__group-wrap">
                                @foreach($permissions as $key => $permission)
                                    <div class="col-xs-12 col-sm-6 col-lg-4">
                                        <div class="form__group form__group--input">
                                            <ul class="list-group js-checks-group">
                                                <li class="list-group__item list-group__item--head">
                                                    <label class="form__checkbox">
                                                        <input class="form__checkbox-input js-checks-all" type="checkbox" name="" value="{{ $key }}" @if($model->permissionsAll($permission)){{ 'checked' }}@endif>
                                                        <span class="form__checkbox-icon"></span>
                                                        <span class="form__checkbox-label">@lang("permissions.groups.{$key}")</span>
                                                    </label>
                                                </li>
                                                @foreach($permission as $item)
                                                <li class="list-group__item">
                                                    <label class="form__checkbox">
                                                        <input class="form__checkbox-input js-checks-item" type="checkbox" name="permissions[]" value="{{ $item->id }}" @if($model->exists && in_array($item->id, $model->permissions->pluck('id')->toArray())){{ 'checked' }}@endif>
                                                        <span class="form__checkbox-icon"></span>
                                                        <span class="form__checkbox-label">{{ $item->description }}</span>
                                                    </label>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
