<div class="panel__actions-item">
    <div class="dropdown">
        <a href="#" class="btn btn--white btn--medium-square dropdown__trigger">
            <i class="fas fa-ellipsis-v"></i>
        </a>
        <div class="dropdown__menu dropdown__menu--right">
            <form action="#" class="dropdown__form form" id="form-fields">
                <div class="dropdown__scroll">
                    <input class="hidden" name="td[]" type="checkbox" value="id" checked>
                    @foreach($model->fieldsForShowing() as $fieldModel)
                    <label class="form__checkbox">
                        <input class="form__checkbox-input" name="td[]" type="checkbox" value="{{ $fieldModel }}" @if (in_array($fieldModel, $model->fieldsSelected())){{ 'checked' }}@endif>
                        <span class="form__checkbox-icon"></span>
                        <span class="form__checkbox-label">@lang("{$entity}_applications.{$fieldModel}")</span>
                    </label>
                    @endforeach
                </div>
                <div class="dropdown__button">
                    <button
                        class="btn btn--default btn--green btn--full js-fields-save-btn"
                        type="submit"
                        data-entity="{{ $entity }}"
                    >
                        @lang("common.apply")
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
