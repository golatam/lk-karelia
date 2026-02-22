<div class="form__group-wrap js-group-items mb-5">
    <div class="row row--small js-group-item">
        @foreach($fields as $field)
        <div class="col-xs-12 col-sm-{{ $field === 'field26' ? '7' : '3' }}">
            <div class="form__group form__group--input">
                @lang("{$entity}_applications.matrix.{$fieldName}.{$field}")
            </div>
        </div>
        @endforeach
        <div class="col-xs-12 col-sm-2">
            <div class="form__group form__group--input">
                @lang('common.actions')
            </div>
        </div>
    </div>
</div>
@if ($models->isEmpty())
<div class="form__group-wrap js-group-items">
    <div class="row row--small js-group-item">
        @foreach($fields as $field)
        <div class="col-xs-12 col-sm-{{ $field === 'field26' ? '7' : '3' }}">
            <div class="form__group form__group--input">
                <input @if(auth()->user()->isShowComittee()) disabled @endif name="{{ $fieldName }}[{{ $field }}][]" class="form__input form__input--medium" type="text" placeholder="@lang("{$entity}_applications.matrix.{$fieldName}.{$field}")">
            </div>
        </div>
        @endforeach
        @if(!auth()->user()->isShowComittee())
        <div class="col-xs-12 col-sm-2">
            <div class="form__group form__group--input">
                <button class="btn btn--medium-square btn--red hidden js-delete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        @endif
    </div>
    @if(!auth()->user()->isShowComittee())
    <div class="form__group form__group--input js-group-btn">
        <button class="btn btn--medium btn--green js-add">
            <i class="fas fa-plus"></i>
            <span class="btn__text btn__text--right">Добавить</span>
        </button>
    </div>
    @endif
</div>
@else
<div class="form__group-wrap js-group-items">
    @foreach($models as $key => $item)
    <div class="row row--small js-group-item">
        @foreach($fields as $field)
        <div class="col-xs-12 col-sm-{{ $field === 'field26' ? '7' : '3' }}">
            <div class="form__group form__group--input">
                @if($field === 'field26')
                    <textarea name="{{ $fieldName }}[{{ $field }}][]" class="form__textarea form__textarea--medium" type="text" placeholder="@lang("{$entity}_applications.matrix.{$fieldName}.{$field}")">{{ $item->{$field} }}</textarea>
                @else
                    <input name="{{ $fieldName }}[{{ $field }}][]" class="form__input form__input--medium" value="{{ $item->{$field} }}" type="text" placeholder="@lang("{$entity}_applications.matrix.{$fieldName}.{$field}")">
                @endif
            </div>
        </div>
        @endforeach
        <div class="col-xs-12 col-sm-2">
            <div class="form__group form__group--input">
                <button class="btn btn--medium-square btn--red @if (!$key){{ 'hidden' }}@endif js-delete">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
    </div>
    @endforeach
    @if(!auth()->user()->isShowComittee())
    <div class="form__group form__group--input js-group-btn">
        <button class="btn btn--medium btn--green js-add">
            <i class="fas fa-plus"></i>
            <span class="btn__text btn__text--right">Добавить</span>
        </button>
    </div>
    @endif
</div>
@endif

