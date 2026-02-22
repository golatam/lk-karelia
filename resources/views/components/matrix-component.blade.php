@if ($matrixModels->isEmpty())
    <div class="form__group-wrap js-group-items">
        <div class="row row--small js-group-item">
            @foreach($matrixFields as $field)
                <div class="col-xs-12 col-sm-{{ $colNumber }}">
                    <div class="form__group form__group--input">
                        @if($fieldType === 'input')
                        <input name="{{ $fieldName }}[{{ $field }}][]" class="form__input form__input--medium" type="text" placeholder="@lang("{$entity}.matrix.{$fieldName}.{$field}")"{{ auth()->user()->isShowComittee() ? ' disabled' : '' }}>
                        @elseif($fieldType === 'textarea')
                        <textarea name="{{ $fieldName }}[{{ $field }}][]" class="form__textarea form__textarea--medium" placeholder="@lang("{$entity}.matrix.{$fieldName}.{$field}")"{{ auth()->user()->isShowComittee() ? ' disabled' : '' }}></textarea>
                        @else
                        @endif
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
        @foreach($matrixModels as $key => $item)
            <div class="row row--small js-group-item">
                @foreach($matrixFields as $field)
                    <div class="col-xs-12 col-sm-{{ $colNumber }}">
                        <div class="form__group form__group--input">
                            @if($fieldType === 'input')
                                <input name="{{ $fieldName }}[{{ $field }}][]" class="form__input form__input--medium" value="{{ $item->{$field} }}" type="text" placeholder="@lang("{$entity}.matrix.{$fieldName}.{$field}")"{{ auth()->user()->isShowComittee() ? ' disabled' : '' }}>
                            @elseif($fieldType === 'textarea')
                                <textarea name="{{ $fieldName }}[{{ $field }}][]" class="form__textarea form__textarea--medium" placeholder="@lang("{$entity}.matrix.{$fieldName}.{$field}")"{{ auth()->user()->isShowComittee() ? ' disabled' : '' }}>{{ $item->{$field} }}</textarea>
                            @else
                            @endif
                        </div>
                    </div>
                @endforeach
                @if(!auth()->user()->isShowComittee())
                <div class="col-xs-12 col-sm-2">
                    <div class="form__group form__group--input">
                        <button class="btn btn--medium-square btn--red @if (!$key){{ 'hidden' }}@endif js-delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </div>
                @endif
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
