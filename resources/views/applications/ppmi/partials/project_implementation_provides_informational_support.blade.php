@if(!$model->isProjectImplementationProvidesInformationalSupportNew)
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label form__label--sm-left" for="is_media_participation">@lang("{$entity}_applications.is_media_participation")</label>
            </div>
            <div class="col-xs-12">
                <label class="form__toggle @error('is_media_participation'){{ 'is-invalid' }}@enderror">
                    <input type="hidden" name="is_media_participation" value="0">
                    <input class="form__toggle-input" type="checkbox" id="is_media_participation" name="is_media_participation" value="1" @if ($model->is_media_participation){{ 'checked' }}@endif>
                    <span class="form__toggle-icon"></span>
                </label>
            </div>
            <div class="col-xs-12">
                @error('is_media_participation')
                <div class="form__message form__message--error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label form__label--sm-left" for="mass_media">@lang("{$entity}_applications.mass_media")</label>
            </div>
            @if ($model->exists)
                <div class="col-xs-12">
                    <div class="form__group form__group--input">
                        <div class="row row--small row--ai-center">
                            <div class="col-xs-12 col-sm-2 text-sm-right">
                                <label class="form__file btn btn--medium btn--blue" for="mass_media">
                                    <i class="fas fa-plus btn__icon"></i>
                                    <span class="btn__text btn__text--right">Добавить</span>
                                    <input type="file" name="mass_media" id="mass_media" class="form__file-input js-upload-files" multiple>
                                    <input type="hidden" name="group" id="group" value="mass_media">
                                    <input type="hidden" name="model_id" value="{{ $model->id }}">
                                    <input type="hidden" name="model_morph_class" value="{{ $model->getMorphClass() }}">
                                </label>
                            </div>
                            <div class="col-xs-12 col-sm-10 js-files-block">
                                @foreach($model->mass_media as $file)
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
                    @error('mass_media')
                    <div class="form__message form__message--error">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        </div>
    </div>
@else
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label" for="project_implementation_provides_informational_support">@lang("{$entity}_applications.project_implementation_provides_informational_support")</label>
            </div>
            <div class="col-xs-12">
                @includeIf("applications.partial.matrix.ppmi.project_implementation_provides_informational_support", ['fieldName' => 'project_implementation_provides_informational_support', 'models' => $model->project_implementation_provides_informational_support, 'fields' => config("app.{$entity}_applications.matrix.project_implementation_provides_informational_support", [])])
            </div>
            <div class="col-xs-12">
                @error('project_implementation_provides_informational_support')
                <div class="form__message form__message--error">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
@endif
