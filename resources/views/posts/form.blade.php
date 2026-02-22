<p>
    Перед заполнением формы ознакомьтесь с
    <a href="https://xn----7sbbgrnaabetoq4cya5d0ewd.xn--p1ai/instrukcziya-po-dobavleniyu-proektov/" target="_blank" rel="noopener" title="инструкцией">инструкцией</a>
    по добавлению проекта.
</p>
{{--@dd(old('acf.project'));--}}
{{--@dd(session()->all(), old('acf.district'))--}}
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <div class="form__group form__group--input">
{{--                    @dd("{$entity}.partial.image")--}}
                    @includeIf("{$entity}.partial.image")
                </div>
                {{-- Название проекта --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="title.rendered">@lang("{$entity}.title.rendered")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="title[rendered]" id="title.rendered" class="form__input form__input--large @error('title.rendered'){{ 'is-invalid' }}@enderror" value="{{ old('title.rendered', Arr::get($model, 'title.rendered', '')) }}" required autofocus>
                        </div>
                        <div class="col-xs-12">
                            @error('title.rendered')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Проект --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.project">@lang("{$entity}.acf.project")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <select name="acf[project]" id="acf.project" class="form__select form__select--large @error('project'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($projects as $project)
                                <option value="{{ $project }}" @if ((string) Arr::get($model, 'acf.project', '') === (string) $project || (string) $project === (string) old('project')){{ 'selected' }}@endif>{{ $project }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                        @error('project')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                {{-- Тип проекта --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.type_proj">@lang("{$entity}.acf.type_proj")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <select name="acf[type_proj]" id="acf.type_proj" class="form__select form__select--large @error('type_proj'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($projectTypes as $projectType)
                                    <option value="{{ $projectType }}" @if ((string) Arr::get($model, 'acf.type_proj', '') === (string) $projectType || (string) $projectType === (string) old('type_proj')){{ 'selected' }}@endif>{{ $projectType }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                            @error('type_proj')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Координаты реализации проекта --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.coordinates">@lang("{$entity}.acf.coordinates")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="acf[coordinates]" id="acf.coordinates" class="js-coordinates form__input form__input--large @error('acf.coordinates'){{ 'is-invalid' }}@enderror" value="{{ old('acf.coordinates', Arr::get($model, 'acf.coordinates', '')) }}">
                        </div>
                        <div class="col-xs-12 col-sm-2 text-sm-right">

                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <div class="form__message">
{{--                                1) Откройте Яндекс. Карты (https://yandex.ru/maps/?ll=33.434026%2C63.656565&z=6.59)--}}
{{--                                2) Найдите точку на карте и нажмите правой кнопкой мыши.--}}
{{--                                3) Нажмите "Что здесь?" и сверху справа отобразятся координаты.--}}
{{--                                4) Один раз кликните по координатам левой кнопкой мыши и они будут скопированы.--}}
{{--                                Вставьте их в это поле.--}}
                                Кликните по карте или перетащите маркер на карте в нужную точку чтобы получить Ваши координаты
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('acf.coordinates')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                {{-- Карта --}}
                @includeIf("{$entity}.partial.map")
                {{-- Описание проекта --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.description">@lang("{$entity}.acf.description")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <textarea name="acf[description]" id="acf.description" class="form__textarea form__textarea--medium" @error('project'){{ 'is-invalid' }}@enderror placeholder="@lang("{$entity}.acf.description")">{{ old('acf.description', Arr::get($model, 'acf.description', '')) }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('acf.description')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Бюджет (общий) --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.budget">@lang("{$entity}.acf.budget")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="acf[budget]" id="acf.budget" class="form__input form__input--large @error('acf.budget'){{ 'is-invalid' }}@enderror" value="{{ old('acf.budget', Arr::get($model, 'acf.budget', '')) }}">
                        </div>
                        <div class="col-xs-12">
                            @error('acf.budget')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Бюджет РК --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.budget_rk">@lang("{$entity}.acf.budget_rk")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="acf[budget_rk]" id="acf.budget_rk" class="form__input form__input--large @error('acf.budget_rk'){{ 'is-invalid' }}@enderror" value="{{ old('acf.budget_rk', Arr::get($model, 'acf.budget_rk', '')) }}">
                        </div>
                        <div class="col-xs-12">
                            @error('acf.budget_rk')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Бюджет привлекаемый (софинансирование) --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.budget_priv">@lang("{$entity}.acf.budget_priv")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="acf[budget_priv]" id="acf.budget_priv" class="form__input form__input--large @error('acf.budget_priv'){{ 'is-invalid' }}@enderror" value="{{ old('acf.budget_priv', Arr::get($model, 'acf.budget_priv', '')) }}">
                        </div>
                        <div class="col-xs-12">
                            @error('acf.budget_priv')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Бюджет местный (МО) --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.budget_mo">@lang("{$entity}.acf.budget_mo")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" name="acf[budget_mo]" id="acf.budget_mo" class="form__input form__input--large @error('acf.budget_mo'){{ 'is-invalid' }}@enderror" value="{{ old('acf.budget_mo', Arr::get($model, 'acf.budget_mo', '')) }}">
                        </div>
                        <div class="col-xs-12">
                            @error('acf.budget_mo')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Муниципальное образование --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label" for="acf.district">@lang("{$entity}.acf.district")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <select @if(auth()->user()->isShowComittee()) disabled @endif name="acf[district]" id="acf.district" class="form__select form__select--large @error('acf.district'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality }}" @if ((string) Arr::get($model, 'acf.district', 0) === (string) $municipality || (string) $municipality === (string) old('acf.district')){{ 'selected' }}@endif>{{ $municipality }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                            @error('acf.district')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Год проекта --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.year">@lang("{$entity}.acf.year")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="number" maxlength="4" name="acf[year]" class="form__input form__input--large @error('acf.year'){{ 'is-invalid' }}@enderror" id="acf.year" value="{{ old('acf.year', Arr::get($model, 'acf.year', '')) }}">
                        </div>
                        <div class="col-xs-12">
                            @error('acf.year')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                {{-- Ваш e-mail --}}
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-2 text-sm-right">
                            <label class="form__label form__label--sm-left" for="acf.email">@lang("{$entity}.acf.email")</label>
                        </div>
                        <div class="col-xs-12 col-sm-10">
                            <input type="text" maxlength="4" name="acf[email]" class="form__input form__input--large @error('acf.email'){{ 'is-invalid' }}@enderror" id="acf.email" value="{{ Arr::get($model, 'acf.email', null) ?? auth()->user()->email }}" readonly>
                        </div>
                        <div class="col-xs-12">
                            @error('acf.email')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
