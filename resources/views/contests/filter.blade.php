<div id="filter" class="panel__filter panel__content hidden">
    <form action="{{ route("{$entity}.filter") }}" method="post" enctype="multipart/form-data" class="form" id="filter-form">
        @csrf
        <input type="hidden" name="page" value="{{ request()->input('page') }}">
        <input type="hidden" name="sort_column" value="{{ $columnDefault }}">
        <input type="hidden" name="sort_direction" value="{{ $directionDefault }}">
        <input type="hidden" name="method" value="{{ $redirectRouteName }}">
        <div class="row form__group-wrap">
            <div class="col-xs-12 col-sm-4">
                <div class="form__group form__group--input">
                    <label class="form__label form__label--medium" for="id">ID</label>
                    <input class="form__input form__input--medium" type="text" name="id" id="id" placeholder="99" value="{{ $filter['id'] }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form__group form__group--input">
                    <label class="form__label form__label--medium" for="name">@lang("{$entity}.name")</label>
                    <input class="form__input form__input--medium" type="text" name="name" id="name" placeholder="@lang("common.enter_text_part")" value="{{ $filter['name'] }}">
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form__group form__group--input">
                    <label class="form__label form__label--medium" for="is_active">@lang("{$entity}.is_active")</label>
                    <select name="is_active" id="is_active" class="form__select form__select--medium">
                        <option value="">@lang("common.select")</option>
                        <option value="0" @if (!is_null($filter['is_active']) && !$filter['is_active']){{ 'selected' }}@endif>Неактивный</option>
                        <option value="1" @if (!!$filter['is_active']){{ 'selected' }}@endif>Активный</option>
                    </select>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="form__group form__group--btn">
                    <div class="form__row">
                        <div class="form__col">
                            <button class="btn btn--blue btn--medium" type="submit">
                                <i class="fas fa-filter"></i>
                                <span class="btn__text--right">@lang("common.filter")</span>
                            </button>
                        </div>
                        <div class="form__col">
                            <a class="btn btn--medium btn--orange" href="{{ route("{$entity}.filter") }}?method={{ $redirectRouteName }}&reset">@lang("common.reset")</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
