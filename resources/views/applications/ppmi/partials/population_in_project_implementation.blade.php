@if(!$model->isPopulationInProjectImplementationNew)
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label form__label--sm-left" for="population_in_project_implementation">@lang("{$entity}_applications.population_in_project_implementation")</label>
            </div>
            <div class="col-xs-12">
                <textarea name="population_in_project_implementation" id="population_in_project_implementation" class="form__textarea form__textarea--large @error('population_in_project_implementation'){{ 'is-invalid' }}@enderror">{{ old('population_in_project_implementation') ?? $model->population_in_project_implementation }}</textarea>
            </div>
            <div class="col-xs-12">
                @error('population_in_project_implementation')
                <div class="form__message form__message--error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
@else
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label" for="participation_population_in_implementation_project">@lang("{$entity}_applications.population_in_project_implementation")</label>
            </div>
            <div class="col-xs-12">
                @includeIf("applications.partial.matrix.ppmi.participation_population_in_implementation_project", ['fieldName' => 'participation_population_in_implementation_project', 'models' => $model->participation_population_in_implementation_project, 'fields' => config("app.{$entity}_applications.matrix.participation_population_in_implementation_project", [])])
            </div>
            <div class="col-xs-12">
                @error('participation_population_in_implementation_project')
                <div class="form__message form__message--error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
@endif
