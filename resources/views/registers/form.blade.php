<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="name_region">@lang("{$entity}.name_region")</label>
                        </div>
                        <div class="col-xs-12">
                            <select name="name_region" id="name_region" class="form__select form__select--medium @error('name_region'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($regions as $region)
                                <option value="{{ $region->id }}" @if ((int) $model->name_region === (int) $region->id || (int) $region->id === (string) old('name_region')){{ 'selected' }}@endif>{{ $region->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                        @error('name_region')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="name_settlement">@lang("{$entity}.name_settlement")</label>
                        </div>
                        <div class="col-xs-12">
                            <select name="name_settlement" id="name_settlement" class="form__select form__select--medium @error('name_settlement'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($settlements as $settlement)
                                <option value="{{ $settlement->id }}" @if ((int) $model->name_settlement === (int) $settlement->id || (int) $settlement->id === (string) old('name_settlement')){{ 'selected' }}@endif>{{ $settlement->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                        @error('name_settlement')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="name_according_charter">@lang("{$entity}.name_according_charter")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="name_according_charter" id="name_according_charter" class="form__input form__input--medium @error('name_according_charter'){{ 'is-invalid' }}@enderror" value="{{ old('name_according_charter') ?? $model->name_according_charter }}">
                        </div>
                        <div class="col-xs-12">
                        @error('name_according_charter')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="is_legal_entity">@lang("{$entity}.is_legal_entity")</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="form__toggle @error('is_legal_entity'){{ 'is-invalid' }}@enderror">
                                <input type="hidden" name="is_legal_entity" value="0">
                                <input class="form__toggle-input" type="checkbox" id="is_legal_entity" name="is_legal_entity" value="1" @if ($model->is_legal_entity){{ 'checked' }}@endif>
                                <span class="form__toggle-icon"></span>
                            </label>
                        </div>
                        <div class="col-xs-12">
                        @error('is_legal_entity')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="membership">@lang("{$entity}.membership")</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="form__toggle @error('membership'){{ 'is-invalid' }}@enderror">
                                <input type="hidden" name="membership" value="0">
                                <input class="form__toggle-input" type="checkbox" id="membership" name="membership" value="1" @if ($model->membership){{ 'checked' }}@endif>
                                <span class="form__toggle-icon"></span>
                            </label>
                        </div>
                        <div class="col-xs-12">
                        @error('membership')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="address">@lang("{$entity}.address")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="address" id="address" class="form__textarea form__textarea--medium @error('address'){{ 'is-invalid' }}@enderror">{{ old('address') ?? $model->address }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('address')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="inn">@lang("{$entity}.inn")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="inn" id="inn" class="form__input form__input--medium @error('inn'){{ 'is-invalid' }}@enderror" value="{{ old('inn') ?? $model->inn }}">
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
                            <label class="form__label" for="kpp">@lang("{$entity}.kpp")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="kpp" id="kpp" class="form__input form__input--medium @error('kpp'){{ 'is-invalid' }}@enderror" value="{{ old('kpp') ?? $model->kpp }}">
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
                            <label class="form__label" for="ogrn">@lang("{$entity}.ogrn")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="ogrn" id="ogrn" class="form__input form__input--medium @error('ogrn'){{ 'is-invalid' }}@enderror" value="{{ old('ogrn') ?? $model->ogrn }}">
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
                            <label class="form__label" for="bank_details">@lang("{$entity}.bank_details")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="bank_details" id="bank_details" class="form__textarea form__textarea--medium @error('bank_details'){{ 'is-invalid' }}@enderror">{{ old('bank_details') ?? $model->bank_details }}</textarea>
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
                            <label class="form__label" for="site">@lang("{$entity}.site")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="site" id="site" class="form__input form__input--medium @error('site'){{ 'is-invalid' }}@enderror" value="{{ old('site') ?? $model->site }}">
                        </div>
                        <div class="col-xs-12">
                        @error('site')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="vk">@lang("{$entity}.vk")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="vk" id="vk" class="form__input form__input--medium @error('vk'){{ 'is-invalid' }}@enderror" value="{{ old('vk') ?? $model->vk }}">
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
                            <label class="form__label" for="ok">@lang("{$entity}.ok")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="ok" id="ok" class="form__input form__input--medium @error('ok'){{ 'is-invalid' }}@enderror" value="{{ old('ok') ?? $model->ok }}">
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
                            <label class="form__label" for="fb">@lang("{$entity}.fb")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="fb" id="fb" class="form__input form__input--medium @error('fb'){{ 'is-invalid' }}@enderror" value="{{ old('fb') ?? $model->fb }}">
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
                            <label class="form__label" for="twitter">@lang("{$entity}.twitter")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="twitter" id="twitter" class="form__input form__input--medium @error('twitter'){{ 'is-invalid' }}@enderror" value="{{ old('twitter') ?? $model->twitter }}">
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
                            <label class="form__label" for="instagram">@lang("{$entity}.instagram")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="instagram" id="instagram" class="form__input form__input--medium @error('instagram'){{ 'is-invalid' }}@enderror" value="{{ old('instagram') ?? $model->instagram }}">
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
                            <label class="form__label" for="boundaries">@lang("{$entity}.boundaries")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="boundaries" id="boundaries" class="form__textarea form__textarea--medium @error('boundaries'){{ 'is-invalid' }}@enderror">{{ old('boundaries') ?? $model->boundaries }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('boundaries')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="legal_act">@lang("{$entity}.legal_act")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="legal_act" id="legal_act" class="form__textarea form__textarea--medium @error('legal_act'){{ 'is-invalid' }}@enderror">{{ old('legal_act') ?? $model->legal_act }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('legal_act')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="registration_date_charter">@lang("{$entity}.registration_date_charter")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="registration_date_charter" id="registration_date_charter" class="form__input form__input--medium form__date @error('registration_date_charter'){{ 'is-invalid' }}@enderror" value="{{ old('registration_date_charter') ?? $model->registration_date_charter }}">
                        </div>
                        <div class="col-xs-12">
                        @error('registration_date_charter')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="registration_date_tos">@lang("{$entity}.registration_date_tos")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="registration_date_tos" id="registration_date_tos" class="form__input form__input--medium form__date @error('registration_date_tos'){{ 'is-invalid' }}@enderror" value="{{ old('registration_date_tos') ?? $model->registration_date_tos }}">
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
                            <label class="form__label" for="nomenclature_number">@lang("{$entity}.nomenclature_number")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="nomenclature_number" id="nomenclature_number" class="form__input form__input--medium @error('nomenclature_number'){{ 'is-invalid' }}@enderror" value="{{ old('nomenclature_number') ?? $model->nomenclature_number }}">
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
                            <label class="form__label" for="number_members">@lang("{$entity}.number_members")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" name="number_members" id="number_members" class="form__input form__input--medium @error('number_members'){{ 'is-invalid' }}@enderror" value="{{ old('number_members') ?? $model->number_members }}">
                        </div>
                        <div class="col-xs-12">
                        @error('number_members')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="number_citizens">@lang("{$entity}.number_citizens")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" name="number_citizens" id="number_citizens" class="form__input form__input--medium @error('number_citizens'){{ 'is-invalid' }}@enderror" value="{{ old('number_citizens') ?? $model->number_citizens }}">
                        </div>
                        <div class="col-xs-12">
                        @error('number_citizens')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="fio_chief">@lang("{$entity}.fio_chief")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="fio_chief" id="fio_chief" class="form__input form__input--medium @error('fio_chief'){{ 'is-invalid' }}@enderror" value="{{ old('fio_chief') ?? $model->fio_chief }}">
                        </div>
                        <div class="col-xs-12">
                        @error('fio_chief')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="email_chief">@lang("{$entity}.email_chief")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="email_chief" id="email_chief" class="form__input form__input--medium @error('email_chief'){{ 'is-invalid' }}@enderror" value="{{ old('email_chief') ?? $model->email_chief }}">
                        </div>
                        <div class="col-xs-12">
                        @error('email_chief')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="phone_chief">@lang("{$entity}.phone_chief")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="phone_chief" id="phone_chief" class="form__input form__input--medium @error('phone_chief'){{ 'is-invalid' }}@enderror" value="{{ old('phone_chief') ?? $model->phone_chief }}">
                        </div>
                        <div class="col-xs-12">
                        @error('phone_chief')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="note">@lang("{$entity}.note")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="note" id="note" class="form__textarea form__textarea--medium @error('note'){{ 'is-invalid' }}@enderror">{{ old('note') ?? $model->note }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('note')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label" for="date_tos_was_added_to_registry">@lang("{$entity}.date_tos_was_added_to_registry")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="date_tos_was_added_to_registry" id="date_tos_was_added_to_registry" class="form__input form__input--medium form__date @error('date_tos_was_added_to_registry'){{ 'is-invalid' }}@enderror" value="{{ old('date_tos_was_added_to_registry') ?? $model->date_tos_was_added_to_registry }}">
                        </div>
                        <div class="col-xs-12">
                            @error('date_tos_was_added_to_registry')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
