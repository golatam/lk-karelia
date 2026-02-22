<div class="row row--small row--ai-center">
    <div class="col-xs-12 col-sm-2 text-sm-right">
        <label class="form__label form__label--sm-left">@lang("{$entity}.image")</label>
    </div>
    <div class="col-xs-12 col-sm-10">
        <div class="form__image">
            <button type="button" class="form__image-delete js-{{ $entity }}-remove-image{{ empty($image) ? ' hidden' : '' }}" data-post-id="{{ Arr::get($model, 'id', 0) }}">Удалить</button>
            <div class="form__image-loader">
                <div class="loader"></div>
            </div>
            <img
                src="{{ !empty($image) ? $image : '' }}"
                class="form__image-img{{ empty($image) ? ' hidden' : '' }}"
                alt="">
            <input type="file" name="image" class="js-{{ $entity }}-upload-image hidden" id="image">
            <label class="form__image-button js-{{ $entity }}-add-image-btn" for="image">
                <i class="fas fa-camera"></i>
            </label>
        </div>
    </div>
</div>
<script>
    let entity = "{{ $entity }}";
    // window.onload = function() {

        const uploadImageAll = document.querySelectorAll('.js-' + entity + '-upload-image')
        uploadImageAll.forEach(function (uploadImage) {
            uploadImage.addEventListener('change', function (e) {
                const [file] = uploadImage.files
                const img = e.target.closest('.form__image').querySelector("img")
                if (file && img) {
                    img.src = URL.createObjectURL(file)
                    img.classList.remove('hidden')
                }

            })
        })
        // Удаление картинки
        const removeImageBtnAll = document.querySelectorAll(".js-{{ $entity }}-remove-image")
        removeImageBtnAll.forEach(function (removeImageBtn) {
            removeImageBtn.addEventListener('click', function (e) {
                e.preventDefault()
                sendData('{{ route('posts.removeFile') }}', 'POST', { ...e.target.dataset }, removeFileSuccess, removeFileError)
            })
        })
        function sendData(url, method, data, successCallback, errorCallback) {
            const XHR = new XMLHttpRequest();
            // Что происходит при успешной отправке данных
            XHR.addEventListener("load", (event) => successCallback(event))
            // Что происходит в случае ошибки
            XHR.addEventListener("error", (event) => errorCallback(event))
            XHR.open(method, url)
            XHR.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}')
            XHR.setRequestHeader("Content-Type", "application/json")
            XHR.send(JSON.stringify(data));
        }
        function removeFileSuccess(event) {
            const response = event.target.responseText
            console.log('event', response.message)
            location.reload()
        }
        function removeFileError(event) {
            console.log('event', event.target.response.message)
        }
    // };
</script>
