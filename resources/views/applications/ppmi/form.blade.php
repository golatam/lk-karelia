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
                            <label class="form__label form__label--sm-left" for="project_name">@lang("{$entity}_applications.project_name")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="project_name" id="project_name" class="form__input form__input--large @error('project_name'){{ 'is-invalid' }}@enderror" value="{{ old('project_name') ?? $model->project_name }}">
                        </div>
                        <div class="col-xs-12">
                        @error('project_name')
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
                            <select name="municipality_id" id="municipality_id" class="form__select form__select--large @error('municipality_id'){{ 'is-invalid' }}@enderror" required>
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
                            <label class="form__label form__label--sm-left" for="population_size_settlement">@lang("{$entity}_applications.population_size_settlement")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" placeholder="0" name="population_size_settlement" id="population_size_settlement" class="form__input form__input--large @error('population_size_settlement'){{ 'is-invalid' }}@enderror" value="{{ old('population_size_settlement') ?? $model->population_size_settlement }}">
                        </div>
                        <div class="col-xs-12">
                        @error('population_size_settlement')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="project_typology">@lang("{$entity}_applications.project_typology")</label>
                        </div>
                        <div class="col-xs-12">
                            <select name="project_typology" id="project_typology" class="form__select form__select--large @error('project_typology'){{ 'is-invalid' }}@enderror">
                                <option value="">@lang("common.select")</option>
                                @foreach ($projectTypologies as $key => $projectTypology)
                                    <option value="{{ $key }}" @if ((int) $model->project_typology === (int) $key || (int) $key === (string) old('project_typology')){{ 'selected' }}@endif>{{ $projectTypology }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xs-12">
                            @error('project_typology')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="extracts">@lang("{$entity}_applications.extracts")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="extracts">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="extracts" id="extracts" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="extracts">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->extracts as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('extracts')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                        @endif
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="description_problem">@lang("{$entity}_applications.description_problem")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="description_problem" id="description_problem" class="form__textarea form__textarea--large @error('description_problem'){{ 'is-invalid' }}@enderror">{{ old('description_problem') ?? $model->description_problem }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('description_problem')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.project_implementation_activities")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.repair_work")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="cost_repair_work">@lang("{$entity}_applications.cost_repair_work")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="number" step="any" placeholder="0.00" name="cost_repair_work" id="cost_repair_work" class="form__input form__input--large @error('cost_repair_work'){{ 'is-invalid' }}@enderror" value="{{ old('cost_repair_work') ?? $model->cost_repair_work }}">
                                    </div>
                                    <div class="col-xs-12">
                                        @error('cost_repair_work')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="comment_on_cost_repairs">@lang("{$entity}_applications.comment_on_cost_repairs")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="text" name="comment_on_cost_repairs" id="comment_on_cost_repairs" class="form__input form__input--large @error('comment_on_cost_repairs'){{ 'is-invalid' }}@enderror" value="{{ old('comment_on_cost_repairs') ?? $model->comment_on_cost_repairs }}">
                                    </div>
                                    <div class="col-xs-12">
                                        @error('comment_on_cost_repairs')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.purchasing_materials")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="cost_purchasing_materials">@lang("{$entity}_applications.cost_purchasing_materials")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="number" step="any" placeholder="0.00" name="cost_purchasing_materials" id="cost_purchasing_materials" class="form__input form__input--large @error('cost_purchasing_materials'){{ 'is-invalid' }}@enderror" value="{{ old('cost_purchasing_materials') ?? $model->cost_purchasing_materials }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('cost_purchasing_materials')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="comment_on_cost_purchasing_materials">@lang("{$entity}_applications.comment_on_cost_purchasing_materials")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="text" name="comment_on_cost_purchasing_materials" id="comment_on_cost_purchasing_materials" class="form__input form__input--large @error('comment_on_cost_purchasing_materials'){{ 'is-invalid' }}@enderror" value="{{ old('comment_on_cost_purchasing_materials') ?? $model->comment_on_cost_purchasing_materials }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('comment_on_cost_purchasing_materials')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.purchasing_equipment")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="cost_purchasing_equipment">@lang("{$entity}_applications.cost_purchasing_equipment")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="number" step="any" placeholder="0.00" name="cost_purchasing_equipment" id="cost_purchasing_equipment" class="form__input form__input--large @error('cost_purchasing_equipment'){{ 'is-invalid' }}@enderror" value="{{ old('cost_purchasing_equipment') ?? $model->cost_purchasing_equipment }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('cost_purchasing_equipment')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="comment_on_cost_purchasing_equipment">@lang("{$entity}_applications.comment_on_cost_purchasing_equipment")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="text" name="comment_on_cost_purchasing_equipment" id="comment_on_cost_purchasing_equipment" class="form__input form__input--large @error('comment_on_cost_purchasing_equipment'){{ 'is-invalid' }}@enderror" value="{{ old('comment_on_cost_purchasing_equipment') ?? $model->comment_on_cost_purchasing_equipment }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('comment_on_cost_purchasing_equipment')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.construction_control")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="cost_construction_control">@lang("{$entity}_applications.cost_construction_control")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="number" step="any" placeholder="0.00" name="cost_construction_control" id="cost_construction_control" class="form__input form__input--large @error('cost_construction_control'){{ 'is-invalid' }}@enderror" value="{{ old('cost_construction_control') ?? $model->cost_construction_control }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('cost_construction_control')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="comment_on_cost_construction_control">@lang("{$entity}_applications.comment_on_cost_construction_control")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="text" name="comment_on_cost_construction_control" id="comment_on_cost_construction_control" class="form__input form__input--large @error('comment_on_cost_construction_control'){{ 'is-invalid' }}@enderror" value="{{ old('comment_on_cost_construction_control') ?? $model->comment_on_cost_construction_control }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('comment_on_cost_construction_control')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.other_expenses")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="cost_other_expenses">@lang("{$entity}_applications.cost_other_expenses")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="number" step="any" placeholder="0.00" name="cost_other_expenses" id="cost_other_expenses" class="form__input form__input--large @error('cost_other_expenses'){{ 'is-invalid' }}@enderror" value="{{ old('cost_other_expenses') ?? $model->cost_other_expenses }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('cost_other_expenses')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div class="col-xs-12">
                                        <label class="form__label form__label--sm-left" for="comment_on_cost_other_expenses">@lang("{$entity}_applications.comment_on_cost_other_expenses")</label>
                                    </div>
                                    <div class="col-xs-12">
                                        <input type="text" name="comment_on_cost_other_expenses" id="comment_on_cost_other_expenses" class="form__input form__input--large @error('comment_on_cost_other_expenses'){{ 'is-invalid' }}@enderror" value="{{ old('comment_on_cost_other_expenses') ?? $model->comment_on_cost_other_expenses }}">
                                    </div>
                                    <div class="col-xs-12">
                                    @error('comment_on_cost_other_expenses')
                                        <div class="form__message form__message--error">{{ $message }}</div>
                                    @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @includeIf("applications.{$entity}.partials.documentation")
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="expected_results">@lang("{$entity}_applications.expected_results")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="expected_results" id="expected_results" class="form__textarea form__textarea--large @error('expected_results'){{ 'is-invalid' }}@enderror">{{ old('expected_results') ?? $model->expected_results }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('expected_results')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="">@lang("{$entity}_applications.planned_sources_financing")</label>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="funds_municipal">@lang("{$entity}_applications.funds_municipal")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" step="any" placeholder="0.00" name="funds_municipal" id="funds_municipal" class="form__input form__input--large @error('funds_municipal'){{ 'is-invalid' }}@enderror" value="{{ old('funds_municipal') ?? $model->funds_municipal }}">
                        </div>
                        <div class="col-xs-12">
                        @error('funds_municipal')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="funds_individuals">@lang("{$entity}_applications.funds_individuals")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" step="any" placeholder="0.00" name="funds_individuals" id="funds_individuals" class="form__input form__input--large @error('funds_individuals'){{ 'is-invalid' }}@enderror" value="{{ old('funds_individuals') ?? $model->funds_individuals }}">
                        </div>
                        <div class="col-xs-12">
                        @error('funds_individuals')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="funds_legal_entities">@lang("{$entity}_applications.funds_legal_entities")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" step="any" placeholder="0.00" name="funds_legal_entities" id="funds_legal_entities" class="form__input form__input--large @error('funds_legal_entities'){{ 'is-invalid' }}@enderror" value="{{ old('funds_legal_entities') ?? $model->funds_legal_entities }}">
                        </div>
                        <div class="col-xs-12">
                        @error('funds_legal_entities')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="funds_republic">@lang("{$entity}_applications.funds_republic")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" step="any" placeholder="0.00" name="funds_republic" id="funds_republic" class="form__input form__input--large @error('funds_republic'){{ 'is-invalid' }}@enderror" value="{{ old('funds_republic') ?? $model->funds_republic }}">
                        </div>
                        <div class="col-xs-12">
                        @error('funds_republic')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="planned_sources_financing">@lang("{$entity}_applications.planned_sources_financing_files")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="planned_sources_financing">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="planned_sources_financing" id="planned_sources_financing" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="planned_sources_financing">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->planned_sources_financing as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('planned_sources_financing')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                        @endif
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="gratuitous_receipts">@lang("{$entity}_applications.gratuitous_receipts")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.ppmi.gratuitous_receipts-matrix", ['fieldName' => 'gratuitous_receipts', 'models' => $model->gratuitous_receipts, 'fields' => config("app.{$entity}_applications.matrix.gratuitous_receipts", [])])
                        </div>
                        <div class="col-xs-12">
                        @error('gratuitous_receipts')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="population_that_benefit_from_results_project">@lang("{$entity}_applications.population_that_benefit_from_results_project")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="population_that_benefit_from_results_project" id="population_that_benefit_from_results_project" class="form__textarea form__textarea--large @error('population_that_benefit_from_results_project'){{ 'is-invalid' }}@enderror">{{ old('population_that_benefit_from_results_project') ?? $model->population_that_benefit_from_results_project }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('population_that_benefit_from_results_project')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="population_size">@lang("{$entity}_applications.population_size")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" placeholder="0" name="population_size" id="population_size" class="form__input form__input--large @error('population_size'){{ 'is-invalid' }}@enderror" value="{{ old('population_size') ?? $model->population_size }}">
                        </div>
                        <div class="col-xs-12">
                        @error('population_size')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="population_size_in_congregation">@lang("{$entity}_applications.population_size_in_congregation")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" placeholder="0" name="population_size_in_congregation" id="population_size_in_congregation" class="form__input form__input--large @error('population_size_in_congregation'){{ 'is-invalid' }}@enderror" value="{{ old('population_size_in_congregation') ?? $model->population_size_in_congregation }}">
                        </div>
                        <div class="col-xs-12">
                        @error('population_size_in_congregation')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="protocols">@lang("{$entity}_applications.protocols")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="protocols">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="protocols" id="protocols" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="protocols">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->protocols as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            @error('protocols')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                        @endif
                    </div>
                </div>
                @includeIf("applications.{$entity}.partials.population_in_project_implementation")
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="operating_and_maintenance_costs">@lang("{$entity}_applications.operating_and_maintenance_costs")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.ppmi.operating_and_maintenance_costs-matrix", ['fieldName' => 'operating_and_maintenance_costs', 'models' => $model->operating_and_maintenance_costs, 'fields' => config("app.{$entity}_applications.matrix.operating_and_maintenance_costs", [])])
                        </div>
                        <div class="col-xs-12">
                        @error('operating_and_maintenance_costs')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                @includeIf("applications.{$entity}.partials.population_in_project_provision")
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="questionnaires">@lang("{$entity}_applications.questionnaires")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="questionnaires">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="questionnaires" id="questionnaires" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="questionnaires">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->questionnaires as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('questionnaires')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                        @endif
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="implementation_date">@lang("{$entity}_applications.implementation_date")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="implementation_date" id="implementation_date" class="form__input form__input--large form__date @error('implementation_date'){{ 'is-invalid' }}@enderror" value="{{ old('implementation_date') ?? $model->implementation_date }}">
                        </div>
                        <div class="col-xs-12">
                        @error('implementation_date')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        @if($model->checkCommentOldField())
                            <div class="col-xs-12">
                                <label class="form__label form__label--sm-left" for="comment">@lang("{$entity}_applications.comment")</label>
                            </div>
                            <div class="col-xs-12">
                                <textarea name="comment" id="comment" class="form__textarea form__textarea--large @error('comment'){{ 'is-invalid' }}@enderror">{{ old('comment') ?? $model->comment }}</textarea>
                            </div>
                            <div class="col-xs-12">
                                @error('comment')
                                <div class="form__message form__message--error">{{ $message }}</div>
                                @enderror
                            </div>
                        @else
                            <div class="col-xs-12">
                                <label class="form__label form__label--sm-left" for="planned_activities_within_project">@lang("{$entity}_applications.planned_activities_within_project")</label>
                            </div>
                            <div class="col-xs-12">
                                @includeIf("applications.partial.matrix.{$entity}.planned_activities_within_project", ['fieldName' => 'planned_activities_within_project', 'models' => $model->planned_activities_within_project, 'fields' => config("app.{$entity}_applications.matrix.planned_activities_within_project", [])])
                            </div>
                            <div class="col-xs-12">
                                @error('planned_activities_within_project')
                                <div class="form__message form__message--error">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="is_unpaid_work_of_population">@lang("{$entity}_applications.is_unpaid_work_of_population")</label>
                        </div>
                        <div class="col-xs-12">
                            <label class="form__toggle @error('is_unpaid_work_of_population'){{ 'is-invalid' }}@enderror">
                                <input type="hidden" name="is_unpaid_work_of_population" value="0">
                                <input class="form__toggle-input" type="checkbox" id="is_unpaid_work_of_population" name="is_unpaid_work_of_population" value="1" @if ($model->is_unpaid_work_of_population){{ 'checked' }}@endif>
                                <span class="form__toggle-icon"></span>
                            </label>
                        </div>
                        <div class="col-xs-12">
                        @error('is_unpaid_work_of_population')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                @includeIf("applications.{$entity}.partials.project_implementation_provides_informational_support")
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="acts">@lang("{$entity}_applications.acts")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="acts">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="acts" id="acts" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="acts">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->acts as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('acts')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                        @endif
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="payment">@lang("{$entity}_applications.payment")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="payment">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="payment" id="payment" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="payment">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->payment as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('payment')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                        @endif
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="publications">@lang("{$entity}_applications.publications")</label>
                        </div>
                        @if ($model->exists)
                        <div class="col-xs-12">
                            <div class="form__group form__group--input">
                                <div class="row row--small row--ai-center">
                                    <div class="col-xs-12 col-sm-2 text-sm-right">
                                        <label class="form__file btn btn--medium btn--blue" for="publications">
                                            <i class="fas fa-plus btn__icon"></i>
                                            <span class="btn__text btn__text--right">Добавить</span>
                                            <input type="file" name="publications" id="publications" class="form__file-input js-upload-files" multiple>
                                            <input type="hidden" name="group" id="group" value="publications">
                                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                        </label>
                                    </div>
                                    <div class="col-xs-12 col-sm-10 js-files-block">
                                        @foreach($model->publications as $file)
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
                                                <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12">
                        @error('publications')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                        @endif
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
                            <label class="form__label form__label--sm-left" for="total_application_points">@lang("{$entity}_applications.executor")</label>
                        </div>
                        <div class="col-xs-12">
                            <div class="form__element form__element--large">
                                <div>{{ $model->user?->executor }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="total_application_points">@lang("{$entity}_applications.executor_phone")</label>
                        </div>
                        <div class="col-xs-12">
                            <div class="form__element form__element--large">
                                <div>{{ $model->user?->executor_phone }}</div>
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
                            <input type="number" step="any" placeholder="0.00" name="points_from_administrator" id="points_from_administrator" class="form__input form__input--large @error('points_from_administrator'){{ 'is-invalid' }}@enderror" value="{{ old('points_from_administrator', $model->points_from_administrator) ?? 0.00 }}">
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
                            <textarea name="comment_on_points_from_administrator" id="comment_on_points_from_administrator" class="form__textarea form__textarea--large @error('comment_on_points_from_administrator'){{ 'is-invalid' }}@enderror">{{ old('comment_on_points_from_administrator') ?? $model->comment_on_points_from_administrator }}</textarea>
                        </div>
                        <div class="col-xs-12">
                        @error('comment_on_points_from_administrator')
                            <div class="form__message form__message--error">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                @includeIf("applications.{$entity}.partials.is-admitted-to-competition")
                @endif
            </div>
        </div>
    </div>
</div>
