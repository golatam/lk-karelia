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
                            <label class="form__label form__label--sm-left" for="register_id">@lang("{$entity}_applications.register_id")</label>
                        </div>
                        <div class="col-xs-12">
                            <select @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif name="register_id" id="register_id" class="form__select form__select--large @error('register_id'){{ 'is-invalid' }}@enderror">
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
                            <label class="form__label form__label--sm-left" for="municipality_id">@lang("{$entity}_applications.municipality_id")</label>
                        </div>
                        <div class="col-xs-12">
                            <select @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif name="municipality_id" id="municipality_id" class="form__select form__select--large @error('municipality_id'){{ 'is-invalid' }}@enderror" required>
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
                            <label class="form__label form__label--sm-left" for="date_registration_charter">@lang("{$entity}_applications.date_registration_charter")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="date_registration_charter" id="date_registration_charter" class="form__input form__input--large form__date @error('date_registration_charter'){{ 'is-invalid' }}@enderror" value="{{ old('date_registration_charter') ?? $model->date_registration_charter }}">
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
                            <label class="form__label form__label--sm-left" for="nomenclature_number">@lang("{$entity}_applications.nomenclature_number")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="nomenclature_number" id="nomenclature_number" class="form__input form__input--large @error('nomenclature_number'){{ 'is-invalid' }}@enderror" value="{{ old('nomenclature_number') ?? $model->nomenclature_number }}">
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
                            <label class="form__label form__label--sm-left" for="is_tos_legal_entity">@lang("{$entity}_applications.is_tos_legal_entity")</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="form__toggle @error('is_tos_legal_entity'){{ 'is-invalid' }}@enderror">
                                <input type="hidden" name="is_tos_legal_entity" value="0">
                                <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif class="form__toggle-input" type="checkbox" id="is_tos_legal_entity" name="is_tos_legal_entity" value="1" @if ($model->is_tos_legal_entity){{ 'checked' }}@endif>
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
                            <label class="form__label form__label--sm-left" for="full_name_chairman_tos">@lang("{$entity}_applications.full_name_chairman_tos")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="full_name_chairman_tos" id="full_name_chairman_tos" class="form__input form__input--large @error('full_name_chairman_tos'){{ 'is-invalid' }}@enderror" value="{{ old('full_name_chairman_tos') ?? $model->full_name_chairman_tos }}">
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
                            <label class="form__label form__label--sm-left" for="tos_address">@lang("{$entity}_applications.tos_address")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="tos_address" id="tos_address" class="form__input form__input--large @error('tos_address'){{ 'is-invalid' }}@enderror" value="{{ old('tos_address') ?? $model->tos_address }}">
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
                            <label class="form__label form__label--sm-left" for="tos_phone">@lang("{$entity}_applications.tos_phone")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="tos_phone" id="tos_phone" class="form__input form__input--large @error('tos_phone'){{ 'is-invalid' }}@enderror" value="{{ old('tos_phone') ?? $model->tos_phone }}">
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
                            <label class="form__label form__label--sm-left" for="tos_email">@lang("{$entity}_applications.tos_email")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="tos_email" id="tos_email" class="form__input form__input--large @error('tos_email'){{ 'is-invalid' }}@enderror" value="{{ old('tos_email') ?? $model->tos_email }}">
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
                            <label class="form__label form__label--sm-left" for="list_tos_board_members">@lang("{$entity}_applications.list_tos_board_members")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.list_tos_board_members", ['fieldName' => 'list_tos_board_members', 'models' => $model->list_tos_board_members, 'fields' => config("app.{$entity}_applications.matrix.list_tos_board_members", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('list_tos_board_members')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="population_size_in_tos">@lang("{$entity}_applications.population_size_in_tos")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="number" placeholder="0" name="population_size_in_tos" id="population_size_in_tos" class="form__input form__input--large @error('population_size_in_tos'){{ 'is-invalid' }}@enderror" value="{{ old('population_size_in_tos') ?? $model->population_size_in_tos }}">
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
                            <label class="form__label form__label--sm-left" for="date_filling_in">@lang("{$entity}_applications.date_filling_in")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="text" name="date_filling_in" id="date_filling_in" class="form__input form__input--large form__date @error('date_filling_in'){{ 'is-invalid' }}@enderror" value="{{ old('date_filling_in') ?? $model->date_filling_in }}">
                        </div>
                        <div class="col-xs-12">
                            @error('date_filling_in')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="organization_cultural_events">@lang("{$entity}_applications.organization_cultural_events")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.organization_cultural_events", ['fieldName' => 'organization_cultural_events', 'models' => $model->organization_cultural_events, 'fields' => config("app.{$entity}_applications.matrix.organization_cultural_events", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('organization_cultural_events')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="organization_cultural_events_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="conducting_sports_competitions">@lang("{$entity}_applications.conducting_sports_competitions")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.conducting_sports_competitions", ['fieldName' => 'conducting_sports_competitions', 'models' => $model->conducting_sports_competitions, 'fields' => config("app.{$entity}_applications.matrix.conducting_sports_competitions", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('conducting_sports_competitions')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="conducting_sports_competitions_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="drug_addiction_prevention_measures">@lang("{$entity}_applications.drug_addiction_prevention_measures")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.drug_addiction_prevention_measures", ['fieldName' => 'drug_addiction_prevention_measures', 'models' => $model->drug_addiction_prevention_measures, 'fields' => config("app.{$entity}_applications.matrix.drug_addiction_prevention_measures", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('drug_addiction_prevention_measures')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="drug_addiction_prevention_measures_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="availability_clubs">@lang("{$entity}_applications.availability_clubs")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.availability_clubs", ['fieldName' => 'availability_clubs', 'models' => $model->availability_clubs, 'fields' => config("app.{$entity}_applications.matrix.availability_clubs", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('availability_clubs')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="availability_clubs_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="measures_organization_landscaping">@lang("{$entity}_applications.measures_organization_landscaping")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.measures_organization_landscaping", ['fieldName' => 'measures_organization_landscaping', 'models' => $model->measures_organization_landscaping, 'fields' => config("app.{$entity}_applications.matrix.measures_organization_landscaping", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('measures_organization_landscaping')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="measures_organization_landscaping_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="number_objects_social_orientation">@lang("{$entity}_applications.number_objects_social_orientation")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.number_objects_social_orientation", ['fieldName' => 'number_objects_social_orientation', 'models' => $model->number_objects_social_orientation, 'fields' => config("app.{$entity}_applications.matrix.number_objects_social_orientation", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('number_objects_social_orientation')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="number_objects_social_orientation_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="providing_assistance">@lang("{$entity}_applications.providing_assistance")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.providing_assistance", ['fieldName' => 'providing_assistance', 'models' => $model->providing_assistance, 'fields' => config("app.{$entity}_applications.matrix.providing_assistance", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('providing_assistance')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="providing_assistance_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="healthy_lifestyle_corner">@lang("{$entity}_applications.healthy_lifestyle_corner")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.healthy_lifestyle_corner", ['fieldName' => 'healthy_lifestyle_corner', 'models' => $model->healthy_lifestyle_corner, 'fields' => config("app.{$entity}_applications.matrix.healthy_lifestyle_corner", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('healthy_lifestyle_corner')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="healthy_lifestyle_corner_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="joint_preventive_measures">@lang("{$entity}_applications.joint_preventive_measures")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.joint_preventive_measures", ['fieldName' => 'joint_preventive_measures', 'models' => $model->joint_preventive_measures, 'fields' => config("app.{$entity}_applications.matrix.joint_preventive_measures", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('joint_preventive_measures')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="joint_preventive_measures_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="fire_prevention">@lang("{$entity}_applications.fire_prevention")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.fire_prevention", ['fieldName' => 'fire_prevention', 'models' => $model->fire_prevention, 'fields' => config("app.{$entity}_applications.matrix.fire_prevention", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('fire_prevention')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="fire_prevention_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="meetings_and_seminars">@lang("{$entity}_applications.meetings_and_seminars")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.meetings_and_seminars", ['fieldName' => 'meetings_and_seminars', 'models' => $model->meetings_and_seminars, 'fields' => config("app.{$entity}_applications.matrix.meetings_and_seminars", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('meetings_and_seminars')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="meetings_and_seminars_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="placement_information_in_mass_media">@lang("{$entity}_applications.placement_information_in_mass_media")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.placement_information_in_mass_media", ['fieldName' => 'placement_information_in_mass_media', 'models' => $model->placement_information_in_mass_media, 'fields' => config("app.{$entity}_applications.matrix.placement_information_in_mass_media", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('placement_information_in_mass_media')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="placement_information_in_mass_media_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="participation_in_previous_contests_unsuccessful">@lang("{$entity}_applications.participation_in_previous_contests_unsuccessful")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.participation_in_previous_contests_unsuccessful", ['fieldName' => 'participation_in_previous_contests_unsuccessful', 'models' => $model->participation_in_previous_contests_unsuccessful, 'fields' => config("app.{$entity}_applications.matrix.participation_in_previous_contests_unsuccessful", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('participation_in_previous_contests_unsuccessful')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="participation_in_previous_contests_unsuccessful_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="participation_in_previous_contests_successful">@lang("{$entity}_applications.participation_in_previous_contests_successful")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.participation_in_previous_contests_successful", ['fieldName' => 'participation_in_previous_contests_successful', 'models' => $model->participation_in_previous_contests_successful, 'fields' => config("app.{$entity}_applications.matrix.participation_in_previous_contests_successful", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('participation_in_previous_contests_successful')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="participation_in_previous_contests_successful_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="awards">@lang("{$entity}_applications.awards")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.matrix.ltos.awards", ['fieldName' => 'awards', 'models' => $model->awards, 'fields' => config("app.{$entity}_applications.matrix.awards", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('awards')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <x-images-component :model="$model" group="awards_slides" has-description="1" type-description="input" limit=""></x-images-component>
                        </div>
                    </div>
                </div>
                <div class="panel__head">
                    <h5 class="reset-m">@lang("{$entity}_applications.additional_documentation")</h5>
                </div>
                <div class="panel__body">
                    <div class="panel__content">
                        <div class="row form__group-wrap">
                            <div class="col-xs-12">
                                <x-images-component :model="$model" group="additional_documentation" has-description="1" type-description="input" limit=""></x-images-component>
                            </div>
                        </div>
                    </div>
                </div>
                @if(auth()->user()->hasPermissions(['other.show_admin']))
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
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="points_from_administrator">@lang("{$entity}_applications.points_from_administrator")</label>
                        </div>
                        <div class="col-xs-12">
                            <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="number" step="any" placeholder="0.00" name="points_from_administrator" id="points_from_administrator" class="form__input form__input--large @error('points_from_administrator'){{ 'is-invalid' }}@enderror" value="{{ old('points_from_administrator') ?? $model->points_from_administrator }}">
                        </div>
                        <div class="col-xs-12">
                            @error('points_from_administrator')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                        <div class="row row--small row--ai-center">
                            <div class="col-xs-12">
                                <label class="form__label form__label--sm-left" for="comment_on_points_from_administrator">@lang("{$entity}_applications.comment_on_points_from_administrator")</label>
                            </div>
                            <div class="col-xs-12">
                                <textarea @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif name="comment_on_points_from_administrator" id="comment_on_points_from_administrator" class="form__textarea form__textarea--large @error('comment_on_points_from_administrator'){{ 'is-invalid' }}@enderror">{{ old('comment_on_points_from_administrator') ?? $model->comment_on_points_from_administrator }}</textarea>
                            </div>
                            <div class="col-xs-12">
                                @error('comment_on_points_from_administrator')
                                <div class="form__message form__message--error">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
