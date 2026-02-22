<div class="modal" id="js-restore-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container">

            <button type="button" class="modal__close js-close" data-micromodal-close></button>

            <div class="modal__header">
                <h4 class="reset-m">@lang('common.restoring')</h4>
            </div>

            <div class="modal__body">@lang('common.restoring_info')</div>

            <div class="modal__actions">
                <div class="modal__action">
                    <button type="button" class="btn btn--medium btn--gray" data-micromodal-close>@lang('common.cancel')</button>
                </div>
                <div class="modal__action">
                    <button type="button" class="btn btn--medium btn--green js-submit-restore-form">@lang('common.restore')</button>
                </div>
            </div>

        </div>
    </div>
</div>
