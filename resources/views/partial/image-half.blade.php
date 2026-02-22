<div class="row row--small row--ai-center">
    <div class="col-xs-12 col-sm-5 text-sm-right">
        <label class="form__label form__label--sm-left">{{ $name }}</label>
    </div>
    <div class="col-xs-12 col-sm-7">
        <div class="form__image"
             data-id="{{ $id }}"
             data-entity="{{ $entity }}"
             data-column-name="{{ $columnName }}"
             data-image-file-path="{{ $imageFilePath }}"
             data-model-full-name="{{ $modelFullName }}"
        >
            <button class="form__image-delete js-remove-image{{ empty($imageFilePath) ? ' hidden' : '' }}">Удалить</button>
            <div class="form__image-loader">
                <div class="loader"></div>
            </div>
            <img
                src="{{ !empty($imageFilePath) ? image_path($imageFilePath, 'thumbnail') : '' }}"
                class="form__image-img{{ empty($imageFilePath) ? ' hidden' : '' }}"
                alt="">
            <input
                type="hidden"
                name="{{ $columnName }}"
                value="{{ !empty($imageFilePath) ? $imageFilePath : '' }}">
            <input type="file" name="file" class="js-upload-image hidden" id="{{ $columnName }}">
            <label class="form__image-button js-add-image-btn" for="{{ $columnName }}">
                <i class="fas fa-camera"></i>
            </label>
        </div>
    </div>
</div>
