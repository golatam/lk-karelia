<div class="form__image"
     data-id="{{ $model->id }}"
     data-entity="{{ $model->getTable() }}"
     data-column-name="value"
     data-image-file-path="{{ $model->value }}"
     data-model-full-name="{{ $model->getMorphClass() }}"
>
    <button class="form__image-delete js-remove-image{{ empty($model->value) ? ' hidden' : '' }}">Удалить</button>
    <div class="form__image-loader">
        <div class="loader"></div>
    </div>
    <img
        src="{{ !empty($model->value) ? image_path($model->value, 'thumbnail') : '' }}"
        class="form__image-img{{ empty($model->value) ? ' hidden' : '' }}"
        alt="">
    <input
        type="hidden"
        name="value"
        value="{{ !empty($model->value) ? $model->value : '' }}">
    <input type="file" name="file" class="js-upload-image hidden" id="value">
    <label class="form__image-button js-add-image-btn" for="value">
        <i class="fas fa-camera"></i>
    </label>
</div>
