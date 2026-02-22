<div id="filter" class="panel__filter panel__content hidden">
    <form action="{{ route("applications.{$entity}.filter") }}" method="post" enctype="multipart/form-data" class="form" id="filter-form">
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
                    <label class="form__label form__label--medium" for="municipality_id">@lang("{$entity}_applications.municipality_id")</label>
                    <select name="municipality_id" id="municipality_id" class="form__select form__select--medium">
                        <option value="" >Выбрать</option>
                        @foreach($municipalities as $municipalityId => $municipalityName)
                            <option value="{{ $municipalityId }}" @if ((int) $filter['municipality_id'] === (int) $municipalityId){{ 'selected' }}@endif>{{ $municipalityName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form__group form__group--input">
                    <label class="form__label form__label--medium" for="year">@lang("{$entity}_applications.year")</label>
                    <select name="year_id" id="year" class="form__select form__select--medium">
                        <option value="" >Выбрать</option>
                        @foreach($years as $yearId => $year)
                            <option value="{{ $yearId }}"{{ in_array($yearId, [$filter['year_id']]) ? ' selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form__group form__group--input">
                    <label class="form__label form__label--medium" for="status">@lang("{$entity}_applications.status")</label>
                    <select name="status" id="status" class="form__select form__select--medium">
                        <option value="" >Выбрать</option>
                        @foreach($statuses as $statusKey => $statusName)
                            <option value="{{ $statusKey }}" @if ((string) $filter['status'] === (string) $statusKey){{ 'selected' }}@endif>{{ $statusName }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-4">
                <div class="form__group form__group--input">
                    <label class="form__label form__label--medium" for="from_total_application_points">@lang("{$entity}_applications.total_application_points")</label>
                    <div class="row row--small">
                        <div class="col-xs-6">
                            <input
                                class="form__input form__input--medium"
                                type="text"
                                name="from_total_application_points"
                                id="from_total_application_points"
                                placeholder="@lang("common.from")"
                                value="{{ $filter['from_total_application_points'] }}"
                            >
                        </div>
                        <div class="col-xs-6">
                            <label for="to_total_application_points"></label>
                            <input
                                class="form__input form__input--medium"
                                type="text"
                                name="to_total_application_points"
                                id="to_total_application_points"
                                placeholder="@lang("common.to")"
                                value="{{ $filter['to_total_application_points'] }}"
                            >
                        </div>
                    </div>
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
                            <a class="btn btn--medium btn--orange" href="{{ route("applications.{$entity}.filter") }}?method={{ $redirectRouteName }}&reset">@lang("common.reset")</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
