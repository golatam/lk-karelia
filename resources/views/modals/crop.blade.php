<div class="modal" id="js-image-crop-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1">
        <div class="modal__container modal__container--large">
            <button type="button" class="modal__close js-close" data-micromodal-close></button>
            <div class="modal__header">
                <h4 class="reset-m">Выберите область фотографии</h4>
            </div>
            <div class="modal__body">
                <img class="modal__image-crop js-image-crop" src="{{ !empty($imageFilePath) ? image_path($imageFilePath, 'thumbnail') : '' }}">
            </div>
            <div class="modal__actions">
                <div class="modal__action">
                    <button type="button" class="btn btn--medium btn--green js-crop-btn">Сохранить</button>
                </div>
                <div class="modal__action">
                    <button type="button" class="btn btn--medium btn--gray" data-micromodal-close>Отмена</button>
                </div>
            </div>
        </div>
    </div>
</div>
