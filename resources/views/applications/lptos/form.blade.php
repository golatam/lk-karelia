<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <input type="hidden" name="contest_id" value="{{ $model->exists ? $model->contest_id : $contest->id }}">
                <input type="hidden" name="user_id" value="{{ $model->exists ? $model->user_id : $user->id }}">
                @if($model->exists)
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12 col-sm-6 text-sm-right">
                            <label class="form__label form__label--sm-left" for="status">@lang("{$entity}_applications.status"):</label>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="form__element form__element--large">
                                <div>{{ config("app.{$entity}_applications.statuses.{$model->status}", '---') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="contest_nomination">@lang("{$entity}_applications.contest_nomination")</label>
                        </div>
                        <div class="col-xs-12">
                            <select @if(auth()->user()->isShowComittee()) disabled @endif name="contest_nomination" id="contest_nomination" class="form__select form__select--large @error('contest_nomination'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($nominations as $key => $nomination)
                                    <option value="{{ $key }}" @if ((int) $model->contest_nomination === (int) $key || (int) $key === (string) old('contest_nomination')){{ 'selected' }}@endif>{{ $nomination }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                            @error('contest_nomination')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="category">@lang("{$entity}_applications.category")</label>
                        </div>
                        <div class="col-xs-12">
                            <select @if(auth()->user()->isShowComittee()) disabled @endif name="category" id="category" class="form__select form__select--large @error('category'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($categories as $key => $category)
                                    <option value="{{ $key }}" @if ((int) $model->category === (int) $key || (int) $key === (string) old('category')){{ 'selected' }}@endif>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                            @error('category')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="municipality_id">@lang("{$entity}_applications.municipality_id")</label>
                        </div>
                        <div class="col-xs-12">
                            <select @if(auth()->user()->isShowComittee()) disabled @endif name="municipality_id" id="municipality_id" class="form__select form__select--large @error('municipality_id'){{ 'is-invalid' }}@enderror" required>
                                <option value="">@lang("common.select")</option>
                                @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality->id }}" @if ((int) $model->municipality_id === (int) $municipality->id || (int) $municipality->id === (string) old('municipality_id')){{ 'selected' }}@endif>{{ $municipality->name }}</option>
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
                        <div class="col-xs-12">
                            <label class="form__label" for="register_id">@lang("{$entity}_applications.register_id")</label>
                        </div>
                        <div class="col-xs-12">
                            <select @if(auth()->user()->isShowComittee()) disabled @endif name="register_id" id="register_id" class="form__select form__select--large @error('register_id'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($model->tosNames as $key => $tosName)
                                    <option value="{{ $key }}" @if ((int) $model->register_id === (int) $key || (int) $key === (string) old('register_id')){{ 'selected' }}@endif>{{ $tosName }}</option>
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
                        <div class="col-xs-12">
                            <label class="form__label" for="nomenclature_number">@lang("{$entity}_applications.nomenclature_number")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="nomenclature_number" id="nomenclature_number" class="form__input form__input--large @error('nomenclature_number'){{ 'is-invalid' }}@enderror" value="{{ old('nomenclature_number') ?? $model->nomenclature_number }}">
                        </div>
                        <div class="col-xs-12">
                            @error('nomenclature_number')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="date_registration_charter">@lang("{$entity}_applications.date_registration_charter")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="date_registration_charter" id="date_registration_charter" class="form__input form__input--large form__date @error('date_registration_charter'){{ 'is-invalid' }}@enderror" value="{{ old('date_registration_charter') ?? $model->date_registration_charter }}">
                        </div>
                        <div class="col-xs-12">
                            @error('date_registration_charter')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="population_size_in_tos">@lang("{$entity}_applications.population_size_in_tos")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="number" placeholder="0" name="population_size_in_tos" id="population_size_in_tos" class="form__input form__input--large @error('population_size_in_tos'){{ 'is-invalid' }}@enderror" value="{{ old('population_size_in_tos') ?? $model->population_size_in_tos }}">
                        </div>
                        <div class="col-xs-12">
                            @error('population_size_in_tos')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="number_beneficiaries">@lang("{$entity}_applications.number_beneficiaries")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="number" placeholder="0" name="number_beneficiaries" id="number_beneficiaries" class="form__input form__input--large @error('number_beneficiaries'){{ 'is-invalid' }}@enderror" value="{{ old('number_beneficiaries') ?? $model->number_beneficiaries }}">
                        </div>
                        <div class="col-xs-12">
                            @error('number_beneficiaries')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="full_name_chairman_tos">@lang("{$entity}_applications.full_name_chairman_tos")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="full_name_chairman_tos" id="full_name_chairman_tos" class="form__input form__input--large @error('full_name_chairman_tos'){{ 'is-invalid' }}@enderror" value="{{ old('full_name_chairman_tos') ?? $model->full_name_chairman_tos }}">
                        </div>
                        <div class="col-xs-12">
                            @error('full_name_chairman_tos')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="tos_address">@lang("{$entity}_applications.tos_address")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="tos_address" id="tos_address" class="form__input form__input--large @error('tos_address'){{ 'is-invalid' }}@enderror" value="{{ old('tos_address') ?? $model->tos_address }}">
                        </div>
                        <div class="col-xs-12">
                            @error('tos_address')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="tos_phone">@lang("{$entity}_applications.tos_phone")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="tos_phone" id="tos_phone" class="form__input form__input--large @error('tos_phone'){{ 'is-invalid' }}@enderror" value="{{ old('tos_phone') ?? $model->tos_phone }}">
                        </div>
                        <div class="col-xs-12">
                            @error('tos_phone')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="tos_email">@lang("{$entity}_applications.tos_email")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="tos_email" id="tos_email" class="form__input form__input--large @error('tos_email'){{ 'is-invalid' }}@enderror" value="{{ old('tos_email') ?? $model->tos_email }}">
                        </div>
                        <div class="col-xs-12">
                            @error('tos_email')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="is_tos_legal_entity">@lang("{$entity}_applications.is_tos_legal_entity")</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="form__toggle @error('is_tos_legal_entity'){{ 'is-invalid' }}@enderror">
                                <input type="hidden" name="is_tos_legal_entity" value="0">
                                <input @if(auth()->user()->isShowComittee()) disabled @endif class="form__toggle-input" type="checkbox" id="is_tos_legal_entity" name="is_tos_legal_entity" value="1" @if ($model->is_tos_legal_entity){{ 'checked' }}@endif>
                                <span class="form__toggle-icon"></span>
                            </label>
                        </div>
                        <div class="col-xs-12">
                            @error('is_tos_legal_entity')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="has_legal_entity">@lang("{$entity}_applications.has_legal_entity")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="registration_date_tos">@lang("{$entity}_applications.registration_date_tos")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="registration_date_tos" id="registration_date_tos" class="form__input form__input--large form__date @error('registration_date_tos'){{ 'is-invalid' }}@enderror" value="{{ old('registration_date_tos') ?? $model->registration_date_tos }}">
                        </div>
                        <div class="col-xs-12">
                            @error('registration_date_tos')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="ogrn">@lang("{$entity}_applications.ogrn")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="ogrn" id="ogrn" class="form__input form__input--large @error('ogrn'){{ 'is-invalid' }}@enderror" value="{{ old('ogrn') ?? $model->ogrn }}">
                        </div>
                        <div class="col-xs-12">
                            @error('ogrn')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="inn">@lang("{$entity}_applications.inn")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="inn" id="inn" class="form__input form__input--large @error('inn'){{ 'is-invalid' }}@enderror" value="{{ old('inn') ?? $model->inn }}">
                        </div>
                        <div class="col-xs-12">
                            @error('inn')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="kpp">@lang("{$entity}_applications.kpp")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="kpp" id="kpp" class="form__input form__input--large @error('kpp'){{ 'is-invalid' }}@enderror" value="{{ old('kpp') ?? $model->kpp }}">
                        </div>
                        <div class="col-xs-12">
                            @error('kpp')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="bank_details">@lang("{$entity}_applications.bank_details")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="bank_details" id="bank_details" class="form__textarea form__textarea--large @error('bank_details'){{ 'is-invalid' }}@enderror">{{ old('bank_details') ?? $model->bank_details }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('bank_details')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="does_your_tos_have">@lang("{$entity}_applications.does_your_tos_have")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="website">@lang("{$entity}_applications.website")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="website" id="website" class="form__input form__input--large @error('website'){{ 'is-invalid' }}@enderror" value="{{ old('website') ?? $model->website }}">
                        </div>
                        <div class="col-xs-12">
                            @error('website')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="vk">@lang("{$entity}_applications.vk")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="vk" id="vk" class="form__input form__input--large @error('vk'){{ 'is-invalid' }}@enderror" value="{{ old('vk') ?? $model->vk }}">
                        </div>
                        <div class="col-xs-12">
                            @error('vk')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="ok">@lang("{$entity}_applications.ok")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="ok" id="ok" class="form__input form__input--large @error('ok'){{ 'is-invalid' }}@enderror" value="{{ old('ok') ?? $model->ok }}">
                        </div>
                        <div class="col-xs-12">
                            @error('ok')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="fb">@lang("{$entity}_applications.fb")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="fb" id="fb" class="form__input form__input--large @error('fb'){{ 'is-invalid' }}@enderror" value="{{ old('fb') ?? $model->fb }}">
                        </div>
                        <div class="col-xs-12">
                            @error('fb')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="twitter">@lang("{$entity}_applications.twitter")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="twitter" id="twitter" class="form__input form__input--large @error('twitter'){{ 'is-invalid' }}@enderror" value="{{ old('twitter') ?? $model->twitter }}">
                        </div>
                        <div class="col-xs-12">
                            @error('twitter')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="instagram">@lang("{$entity}_applications.instagram")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="instagram" id="instagram" class="form__input form__input--large @error('instagram'){{ 'is-invalid' }}@enderror" value="{{ old('instagram') ?? $model->instagram }}">
                        </div>
                        <div class="col-xs-12">
                            @error('instagram')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="practice_name">@lang("{$entity}_applications.practice_name")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="practice_name" id="practice_name" class="form__input form__input--large @error('practice_name'){{ 'is-invalid' }}@enderror" value="{{ old('practice_name') ?? $model->practice_name }}">
                        </div>
                        <div class="col-xs-12">
                            @error('practice_name')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="practice_purpose">@lang("{$entity}_applications.practice_purpose")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="practice_purpose" id="practice_purpose" class="form__textarea form__textarea--large @error('practice_purpose'){{ 'is-invalid' }}@enderror">{{ old('practice_purpose') ?? $model->practice_purpose }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('practice_purpose')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="practice_tasks">@lang("{$entity}_applications.practice_tasks")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="practice_tasks" id="practice_tasks" class="form__textarea form__textarea--large @error('practice_tasks'){{ 'is-invalid' }}@enderror">{{ old('practice_tasks') ?? $model->practice_tasks }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('practice_tasks')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="duration_practice">@lang("{$entity}_applications.duration_practice")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="duration_practice" id="duration_practice" class="form__input form__input--large form__date @error('duration_practice'){{ 'is-invalid' }}@enderror" value="{{ old('duration_practice') ?? $model->duration_practice }}">
                        </div>
                        <div class="col-xs-12">
                            @error('duration_practice')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="practice_implementation_geography">@lang("{$entity}_applications.practice_implementation_geography")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="practice_implementation_geography" id="practice_implementation_geography" class="form__textarea form__textarea--large @error('practice_implementation_geography'){{ 'is-invalid' }}@enderror">{{ old('practice_implementation_geography') ?? $model->practice_implementation_geography }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('practice_implementation_geography')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="activity_social_significance">@lang("{$entity}_applications.activity_social_significance")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="activity_social_significance" id="activity_social_significance" class="form__textarea form__textarea--large @error('activity_social_significance'){{ 'is-invalid' }}@enderror">{{ old('activity_social_significance') ?? $model->activity_social_significance }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('activity_social_significance')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="problem_description">@lang("{$entity}_applications.problem_description")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="problem_description" id="problem_description" class="form__textarea form__textarea--large @error('problem_description'){{ 'is-invalid' }}@enderror">{{ old('problem_description') ?? $model->problem_description }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('problem_description')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="number_people_part_in_project_implementation">@lang("{$entity}_applications.number_people_part_in_project_implementation")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="number" placeholder="0" name="number_people_part_in_project_implementation" id="number_people_part_in_project_implementation" class="form__input form__input--large @error('number_people_part_in_project_implementation'){{ 'is-invalid' }}@enderror" value="{{ old('number_people_part_in_project_implementation') ?? $model->number_people_part_in_project_implementation }}">
                        </div>
                        <div class="col-xs-12">
                            @error('number_people_part_in_project_implementation')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="list_documents_regulating_activity">@lang("{$entity}_applications.list_documents_regulating_activity")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.lptos.list_documents_regulating_activity-matrix", ['fieldName' => 'list_documents_regulating_activity', 'models' => $model->list_documents_regulating_activity, 'fields' => config("app.{$entity}_applications.matrix.list_documents_regulating_activity", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('list_documents_regulating_activity')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="implementation_resources">@lang("{$entity}_applications.implementation_resources")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="implementation_resources_involved_practice_own">@lang("{$entity}_applications.implementation_resources_involved_practice_own")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="implementation_resources_involved_practice_own" id="implementation_resources_involved_practice_own" class="form__input form__input--large @error('implementation_resources_involved_practice_own'){{ 'is-invalid' }}@enderror" value="{{ old('implementation_resources_involved_practice_own') ?? $model->implementation_resources_involved_practice_own }}">
                        </div>
                        <div class="col-xs-12">
                            @error('implementation_resources_involved_practice_own')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="implementation_resources_involved_practice_budget">@lang("{$entity}_applications.implementation_resources_involved_practice_budget")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="implementation_resources_involved_practice_budget" id="implementation_resources_involved_practice_budget" class="form__input form__input--large @error('implementation_resources_involved_practice_budget'){{ 'is-invalid' }}@enderror" value="{{ old('implementation_resources_involved_practice_budget') ?? $model->implementation_resources_involved_practice_budget }}">
                        </div>
                        <div class="col-xs-12">
                            @error('implementation_resources_involved_practice_budget')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="implementation_resources_involved_practice_other">@lang("{$entity}_applications.implementation_resources_involved_practice_other")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="implementation_resources_involved_practice_other" id="implementation_resources_involved_practice_other" class="form__textarea form__textarea--large @error('implementation_resources_involved_practice_other'){{ 'is-invalid' }}@enderror">{{ old('implementation_resources_involved_practice_other') ?? $model->implementation_resources_involved_practice_other }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('implementation_resources_involved_practice_other')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="achieved_results">@lang("{$entity}_applications.achieved_results")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea @if(auth()->user()->isShowComittee()) disabled @endif name="achieved_results" id="achieved_results" class="form__textarea form__textarea--large @error('achieved_results'){{ 'is-invalid' }}@enderror">{{ old('achieved_results') ?? $model->achieved_results }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('achieved_results')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @if(auth()->user()->hasPermissions(['other.show_admin']))
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="total_application_points">@lang("{$entity}_applications.total_application_points")</label>
                        </div>
                        <div class="col-xs-12">
                            <div class="form__element form__element--large">
                                <div>{{ $model->total_application_points }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
