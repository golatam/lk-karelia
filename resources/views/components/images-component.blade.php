@if ($model->exists)
<div class="panel__body">
    <div class="panel__content">
        <div class="row">
            <div class="col-xs-12 reset-mt">
                <h5 class="reset-mt">@lang("{$entity}_applications.bind_photo")</h5>
            </div>
            <div class="col-xs-12">
                <div class="row row--small form__image-wrap js-change-position-images js-images-list {{ $group }}">
                @if ($images->isNotEmpty())
                    @foreach($images as $image)
                    <div
                        class="col-xs-6 col-sm-4 mb-6 js-images-item {{ $group }}"
                        data-position="{{ $image->position }}"
                        data-id="{{ $model->id }}"
                        data-image-id="{{ $image->id }}"
                        data-morph-class="{{ $model->getMorphClass() }}"
                        data-group="{{ $group }}"
                    >
                        <div class="form__image form__image--fluid">
                            <button {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} class="form__image-delete js-remove-images">Удалить</button>
                            @if (file_exists(public_path("{$image->path}")))
                            <img src="{{ asset(image_path("{$image->path}", "thumbnail")) }}" alt="{{ $image->description }}" class="form__image-img">
                            @else
                            <img src="{{ asset("/assets/images/no-photo.jpg") }}" alt="{{ $image->description }}" class="form__image-img">
                            @endif
                        </div>
                        @if($hasDescription && $typeDescription === 'input')
                        <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} class="form__image-description" type="text" name="images[{{ $image->id }}]" value="{{ $image->description }}" placeholder="Описание">
                        @elseif($hasDescription && $typeDescription === 'textarea')
                        <textarea {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} class="form__image-description" name="images[{{ $image->id }}]" placeholder="Описание">{{ $image->description }}</textarea>
                        @else
                        @endif
                    </div>
                    @endforeach
                    <div
                        class="col-xs-6 col-sm-2 js-btn-block {{ $group }}"
                    >
                        <div class="form__image form__image--fluid">
                            <div class="form__image-loader">
                                <div class="loader"></div>
                            </div>
                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="file" name="file" class="js-upload-images hidden" multiple id="upload-images-{{ $group }}"
                                   data-id="{{ $model->id }}"
                                   data-morph-class="{{ $model->getMorphClass() }}"
                                   data-group="{{ $group }}"
                                   data-has-description="{{ (int) $hasDescription }}"
                                   data-limit="{{ $limit }}"
                                   data-type-description="{{ $typeDescription }}">
                            <label class="form__image-button js-add-images-btn" for="upload-images-{{ $group }}">
                                <i class="fas fa-plus"></i>
                            </label>
                        </div>
                    </div>
                @else
                    <div
                        class="col-xs-6 col-sm-2 js-btn-block {{ $group }}"
                    >
                        <div class="form__image form__image--fluid">
                            <div class="form__image-loader">
                                <div class="loader"></div>
                            </div>
                            <input {{ auth()->user()->isShowComittee() ? 'disabled' : '' }} type="file" name="file" class="js-upload-images hidden" multiple id="upload-images-{{ $group }}"
                                   data-id="{{ $model->id }}"
                                   data-morph-class="{{ $model->getMorphClass() }}"
                                   data-group="{{ $group }}"
                                   data-has-description="{{ (int) $hasDescription }}"
                                   data-limit="{{ $limit }}"
                                   data-type-description="{{ $typeDescription }}">
                            <label class="form__image-button js-add-images-btn" for="upload-images-{{ $group }}">
                                <i class="fas fa-camera"></i>
                            </label>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
