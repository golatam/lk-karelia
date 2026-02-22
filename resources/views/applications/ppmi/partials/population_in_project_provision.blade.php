@if(!$model->isPopulationInProjectProvisionNew)
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label form__label--sm-left" for="population_in_project_provision">@lang("{$entity}_applications.population_in_project_provision")</label>
            </div>
            <div class="col-xs-12">
                <textarea name="population_in_project_provision" id="population_in_project_provision" class="form__textarea form__textarea--large @error('population_in_project_provision'){{ 'is-invalid' }}@enderror">{{ old('population_in_project_provision') ?? $model->population_in_project_provision }}</textarea>
            </div>
            <div class="col-xs-12">
                @error('population_in_project_provision')
                <div class="form__message form__message--error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
@else
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label" for="public_participation_in_operation_facility">@lang("{$entity}_applications.population_in_project_provision")</label>
            </div>
            <div class="col-xs-12">
                @includeIf("applications.partial.matrix.ppmi.public_participation_in_operation_facility", ['fieldName' => 'public_participation_in_operation_facility', 'models' => $model->public_participation_in_operation_facility, 'fields' => config("app.{$entity}_applications.matrix.public_participation_in_operation_facility", [])])
            </div>
            <div class="col-xs-12">
                @error('public_participation_in_operation_facility')
                <div class="form__message form__message--error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
@endif
