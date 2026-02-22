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
                            <label class="form__label form__label--sm-left" for="contest_nomination">@lang("{$entity}_applications.contest_nomination")</label>
                        </div>
                        <div class="col-xs-12">
                            <select name="contest_nomination" id="contest_nomination" class="form__select form__select--large @error('contest_nomination'){{ 'is-invalid' }}@enderror">
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
                            <label class="form__label form__label--sm-left" for="fio">@lang("{$entity}_applications.fio")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="fio" id="fio" class="form__input form__input--large @error('fio'){{ 'is-invalid' }}@enderror" value="{{ old('fio') ?? $model->fio }}">
                        </div>
                        <div class="col-xs-12">
                            @error('fio')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="date_birth">@lang("{$entity}_applications.date_birth")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="date_birth" id="date_birth" class="form__input form__input--large form__date @error('date_birth'){{ 'is-invalid' }}@enderror" value="{{ old('date_birth') ?? $model->date_birth }}">
                        </div>
                        <div class="col-xs-12">
                            @error('date_birth')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="phone">@lang("{$entity}_applications.phone")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="phone" id="phone" class="form__input form__input--large @error('phone'){{ 'is-invalid' }}@enderror" value="{{ old('phone') ?? $model->phone }}">
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
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="email">@lang("{$entity}_applications.email")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="email" id="email" class="form__input form__input--large @error('email'){{ 'is-invalid' }}@enderror" value="{{ old('email') ?? $model->email }}">
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
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="education">@lang("{$entity}_applications.education")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="education" id="education" class="form__textarea form__textarea--large @error('education'){{ 'is-invalid' }}@enderror">{{ old('education') ?? $model->education }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('education')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="additional_education">@lang("{$entity}_applications.additional_education")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.ls.additional_education-matrix", ['fieldName' => 'additional_education', 'models' => $model->additional_education, 'fields' => config("app.{$entity}_applications.matrix.additional_education", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('additional_education')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="professional_development">@lang("{$entity}_applications.professional_development")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.ls.professional_development-matrix", ['fieldName' => 'professional_development', 'models' => $model->professional_development, 'fields' => config("app.{$entity}_applications.matrix.professional_development", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('professional_development')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="total_work_experience">@lang("{$entity}_applications.total_work_experience")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" name="total_work_experience" id="total_work_experience" class="form__input form__input--large @error('total_work_experience'){{ 'is-invalid' }}@enderror" value="{{ old('total_work_experience') ?? $model->total_work_experience }}">
                        </div>
                        <div class="col-xs-12">
                            @error('total_work_experience')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="work_experience_in_government">@lang("{$entity}_applications.work_experience_in_government")</label>
                        </div>
                        <div class="col-xs-12">
                            @includeIf("applications.partial.ls.work_experience_in_government-matrix", ['fieldName' => 'work_experience_in_government', 'models' => $model->work_experience_in_government, 'fields' => config("app.{$entity}_applications.matrix.work_experience_in_government", [])])
                        </div>
                        <div class="col-xs-12">
                            @error('work_experience_in_government')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="place_work">@lang("{$entity}_applications.place_work")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="place_work" id="place_work" class="form__input form__input--large @error('place_work'){{ 'is-invalid' }}@enderror" value="{{ old('place_work') ?? $model->place_work }}">
                        </div>
                        <div class="col-xs-12">
                            @error('place_work')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="organization_phone">@lang("{$entity}_applications.organization_phone")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="organization_phone" id="organization_phone" class="form__input form__input--large @error('organization_phone'){{ 'is-invalid' }}@enderror" value="{{ old('organization_phone') ?? $model->organization_phone }}">
                        </div>
                        <div class="col-xs-12">
                            @error('organization_phone')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="organization_email">@lang("{$entity}_applications.organization_email")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="organization_email" id="organization_email" class="form__input form__input--large @error('organization_email'){{ 'is-invalid' }}@enderror" value="{{ old('organization_email') ?? $model->organization_email }}">
                        </div>
                        <div class="col-xs-12">
                            @error('organization_email')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="working_hours_in_this_organization">@lang("{$entity}_applications.working_hours_in_this_organization")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="working_hours_in_this_organization" id="working_hours_in_this_organization" class="form__input form__input--large @error('working_hours_in_this_organization'){{ 'is-invalid' }}@enderror" value="{{ old('working_hours_in_this_organization') ?? $model->working_hours_in_this_organization }}">
                        </div>
                        <div class="col-xs-12">
                            @error('working_hours_in_this_organization')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="working_hours_in_this_position">@lang("{$entity}_applications.working_hours_in_this_position")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="text" name="working_hours_in_this_position" id="working_hours_in_this_position" class="form__input form__input--large @error('working_hours_in_this_position'){{ 'is-invalid' }}@enderror" value="{{ old('working_hours_in_this_position') ?? $model->working_hours_in_this_position }}">
                        </div>
                        <div class="col-xs-12">
                            @error('working_hours_in_this_position')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="number_employees_division_total">@lang("{$entity}_applications.number_employees_division_total")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" name="number_employees_division_total" id="number_employees_division_total" class="form__input form__input--large @error('number_employees_division_total'){{ 'is-invalid' }}@enderror" value="{{ old('number_employees_division_total') ?? $model->number_employees_division_total }}">
                        </div>
                        <div class="col-xs-12">
                            @error('number_employees_division_total')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="number_employees_division_under_your_command">@lang("{$entity}_applications.number_employees_division_under_your_command")</label>
                        </div>
                        <div class="col-xs-12">
                            <input type="number" name="number_employees_division_under_your_command" id="number_employees_division_under_your_command" class="form__input form__input--large @error('number_employees_division_under_your_command'){{ 'is-invalid' }}@enderror" value="{{ old('number_employees_division_under_your_command') ?? $model->number_employees_division_under_your_command }}">
                        </div>
                        <div class="col-xs-12">
                            @error('number_employees_division_under_your_command')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="job_responsibilities">@lang("{$entity}_applications.job_responsibilities")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="job_responsibilities" id="job_responsibilities" class="form__textarea form__textarea--large @error('job_responsibilities'){{ 'is-invalid' }}@enderror">{{ old('job_responsibilities') ?? $model->job_responsibilities }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('job_responsibilities')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="consulting">@lang("{$entity}_applications.consulting")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="consulting" id="consulting" class="form__textarea form__textarea--large @error('consulting'){{ 'is-invalid' }}@enderror">{{ old('consulting') ?? $model->consulting }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('consulting')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="awards">@lang("{$entity}_applications.awards")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="awards" id="awards" class="form__textarea form__textarea--large @error('awards'){{ 'is-invalid' }}@enderror">{{ old('awards') ?? $model->awards }}</textarea>
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
                            <label class="form__label form__label--sm-left" for="participation_in_projects">@lang("{$entity}_applications.participation_in_projects")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="participation_in_projects" id="participation_in_projects" class="form__textarea form__textarea--large @error('participation_in_projects'){{ 'is-invalid' }}@enderror">{{ old('participation_in_projects') ?? $model->participation_in_projects }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('participation_in_projects')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="form__group form__group--input">
                    <div class="row row--small row--ai-center">
                        <div class="col-xs-12">
                            <label class="form__label form__label--sm-left" for="results_activity_in_current_year">@lang("{$entity}_applications.results_activity_in_current_year")</label>
                        </div>
                        <div class="col-xs-12">
                            <textarea name="results_activity_in_current_year" id="results_activity_in_current_year" class="form__textarea form__textarea--large @error('results_activity_in_current_year'){{ 'is-invalid' }}@enderror">{{ old('results_activity_in_current_year') ?? $model->results_activity_in_current_year }}</textarea>
                        </div>
                        <div class="col-xs-12">
                            @error('results_activity_in_current_year')
                            <div class="form__message form__message--error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
