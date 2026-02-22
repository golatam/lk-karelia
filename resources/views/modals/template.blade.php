<div class="modal" id="js-template-create-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container">
            <button type="button" class="modal__close js-close" data-micromodal-close></button>
            <div class="modal__header">
                <h4 class="reset-m">@lang('app.common.creating_from_template')</h4>
            </div>
            <form action="{{ route("schedule.creating-from-template") }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal__body">
                    <div class="form__group form__group--input">
                        <label class="form__label form__label--medium" for="city">@lang("app.{$entity}.select_city")</label>
                        <select name="city_id" id="city" class="form__select form__select--medium js-template-change-city">
                            <option value="" >Выбрать</option>
                            @foreach($citiesTemplate as $key => $city)
                                <option value="{{ $key }}">{{ $city }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form__group form__group--input">
                        <label class="form__label form__label--medium" for="address">@lang("app.{$entity}.select_address")</label>
                        <select name="address" id="address" class="form__select form__select--medium js-template-block-address">
                            <option value="" >Выбрать</option>
                        </select>
                    </div>
                    <div class="form__group form__group--input">
                        <label class="form__label form__label--medium" for="date">@lang("app.{$entity}.select_date")</label>
                        <input type="text" name="date" id="date" class="form__input form__input--medium form__date">
                    </div>
                </div>
                <div class="modal__actions">
                    <div class="modal__action">
                        <button type="button" class="btn btn--medium btn--gray" data-micromodal-close>@lang('app.common.cancel')</button>
                    </div>
                    <div class="modal__action">
                        <button type="submit" class="btn btn--medium btn--green js-creating-from-template-btn">@lang('app.common.create')</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
