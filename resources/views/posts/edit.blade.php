@extends('layouts.app')

@section('title'){{ $titleEdit }}@endsection

@push('header')
    <h1 class="h2 reset-m">@yield('title')</h1>
    <ol class="breadcrumb">
        {!! $breadcrumbs !!}
    </ol>
@endpush

@section('content')
    <form action="{{ route("{$entity}.update", Arr::get($model, 'id', 0)) }}{{ isset($locale) ? $locale : '' }}" method="post" enctype="multipart/form-data">
        @method('put')
        @csrf
        <div class="panel">
            <div class="panel__head">
                <div class="panel__actions">
                    <div class="panel__actions-item">
                        <a href="{{ route("{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                            <i class="fas fa-reply btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.back')</span>
                        </a>
                    </div>
                    @includeIf("{$entity}.buttons-edit")
                    <div class="panel__actions-item right">
                        <button type="submit" class="btn btn--medium btn--green">
                            <i class="fas fa-check btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.save')</span>
                        </button>
                    </div>
                </div>
            </div>
            @include("{$entity}.form")
            <div class="panel__footer">
                <div class="panel__actions">
                    <div class="panel__actions-item">
                        <a href="{{ route("{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                            <i class="fas fa-reply btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.back')</span>
                        </a>
                    </div>
                    <div class="panel__actions-item right">
                        <button type="submit" class="btn btn--medium btn--green">
                            <i class="fas fa-check btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.save')</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;amp;apikey=3144a72a-cb78-46fe-a0e6-5de0ad67a115" type="text/javascript"></script>
    <script>
        ymaps.ready(init);
        var map, latitude, longitude;

        function init() {
            latitude = {{ auth()->user()->latitude }};
            longitude = {{ auth()->user()->longitude }};
            map = new ymaps.Map("map", {
                center: [latitude, longitude],
                zoom: 8
            });

            map.behaviors.disable('scrollZoom');

            // Если есть данные
            placemark = new ymaps.Placemark([latitude, longitude], {}, {
                iconLayout: 'default#image',
                iconImageHref: '{{ asset("assets/frontend/img/svg/marker.svg") }}',
                iconImageSize: [30, 40],
                iconImageOffset: [-15, -40],
                draggable: 'true'
            });

            placemark.events.add("dragend", function (e) {

                var coords = e.get('target').geometry.getCoordinates(),
                    latitudeInput = document.querySelector('input#latitude'),
                    longitudeInput = document.querySelector('input#longitude');

                latitudeInput.value = coords[0].toPrecision(6);
                longitudeInput.value = coords[1].toPrecision(6);

            });

            map.geoObjects.add(placemark);
        }
    </script>
@endpush
