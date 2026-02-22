<input type="hidden" name="contest_id" value="{{ $model->exists ? $model->contest_id : $contest->id }}">
<input type="hidden" name="user_id" value="{{ $model->exists ? $model->user_id : $user->id }}">
@if($model->exists)
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12 col-sm-6 text-sm-right">
                <label class="form__label" for="status">@lang("{$entity}_applications.status"):</label>
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
            @error('register_id')
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
                    <option value="{{ $municipality->id }}" @if ((int) $model->municipality_id === (int) $municipality->id || (int) $municipality->id === (int) old('municipality_id')){{ 'selected' }}@endif>{{ $municipality->name }}</option>
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
            <label class="form__label" for="list_members_council_tos">@lang("{$entity}_applications.list_members_council_tos")</label>
        </div>
        <div class="col-xs-12">
            @includeIf("applications.partial.matrix.szptos.list_members_council_tos", ['fieldName' => 'list_members_council_tos', 'models' => $model->list_members_council_tos, 'fields' => config("app.{$entity}_applications.matrix.list_members_council_tos", [])])
        </div>
        <div class="col-xs-12">
            @error('list_members_council_tos')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="population_size_settlement">@lang("{$entity}_applications.population_size_settlement")</label>
        </div>
        <div class="col-xs-12">
            <input @if(auth()->user()->isShowComittee()) disabled @endif type="number" placeholder="0" name="population_size_settlement" id="population_size_settlement" class="form__input form__input--large @error('population_size_settlement'){{ 'is-invalid' }}@enderror" value="{{ old('population_size_settlement') ?? $model->population_size_settlement ?? 0 }}">
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
            <label class="form__label" for="project_name">@lang("{$entity}_applications.project_name")</label>
        </div>
        <div class="col-xs-12">
            <input @if(auth()->user()->isShowComittee()) disabled @endif type="text" name="project_name" id="project_name" class="form__input form__input--large @error('project_name'){{ 'is-invalid' }}@enderror" value="{{ old('project_name') ?? $model->project_name }}">
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
            <label class="form__label" for="project_direction">@lang("{$entity}_applications.project_direction")</label>
        </div>
        <div class="col-xs-12">
            <select @if(auth()->user()->isShowComittee()) disabled @endif name="project_direction" id="project_direction" class="form__select form__select--large @error('project_direction'){{ 'is-invalid' }}@enderror">
                <option value="">@lang("common.select")</option>
                @foreach ($projectDirections as $key => $projectDirection)
                    <option value="{{ $key }}" @if ((int) $model->project_direction === (int) $key || (int) $key === (int) old('project_direction')){{ 'selected' }}@endif>{{ $projectDirection }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-xs-12">
            @error('project_direction')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@if($model->preliminary_work_on_selection_project->isNotEmpty())
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="preliminary_work_on_selection_project">@lang("{$entity}_applications.preliminary_work_on_selection_project")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="preliminary_work_on_selection_project">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="preliminary_work_on_selection_project" id="preliminary_work_on_selection_project" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="preliminary_work_on_selection_project">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->preliminary_work_on_selection_project as $file)
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
            @error('preliminary_work_on_selection_project')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@else
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label h2" for="preliminary_work_on_selection_project">@lang("{$entity}_applications.preliminary_work_on_selection_project")</label>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="preliminary_work_on_selection_project_a">@lang("{$entity}_applications.preliminary_work_on_selection_project_a")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="preliminary_work_on_selection_project_a">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="preliminary_work_on_selection_project_a" id="preliminary_work_on_selection_project_a" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="preliminary_work_on_selection_project_a">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->preliminary_work_on_selection_project_a as $file)
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
            @error('preliminary_work_on_selection_project_a')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="preliminary_work_on_selection_project_b">@lang("{$entity}_applications.preliminary_work_on_selection_project_b")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="preliminary_work_on_selection_project_b">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="preliminary_work_on_selection_project_b" id="preliminary_work_on_selection_project_b" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="preliminary_work_on_selection_project_b">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->preliminary_work_on_selection_project_b as $file)
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
            @error('preliminary_work_on_selection_project_b')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@endif
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="number_present_at_general_meeting">@lang("{$entity}_applications.number_present_at_general_meeting")</label>
        </div>
        <div class="col-xs-12">
            <input type="number" placeholder="0" name="number_present_at_general_meeting" id="number_present_at_general_meeting" class="form__input form__input--large @error('number_present_at_general_meeting'){{ 'is-invalid' }}@enderror" value="{{ old('number_present_at_general_meeting') ?? $model->number_present_at_general_meeting ?? 0 }}">
        </div>
        <div class="col-xs-12">
            @error('number_present_at_general_meeting')
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
            <textarea name="problem_description" id="problem_description" class="form__textarea form__textarea--large @error('problem_description'){{ 'is-invalid' }}@enderror">{{ old('problem_description') ?? $model->problem_description }}</textarea>
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
            <label class="form__label" for="project_purpose">@lang("{$entity}_applications.project_purpose")</label>
        </div>
        <div class="col-xs-12">
            <textarea name="project_purpose" id="project_purpose" class="form__textarea form__textarea--large @error('project_purpose'){{ 'is-invalid' }}@enderror">{{ old('project_purpose') ?? $model->project_purpose }}</textarea>
        </div>
        <div class="col-xs-12">
            @error('project_purpose')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="project_tasks">@lang("{$entity}_applications.project_tasks")</label>
        </div>
        <div class="col-xs-12">
            <textarea name="project_tasks" id="project_tasks" class="form__textarea form__textarea--large @error('project_tasks'){{ 'is-invalid' }}@enderror">{{ old('project_tasks') ?? $model->project_tasks }}</textarea>
        </div>
        <div class="col-xs-12">
            @error('project_tasks')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label h2" for="planned_sources_financing_project_activities">@lang("{$entity}_applications.planned_sources_financing_project_activities")</label>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="planned_sources_financing_project_activities_a">@lang("{$entity}_applications.planned_sources_financing_project_activities_a")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="planned_sources_financing_project_activities_a">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="planned_sources_financing_project_activities_a" id="planned_sources_financing_project_activities_a" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="planned_sources_financing_project_activities_a">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->planned_sources_financing_project_activities_a as $file)
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
            @error('planned_sources_financing_project_activities_a')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="planned_sources_financing_project_activities_b">@lang("{$entity}_applications.planned_sources_financing_project_activities_b")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="planned_sources_financing_project_activities_b">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="planned_sources_financing_project_activities_b" id="planned_sources_financing_project_activities_b" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="planned_sources_financing_project_activities_b">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->planned_sources_financing_project_activities_b as $file)
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
            @error('planned_sources_financing_project_activities_b')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="duration_practice_start">@lang("{$entity}_applications.duration_practice_start")</label>
        </div>
        <div class="col-xs-12">
            <input type="text" name="duration_practice_start" id="duration_practice_start" class="form__input form__input--large form__date @error('duration_practice_start'){{ 'is-invalid' }}@enderror" value="{{ old('duration_practice_start') ?? $model->duration_practice_start }}">
        </div>
        <div class="col-xs-12">
            @error('duration_practice_start')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="duration_practice_end">@lang("{$entity}_applications.duration_practice_end")</label>
        </div>
        <div class="col-xs-12">
            <input type="text" name="duration_practice_end" id="duration_practice_end" class="form__input form__input--large form__date @error('duration_practice_end'){{ 'is-invalid' }}@enderror" value="{{ old('duration_practice_end') ?? $model->duration_practice_end }}">
        </div>
        <div class="col-xs-12">
            @error('duration_practice_end')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="calendar_plan_work_on_project">@lang("{$entity}_applications.calendar_plan_work_on_project")</label>
        </div>
        <div class="col-xs-12">
            @includeIf("applications.partial.matrix.szptos.calendar_plan_work_on_project", ['fieldName' => 'calendar_plan_work_on_project', 'models' => $model->calendar_plan_work_on_project, 'fields' => config("app.{$entity}_applications.matrix.calendar_plan_work_on_project", [])])
        </div>
        <div class="col-xs-12">
            @error('calendar_plan_work_on_project')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
{{--<div class="form__group form__group--input">--}}
{{--    <div class="row row--small row--ai-center">--}}
{{--        <div class="col-xs-12">--}}
{{--            <label class="form__label" for="information_project_support_info">@lang("{$entity}_applications.information_project_support_info")</label>--}}
{{--        </div>--}}
{{--        <div class="col-xs-12">--}}
{{--            @includeIf("applications.partial.matrix.szptos.information_project_support_info", ['fieldName' => 'information_project_support_info', 'models' => $model->information_project_support_info, 'fields' => config("app.{$entity}_applications.matrix.information_project_support_info", [])])--}}
{{--        </div>--}}
{{--        <div class="col-xs-12">--}}
{{--            @error('information_project_support_info')--}}
{{--            <div class="form__message form__message--error">{{ $message }}</div>--}}
{{--            @enderror--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
{{--<div class="form__group form__group--input">--}}
{{--    <div class="row row--small row--ai-center">--}}
{{--        <div class="col-xs-12">--}}
{{--            <label class="form__label" for="information_project_support">@lang("{$entity}_applications.information_project_support")</label>--}}
{{--        </div>--}}
{{--        <div class="col-xs-12">--}}
{{--            <div class="form__group form__group--input">--}}
{{--                <div class="row row--small row--ai-center">--}}
{{--                    <div class="col-xs-12 col-sm-2 text-sm-right">--}}
{{--                        <label class="form__file btn btn--medium btn--blue" for="information_project_support">--}}
{{--                            <i class="fas fa-plus btn__icon"></i>--}}
{{--                            <span class="btn__text btn__text--right">Добавить</span>--}}
{{--                            <input type="file" name="information_project_support" id="information_project_support" class="form__file-input js-upload-files" multiple>--}}
{{--                            <input type="hidden" name="group" id="group" value="information_project_support">--}}
{{--                            <input type="hidden" name="model_id" value="{{ $model->id }}">--}}
{{--                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">--}}
{{--                        </label>--}}
{{--                    </div>--}}
{{--                    <div class="col-xs-12 col-sm-10 js-files-block">--}}
{{--                        @foreach($model->information_project_support as $file)--}}
{{--                            <div class="row row--small row--ai-center">--}}
{{--                                <div class="col-xs-9 col-sm-10 col-md-11">--}}
{{--                                    <div class="form__element form__element--large">--}}
{{--                                        <a href="{{ $file->path }}" class="btn btn--full btn--text-left btn--medium btn--gray" download>--}}
{{--                                            <i class="fas fa-file-{{ $file->extension }}"></i>--}}
{{--                                            <span class="btn__text btn__text--right">{{ $file->name }}</span>--}}
{{--                                        </a>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-xs-3 col-sm-2 col-md-1">--}}
{{--                                    <button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="{{ $model->id }}" data-morph-class="{{ $model->getMorphClass() }}" data-file-id="{{ $file->id }}">--}}
{{--                                        <i class="fas fa-trash-alt"></i>--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--        <div class="col-xs-12">--}}
{{--            @error('information_project_support')--}}
{{--            <div class="form__message form__message--error">{{ $message }}</div>--}}
{{--            @enderror--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="results_project_implementation">@lang("{$entity}_applications.results_project_implementation")</label>
        </div>
        <div class="col-xs-12">
            <textarea name="results_project_implementation" id="results_project_implementation" class="form__textarea form__textarea--large @error('results_project_implementation'){{ 'is-invalid' }}@enderror">{{ old('results_project_implementation') ?? $model->results_project_implementation }}</textarea>
        </div>
        <div class="col-xs-12">
            @error('results_project_implementation')
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
            <input type="number" placeholder="0" name="number_beneficiaries" id="number_beneficiaries" class="form__input form__input--large @error('number_beneficiaries'){{ 'is-invalid' }}@enderror" value="{{ old('number_beneficiaries') ?? $model->number_beneficiaries ?? 0 }}">
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
            <label class="form__label" for="description_need">@lang("{$entity}_applications.description_need")</label>
        </div>
        <div class="col-xs-12">
            <textarea name="description_need" id="description_need" class="form__textarea form__textarea--large @error('description_need'){{ 'is-invalid' }}@enderror">{{ old('description_need') ?? $model->description_need }}</textarea>
        </div>
        <div class="col-xs-12">
            @error('description_need')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="budget_funds_republic">@lang("{$entity}_applications.budget_funds_republic")</label>
        </div>
        <div class="col-xs-12">
            <input type="number" step="any" placeholder="0.00" name="budget_funds_republic" id="budget_funds_republic" class="form__input form__input--large @error('budget_funds_republic'){{ 'is-invalid' }}@enderror" value="{{ old('budget_funds_republic') ?? $model->budget_funds_republic ?? 0 }}">
        </div>
        <div class="col-xs-12">
            @error('budget_funds_republic')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="funds_tos">@lang("{$entity}_applications.funds_tos")</label>
        </div>
        <div class="col-xs-12">
            <input type="number" step="any" placeholder="0.00" name="funds_tos" id="funds_tos" class="form__input form__input--large @error('funds_tos'){{ 'is-invalid' }}@enderror" value="{{ old('funds_tos') ?? $model->funds_tos ?? 0 }}">
        </div>
        <div class="col-xs-12">
            @error('funds_tos')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="funds_legal_entities">@lang("{$entity}_applications.funds_legal_entities")</label>
        </div>
        <div class="col-xs-12">
            <input type="number" step="any" placeholder="0.00" name="funds_legal_entities" id="funds_legal_entities" class="form__input form__input--large @error('funds_legal_entities'){{ 'is-invalid' }}@enderror" value="{{ old('funds_legal_entities') ?? $model->funds_legal_entities ?? 0 }}">
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
            <label class="form__label" for="funds_local_budget">@lang("{$entity}_applications.funds_local_budget")</label>
        </div>
        <div class="col-xs-12">
            <input type="number" step="any" placeholder="0.00" name="funds_local_budget" id="funds_local_budget" class="form__input form__input--large @error('funds_local_budget'){{ 'is-invalid' }}@enderror" value="{{ old('funds_local_budget') ?? $model->funds_local_budget ?? 0 }}">
        </div>
        <div class="col-xs-12">
            @error('funds_local_budget')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="extract_from_registry">@lang("{$entity}_applications.extract_from_registry")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="extract_from_registry">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="extract_from_registry" id="extract_from_registry" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="extract_from_registry">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->extract_from_registry as $file)
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
            @error('extract_from_registry')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
{{--@dd($model->documentation->isNotEmpty())--}}
@if($model->documentation->isNotEmpty())
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="documentation">@lang("{$entity}_applications.documentation")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="documentation">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="documentation" id="documentation" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="documentation">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->documentation as $file)
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
            @error('documentation')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@else
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="documentation">@lang("{$entity}_applications.documentation")</label>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="documentation_a">@lang("{$entity}_applications.documentation_a")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="documentation_a">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="documentation_a" id="documentation_a" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="documentation_a">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->documentation_a as $file)
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
            @error('documentation_a')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="documentation_b">@lang("{$entity}_applications.documentation_b")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="documentation_b">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="documentation_b" id="documentation_b" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="documentation_b">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->documentation_b as $file)
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
            @error('documentation_b')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="documentation_c">@lang("{$entity}_applications.documentation_c")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="documentation_c">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="documentation_c" id="documentation_c" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="documentation_c">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->documentation_c as $file)
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
            @error('documentation_c')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="documentation_d">@lang("{$entity}_applications.documentation_d")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="documentation_d">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="documentation_d" id="documentation_d" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="documentation_d">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->documentation_d as $file)
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
            @error('documentation_d')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@endif
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="letter_guarantee">@lang("{$entity}_applications.letter_guarantee")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="letter_guarantee">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="letter_guarantee" id="letter_guarantee" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="letter_guarantee">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->letter_guarantee as $file)
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
            @error('letter_guarantee')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="other_documents">@lang("{$entity}_applications.other_documents")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="other_documents">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="other_documents" id="other_documents" class="form__file-input js-upload-files" accept=".pdf" multiple>
                            <input type="hidden" name="group" id="group" value="other_documents">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->other_documents as $file)
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
            @error('other_documents')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="participation_population_in_implementation_project">@lang("{$entity}_applications.participation_population_in_implementation_project")</label>
        </div>
        <div class="col-xs-12">
            @includeIf("applications.partial.matrix.szptos.participation_population_in_implementation_project", ['fieldName' => 'participation_population_in_implementation_project', 'models' => $model->participation_population_in_implementation_project, 'fields' => config("app.{$entity}_applications.matrix.participation_population_in_implementation_project", [])])
        </div>
        <div class="col-xs-12">
            @error('participation_population_in_implementation_project')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="public_participation_in_operation_facility">@lang("{$entity}_applications.public_participation_in_operation_facility")</label>
        </div>
        <div class="col-xs-12">
            @includeIf("applications.partial.matrix.szptos.public_participation_in_operation_facility", ['fieldName' => 'public_participation_in_operation_facility', 'models' => $model->public_participation_in_operation_facility, 'fields' => config("app.{$entity}_applications.matrix.public_participation_in_operation_facility", [])])
        </div>
        <div class="col-xs-12">
            @error('public_participation_in_operation_facility')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="project_implementation_provides_informational_support">@lang("{$entity}_applications.project_implementation_provides_informational_support")</label>
        </div>
        <div class="col-xs-12">
            @includeIf("applications.partial.matrix.szptos.project_implementation_provides_informational_support", ['fieldName' => 'project_implementation_provides_informational_support', 'models' => $model->project_implementation_provides_informational_support, 'fields' => config("app.{$entity}_applications.matrix.project_implementation_provides_informational_support", [])])
        </div>
        <div class="col-xs-12">
            @error('project_implementation_provides_informational_support')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="person_responsible_implementation_project">@lang("{$entity}_applications.person_responsible_implementation_project")</label>
        </div>
        <div class="col-xs-12">
            <input type="text" name="person_responsible_implementation_project" id="person_responsible_implementation_project" class="form__input form__input--large @error('person_responsible_implementation_project'){{ 'is-invalid' }}@enderror" value="{{ old('person_responsible_implementation_project') ?? $model->person_responsible_implementation_project }}">
        </div>
        <div class="col-xs-12">
            @error('person_responsible_implementation_project')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="is_grand_opening_with_media_coverage">@lang("{$entity}_applications.is_grand_opening_with_media_coverage")</label>
        </div>
        <div class="col-xs-12">
            <label class="form__toggle @error('is_grand_opening_with_media_coverage'){{ 'is-invalid' }}@enderror">
                <input type="hidden" name="is_grand_opening_with_media_coverage" value="0">
                <input class="form__toggle-input" type="checkbox" id="is_grand_opening_with_media_coverage" name="is_grand_opening_with_media_coverage" value="1" @if ($model->is_grand_opening_with_media_coverage){{ 'checked' }}@endif>
                <span class="form__toggle-icon"></span>
            </label>
        </div>
        <div class="col-xs-12">
            @error('is_grand_opening_with_media_coverage')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="app_four">@lang("{$entity}_applications.app_four")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__group form__group--input">
                <div class="row row--small row--ai-center">
                    <div class="col-xs-12 col-sm-2 text-sm-right">
                        <label class="form__file btn btn--medium btn--blue" for="app_four">
                            <i class="fas fa-plus btn__icon"></i>
                            <span class="btn__text btn__text--right">Добавить</span>
                            <input type="file" name="app_four" id="app_four" class="form__file-input js-upload-files" multiple>
                            <input type="hidden" name="group" id="group" value="app_four">
                            <input type="hidden" name="model_id" value="{{ $model->id }}">
                            <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                        </label>
                    </div>
                    <div class="col-xs-12 col-sm-10 js-files-block">
                        @foreach($model->app_four as $file)
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
            @error('app_four')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="date_filling_in">@lang("{$entity}_applications.date_filling_in")</label>
        </div>
        <div class="col-xs-12">
            <input type="text" name="date_filling_in" id="date_filling_in" class="form__input form__input--large form__date @error('date_filling_in'){{ 'is-invalid' }}@enderror" value="{{ old('date_filling_in') ?? $model->date_filling_in }}">
        </div>
        <div class="col-xs-12">
            @error('date_filling_in')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@if(auth()->user()->hasPermissions(['other.show_admin']))
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
                <label class="form__label" for="total_application_points">@lang("{$entity}_applications.total_application_points")</label>
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
                <label class="form__label" for="points_from_administrator">@lang("{$entity}_applications.points_from_administrator")</label>
            </div>
            <div class="col-xs-12">
                <input @if(auth()->user()->isShowComittee()){{ 'disabled' }}@endif type="number" step="any" placeholder="0.00" name="points_from_administrator" id="points_from_administrator" class="form__input form__input--large @error('points_from_administrator'){{ 'is-invalid' }}@enderror" value="{{ old('points_from_administrator') ?? $model->points_from_administrator ?? 0 }}">
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
                <label class="form__label" for="comment_on_points_from_administrator">@lang("{$entity}_applications.comment_on_points_from_administrator")</label>
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
    @includeIf("applications.{$entity}.partials.is-admitted-to-competition")
@endif
