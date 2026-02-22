@if ($models->isEmpty())
<div class="form__group-wrap js-group-items">
    <div class="row row--small js-group-item">
        @foreach($fields as $field)
        <div class="col-xs-12 col-sm-{{ floor((10/count($fields))) }} row">
            <input name="{{ $fieldName }}[{{ $field }}][]" class="form__input form__input--medium" type="text" placeholder="@lang("{$entity}_applications.matrix.{$fieldName}.{$field}")">
            <div class="form__group form__group--input">
                <input name="{{ $fieldName }}[{{ $field }}][]" class="form__input form__input--medium" type="text" placeholder="@lang("{$entity}_applications.matrix.{$fieldName}.{$field}")">
            </div>
        </div>
        @endforeach
        <div class="col-xs-12 col-sm-2">
            <button class="btn btn--medium-square btn--red hidden js-delete">
                <i class="fas fa-trash-alt"></i>
            </button>
            <div class="form__group form__group--input">
                <button class="btn btn--medium-square btn--red hidden js-delete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="form__group form__group--input js-group-btn">
        <button class="btn btn--medium btn--green js-add">
            <i class="fas fa-plus"></i>
            <span class="btn__text btn__text--right">Добавить</span>
        </button>
    </div>
</div>
@else
<div class="form__group-wrap js-group-items">
    @foreach($models as $key => $item)
    <div class="row row--small js-group-item">
        <div class="col-xs-5">
            <div class="form__group form__group--input">
                <input name="dates[start][]" class="form__input form__input--medium form__date-time" value="{{ $item->date_start }}" type="text" placeholder="@lang("app.{$entity}.date_start")"{{ $loop->first ? '' : ' required' }}>
            </div>
        </div>
        <div class="col-xs-5">
            <div class="form__group form__group--input">
                <input name="dates[end][]" class="form__input form__input--medium form__date-time" value="{{ $item->date_end }}" type="text" placeholder="@lang("app.{$entity}.date_end")"{{ $loop->first ? '' : ' required' }}>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="form__group form__group--input">
                <button class="btn btn--medium-square btn--red @if (!$key){{ 'hidden' }}@endif js-delete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
    <div class="form__group form__group--input js-group-btn">
        <button class="btn btn--medium btn--green js-add">
            <i class="fas fa-plus"></i>
            <span class="btn__text btn__text--right">Добавить</span>
        </button>
    </div>
</div>
@endif

