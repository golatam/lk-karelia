<div class="panel__head">
    <h5 class="reset-m">@lang("common.common_information")</h5>
</div>
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
                            <label class="form__label form__label--sm-left" for="settlement_id">@lang("{$entity}_applications.settlement_id") *</label>
                        </div>
                        <div class="col-xs-12">
                            <select {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="settlement_id" id="settlement_id" class="form__select form__select--large @error('settlement_id'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($settlements as $settlementKey => $settlementName)
                                    <option value="{{ $settlementKey }}" @if ((int) $model->settlement_id === (int) $settlementKey || (int) $settlementKey === (string) old('settlement_id')){{ 'selected' }}@endif>{{ $settlementName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                            @error('settlement_id')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="applicant_fio">@lang("{$entity}_applications.applicant_fio") *</label>
                        </div>
                        <div class="col-xs-12">
                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="text" name="applicant_fio" id="applicant_fio" class="form__input form__input--large @error('applicant_fio'){{ 'is-invalid' }}@enderror" value="{{ old('applicant_fio') ?? $model->applicant_fio }}">
                        </div>
                        <div class="col-xs-12">
                            @error('applicant_fio')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="applicant_position">@lang("{$entity}_applications.applicant_position") *</label>
                        </div>
                        <div class="col-xs-12">
                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="text" name="applicant_position" id="applicant_position" class="form__input form__input--large @error('applicant_position'){{ 'is-invalid' }}@enderror" value="{{ old('applicant_position') ?? $model->applicant_position }}">
                        </div>
                        <div class="col-xs-12">
                            @error('applicant_position')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="contact_details">@lang("{$entity}_applications.contact_details") *</label>
                        </div>
                        <div class="col-xs-12">
                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="text" name="contact_details" id="contact_details" class="form__input form__input--large @error('contact_details'){{ 'is-invalid' }}@enderror" value="{{ old('contact_details') ?? $model->contact_details }}" placeholder="адрес, телефон, электронная почта главы сельского поселения">
                        </div>
                        <div class="col-xs-12">
                            @error('contact_details')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @if($model->exists)
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="administration_recommendation">@lang("{$entity}_applications.administration_recommendation") * <br/><small>(загрузите скан рекомендации от администрации)</small></label>
                        </div>
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="administration_recommendation">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="file" name="administration_recommendation" id="administration_recommendation" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="administration_recommendation">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->administration_recommendation as $file)
                                            <div class="row row--small row--ai-center">
                                                <div class="col-xs-9 col-sm-10 col-md-11">
                                                    <div class="form__element form__element--large">
                                                        <a href="{{ $file->path }}" class="btn btn--full btn--text-left btn--medium btn--gray" download>
                                                            <i class="fas fa-file-{{ $file->extension }}"></i>
                                                            <span class="btn__text btn__text--right">{{ $file->name }}</span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-xs-3 col-sm-2 col-md-1">
                                                    @if(!auth()->user()->isShowComittee())
                                                    <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            @error('information_project_support')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="population_size_in_settlement">@lang("{$entity}_applications.population_size_in_settlement") * <br/><small>(на начало текущего года)</small></label>
                        </div>
                        <div class="col-xs-12">
                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="number" name="population_size_in_settlement" id="population_size_in_settlement" class="form__input form__input--large @error('population_size_in_settlement'){{ 'is-invalid' }}@enderror" value="{{ old('population_size_in_settlement') ?? $model->population_size_in_settlement }}">
                        </div>
                        <div class="col-xs-12">
                            @error('population_size_in_settlement')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="demographic_parameters">@lang("{$entity}_applications.demographic_parameters") *</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="demographic_parameters" id="demographic_parameters" class="form__textarea form__textarea--large @error('demographic_parameters'){{ 'is-invalid' }}@enderror" placeholder="указать данные органа ЗАГСа за 2 предыдущих года">{{ old('demographic_parameters') ?? $model->demographic_parameters }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('demographic_parameters')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="forms_self_organization_citizens">@lang("{$entity}_applications.forms_self_organization_citizens") </label>
                        </div>
                        <div class="col-xs-12">
                            <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="forms_self_organization_citizens" id="forms_self_organization_citizens" class="form__textarea form__textarea--large @error('forms_self_organization_citizens'){{ 'is-invalid' }}@enderror" placeholder="ТОСы, женсоветы, Советы ветеранов, молодежные объединения и др., деятельность, мероприятия">{{ old('forms_self_organization_citizens') ?? $model->forms_self_organization_citizens }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('forms_self_organization_citizens')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="landscaping">@lang("{$entity}_applications.landscaping")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="landscaping" id="landscaping" class="form__textarea form__textarea--large @error('landscaping'){{ 'is-invalid' }}@enderror" placeholder="лучший инновационный проект, уникальная идея">{{ old('landscaping') ?? $model->landscaping }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('landscaping')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="cultural_traditions">@lang("{$entity}_applications.cultural_traditions")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="cultural_traditions" id="cultural_traditions" class="form__textarea form__textarea--large @error('cultural_traditions'){{ 'is-invalid' }}@enderror" placeholder="наличие, сохранение, приобщение и непосредственное участие жителей в проводимых мероприятиях">{{ old('cultural_traditions') ?? $model->cultural_traditions }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('cultural_traditions')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@if($model->exists)
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.data_on_internet")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <x-matrix-component :model="$model" entity="{{ $entity }}_applications" fieldName="data_on_internet" field-type="input"></x-matrix-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.appearance_village")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="appearance_village_description" id="appearance_village_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.appearance_village_description")">{{ $model->appearance_village_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="appearance_village" has-description="0" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.history_village")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="history_village_description" id="history_village_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.history_village_description")">{{ $model->history_village_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="history_village" has-description="1" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.reservoirs")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="reservoirs_description" id="reservoirs_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.reservoirs_description")">{{ $model->reservoirs_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="reservoirs" has-description="0" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.natural_monuments")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="natural_monuments_description" id="natural_monuments_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.natural_monuments_description")">{{ $model->natural_monuments_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="natural_monuments" has-description="1" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.architectural_monuments")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="architectural_monuments_description" id="architectural_monuments_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.architectural_monuments_description")">{{ $model->architectural_monuments_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="architectural_monuments" has-description="1" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.illumination")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="illumination_description" id="illumination_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.illumination_description")">{{ $model->illumination_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="illumination" has-description="0" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.common_areas_and_recreation")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="common_areas_and_recreation_description" id="common_areas_and_recreation_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.common_areas_and_recreation_description")">{{ $model->common_areas_and_recreation_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="common_areas_and_recreation" has-description="0" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.artistic_expressiveness")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="artistic_expressiveness_description" id="artistic_expressiveness_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.artistic_expressiveness_description")">{{ $model->artistic_expressiveness_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="artistic_expressiveness" has-description="0" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.condition_burial_sites")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="condition_burial_sites_description" id="condition_burial_sites_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.condition_burial_sites_description")">{{ $model->condition_burial_sites_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="condition_burial_sites" has-description="0" type-description="textarea" limit=""></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.degree_population_participation") <br><small>(перечень проводимых населением мероприятий (до 5-ти мероприятий и фото))</small></h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} name="degree_population_participation_description" id="degree_population_participation_description" class="form__textarea form__textarea--large" placeholder="@lang("{$entity}_applications.degree_population_participation_description")">{{ $model->degree_population_participation_description }}</textarea>
            </div>
            <div class="col-xs-12">
                <x-images-component :model="$model" group="degree_population_participation" has-description="1" type-description="textarea" limit="5"></x-images-component>
            </div>
        </div>
    </div>
</div>
<div class="panel__head">
    <h5 class="reset-m">@lang("{$entity}_applications.cultural_events")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <x-matrix-component :model="$model" entity="{{ $entity }}_applications" fieldName="cultural_events" field-type="textarea"></x-matrix-component>
            </div>
        </div>
    </div>
</div>
@endif
@if(auth()->user()->hasPermissions(['other.show_admin']))
<div class="panel__head">
    <h5 class="reset-m">@lang("common.administrative_section")</h5>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="total_application_points">@lang("{$entity}_applications.total_application_points")</label>
                        </div>
                        <div class="col-xs-12">
                            <div class="form__element form__element--large">
                                <div>{{ $model->total_application_points ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
