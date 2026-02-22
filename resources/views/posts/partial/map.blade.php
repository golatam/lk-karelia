<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12 col-sm-2 text-sm-right"></div>
        <div class="col-xs-12 col-sm-10">
            <div class="form__map" id="map"></div>
        </div>
    </div>
</div>
<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;amp;apikey={{ $yandexMapToken }}" type="text/javascript"></script>
<script>
    var map, coordinates, latitude = '62.276681', longitude = '32.123269', inputCoordinates;

    window.onload = function() {
        inputCoordinates = document.querySelector('.js-coordinates')

        if (inputCoordinates) {
            ymaps.ready(init);
        }
    };//62.276681, 32.123269

    function init() {
        coordinates = inputCoordinates.value.split(",")
        if (coordinates.length > 1) {
            [latitude, longitude] = coordinates
        } else {
            inputCoordinates.placeholder = latitude + ', ' + longitude
        }

        map = new ymaps.Map("map", {
            center: [latitude, longitude],
            zoom: 8
        });

        map.behaviors.disable('scrollZoom');

        // Если есть данные
        placemark = new ymaps.Placemark([latitude, longitude], {}, {
            iconLayout: 'default#image',
            iconImageHref: '{{ asset("assets/images/marker.svg") }}',
            iconImageSize: [30, 40],
            iconImageOffset: [-15, -40],
            draggable: 'true'
        });

        placemark.events.add("dragend", function (e) {

            var coords = e.get('target').geometry.getCoordinates(),
                inputCoordinates = document.querySelector('.js-coordinates')

            inputCoordinates.value = coords[0].toPrecision(6) + ', ' + coords[1].toPrecision(6)
        });

        map.events.add("click", function (e) {

            var coords = e.get('coords'),
                inputCoordinates = document.querySelector('.js-coordinates')

            if (placemark) {
                placemark.geometry.setCoordinates(coords)
            }

            if (inputCoordinates) {
                inputCoordinates.value = coords[0].toPrecision(6) + ', ' + coords[1].toPrecision(6)
            }
        });

        map.geoObjects.add(placemark);
    }
</script>
<style>
    .form__map {
        height: 15rem;
        width: 100%;
    }
</style>
