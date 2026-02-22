@if($model->documentation->isNotEmpty())
    <div class="form__group form__group--input">
        <div class="row row--small row--ai-center">
            <div class="col-xs-12">
                <label class="form__label form__label--sm-left" for="documentation">@lang("{$entity}_applications.documentation")</label>
            </div>
            @if ($model->exists)
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
            @endif
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
