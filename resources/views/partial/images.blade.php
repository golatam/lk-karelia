@if ($model->exists)
    <div class="panel__body">
        <div class="panel__content">
            <div class="row">
                <div class="col-xs-12 col-md-11 col-lg-10 col-xl-9">
                    <div class="row row--small form__image-wrap js-change-position-images js-images-list">
                        @if ($images->isNotEmpty())
                            @foreach($images as $image)
                                <div
                                    class="col-xs-6 col-sm-2 js-images-item"
                                    data-position="{{ $image->position }}"
                                    data-id="{{ $model->id }}"
                                    data-image-id="{{ $image->id }}"
                                    data-morph-class="{{ $model->getMorphClass() }}"
                                >
                                    <div class="form__image form__image--fluid">
                                        <button
                                            class="form__image-delete js-remove-images"
                                        >Удалить</button>
                                        @if (file_exists(public_path("{$image->path}")))
                                            <img src="{{ asset(image_path("{$image->path}", "thumbnail")) }}" alt="" class="form__image-img">
                                        @else
                                            <img src="{{ asset("/assets/images/no-photo.jpg") }}" alt="" class="form__image-img">
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <div
                                class="col-xs-6 col-sm-2 js-btn-block"
                                data-id="{{ $model->id }}"
                                data-morph-class="{{ $model->getMorphClass() }}"
                            >
                                <div class="form__image form__image--fluid">
                                    <div class="form__image-loader">
                                        <div class="loader"></div>
                                    </div>
                                    <input type="file" name="file" class="js-upload-images hidden" multiple id="upload-images">
                                    <label class="form__image-button js-add-images-btn" for="upload-images">
                                        <i class="fas fa-plus"></i>
                                    </label>
                                </div>
                            </div>
                        @else
                            <div
                                class="col-xs-6 col-sm-2 js-btn-block"
                                data-id="{{ $model->id }}"
                                data-morph-class="{{ $model->getMorphClass() }}"
                            >
                                <div class="form__image form__image--fluid">
                                    <div class="form__image-loader">
                                        <div class="loader"></div>
                                    </div>
                                    <input type="file" name="file" class="js-upload-images hidden" multiple id="upload-images">
                                    <label class="form__image-button js-add-images-btn" for="upload-images">
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
