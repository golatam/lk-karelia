import $ from 'jquery';
window.$ = window.jQuery = $;

import 'jquery-ui/ui/widgets/autocomplete';
import 'jquery-ui/ui/widget';
import 'jquery-ui/ui/widgets/mouse';
import 'jquery-ui/ui/widgets/slider';
import 'jquery-ui/ui/widgets/datepicker';
import 'jquery-ui/ui/i18n/datepicker-ru';
import 'jquery-ui/ui/widgets/sortable.js';
import './libs/timepicker';
import 'select2/dist/js/select2.full.min';
import './libs/notify';
import MicroModal from 'micromodal/dist/micromodal';
import VMasker from 'vanilla-masker/build/vanilla-masker.min';

$.timepicker.regional['ru'] = {
    timeOnlyTitle: 'Выберите время',
    timeText: 'Время',
    hourText: 'Часы',
    minuteText: 'Минуты',
    secondText: 'Секунды',
    millisecText: 'Миллисекунды',
    timezoneText: 'Часовой пояс',
    currentText: 'Сейчас',
    closeText: 'Закрыть',
    timeFormat: 'HH:mm',
    amNames: ['AM', 'A'],
    pmNames: ['PM', 'P'],
    isRTL: false
};
$.timepicker.setDefaults($.timepicker.regional['ru']);

// Touch Slider UI
!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);

// Double Scroll
(function( $ ) {

    jQuery.fn.doubleScroll = function(userOptions) {

        // Default options
        var options = {
            contentElement: undefined, // Widest element, if not specified first child element will be used
            scrollCss: {
                'overflow-x': 'auto',
                'overflow-y': 'hidden',
                'height': '20px'
            },
            contentCss: {
                'overflow-x': 'auto',
                'overflow-y': 'hidden'
            },
            onlyIfScroll: true, // top scrollbar is not shown if the bottom one is not present
            resetOnWindowResize: false, // recompute the top ScrollBar requirements when the window is resized
            timeToWaitForResize: 30 // wait for the last update event (usefull when browser fire resize event constantly during ressing)
        };

        $.extend(true, options, userOptions);

        // do not modify
        // internal stuff
        $.extend(options, {
            topScrollBarMarkup: '<div class="doubleScroll-scroll-wrapper"><div class="doubleScroll-scroll"></div></div>',
            topScrollBarWrapperSelector: '.doubleScroll-scroll-wrapper',
            topScrollBarInnerSelector: '.doubleScroll-scroll'
        });

        var _showScrollBar = function($self, options) {

            if (options.onlyIfScroll && $self.get(0).scrollWidth <= $self.width()) {
                // content doesn't scroll
                // remove any existing occurrence...
                $self.prev(options.topScrollBarWrapperSelector).remove();
                return;
            }

            // add div that will act as an upper scroll only if not already added to the DOM
            var $topScrollBar = $self.prev(options.topScrollBarWrapperSelector);

            if ($topScrollBar.length == 0) {

                // creating the scrollbar
                // added before in the DOM
                $topScrollBar = $(options.topScrollBarMarkup);
                $self.before($topScrollBar);

                // apply the css
                $topScrollBar.css(options.scrollCss);
                $(options.topScrollBarInnerSelector).css("height", "20px");
                $self.css(options.contentCss);

                // bind upper scroll to bottom scroll
                $topScrollBar.bind('scroll.doubleScroll', function() {
                    $self.scrollLeft($topScrollBar.scrollLeft());
                });

                // bind bottom scroll to upper scroll
                var selfScrollHandler = function() {
                    $topScrollBar.scrollLeft($self.scrollLeft());
                };
                $self.bind('scroll.doubleScroll', selfScrollHandler);
            }

            // find the content element (should be the widest one)
            var $contentElement;

            if (options.contentElement !== undefined && $self.find(options.contentElement).length !== 0) {
                $contentElement = $self.find(options.contentElement);
            } else {
                $contentElement = $self.find('>:first-child');
            }

            // set the width of the wrappers
            $(options.topScrollBarInnerSelector, $topScrollBar).width($contentElement.outerWidth());
            $topScrollBar.width($self.width());
            $topScrollBar.scrollLeft($self.scrollLeft());

        }

        return this.each(function() {

            var $self = $(this);

            _showScrollBar($self, options);

            // bind the resize handler
            // do it once
            if (options.resetOnWindowResize) {

                var id;
                var handler = function(e) {
                    _showScrollBar($self, options);
                };

                $(window).bind('resize.doubleScroll', function() {
                    // adding/removing/replacing the scrollbar might resize the window
                    // so the resizing flag will avoid the infinite loop here...
                    clearTimeout(id);
                    id = setTimeout(handler, options.timeToWaitForResize);
                });

            }

        });

    }

}( jQuery ));

(function ($) {
    'use strict';

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Init modals
    MicroModal.init();

    // Double scroll
    $('.panel__table-wrap').doubleScroll({
        resetOnWindowResize: true,
        onlyIfScroll: true,
    });

    // Toggle sidebar
    $('.hamburger, .sidebar__close').on('click', function() {
        $('.sidebar').toggleClass('open');
    });

    // Dropdown
    $('.dropdown__trigger').on('click', function(e) {
        $(this).next().toggleClass('open');
        e.preventDefault();
    });
    $(document).mouseup(function(e) {
        if (!e.target.closest('.dropdown')) {
            $('.dropdown__menu.open').removeClass('open');
        }
    });

    // Toggle menu
    $('.sidebar__menu li.has-sub > a').on('click', function(e) {
        $(this).parent().toggleClass('active');
        $(this).next().slideToggle(300);
        e.preventDefault();
    });

    // Select 2
    $('.form__select--large, .form__select--medium').on('select2:open', function() {
        if ($(window).outerWidth() < 1280) {
            $('.select2-search__field').prop('focus', true);
        }
    });
    $('.form__select--large').select2({
        width: '100%',
        theme: "default large",
        minimumResultsForSearch: 10,
        tags: true,
        tokenSeparators: [',']
    });
    $('.form__select--medium').select2({
        width: '100%',
        theme: "default medium",
        minimumResultsForSearch: 10,
        tags: true,
        tokenSeparators: [',']
    });

    // Checked all
    $('.js-checks').on('change', function () {

        var val = $(this).prop('checked'),
            buttonDelete = $('.js-delete-items'),
            buttonRestore = $('.js-restore-items');

        $('.js-check').prop('checked', val);

        if(val) {

            buttonDelete.show();
            buttonRestore.show();

        } else {

            buttonDelete.hide();
            buttonRestore.hide();

        }
    });
    $('.js-checks-all').on('change', function () {

        var val = $(this).prop('checked'),
            group = $(this).closest('.js-checks-group');

        group.find('.js-checks-item').prop('checked', val);
    });

    // Check item
    $('.js-check').on('change', function () {

        var value = $(this).prop('checked'),
            buttonDelete = $('.js-delete-items'),
            buttonRestore = $('.js-restore-items'),
            checksChecked = $('.js-check:checked').length,
            checks = $('.js-check:not(:checked)').length,
            checkAll = $('.js-checks');

        if(!value) {

            if(!checksChecked) {

                buttonDelete.hide();
                buttonRestore.hide();
            }

            checkAll.prop('checked', false);

        } else {

            if(!checks) { checkAll.prop('checked', true) }

            buttonDelete.show();
            buttonRestore.show();
        }

    });
    $('.js-checks-item').on('change', function () {

        var value = $(this).prop('checked'),
            group = $(this).closest('.js-checks-group'),
            checks = group.find('.js-checks-item:not(:checked)').length,
            checkAll = group.find('.js-checks-all');

        if(!value) {

            checkAll.prop('checked', false);

        } else {

            if(!checks) { checkAll.prop('checked', true) }
        }

    });

    // Delete item
    $('.js-delete-item').on('click', function () {

        $(this).closest('tr').find('.js-check').prop('checked', true);

        MicroModal.show('js-remove-modal');

    });

    // Delete items
    $('.js-delete-items').on('click', function () {

        MicroModal.show('js-remove-modal');

    });

    // Cancel modal
    $(document).on('click', '[data-micromodal-close]', function () {

        $('.js-check, .js-checks').prop('checked', false);
        $('.js-delete-items, .js-restore-items').hide();

    });

    $('.js-submit-delete-form').on('click', function () {
        var form = $('#delete-form'),
            action = form.attr('action'),
            data = form.serialize();

        $('.js-delete-items').hide();

        MicroModal.close('js-remove-modal');

        $.ajax({
            url: action,
            type: 'DELETE',
            data: data,
            dataType: 'json',
            success: function(result) {

                $.each(result.data, function (i, val) {

                    if (val.success) {

                        $.notify(val.message, "success");
                        $('tr[data-id="' + val.id + '"]').remove();
                    } else {

                        $.notify(val.message, "error");
                        $('tr[data-id="' + val.id + '"]').addClass('error').find('.js-check').prop('checked', true);
                        $('.js-delete-items').show();
                    }
                });
            },
            error: function(result) {
                $.notify(result, "error");
            }
        });

    });

    // Tabs
    $('.tabs__links').on('click', 'li a:not(.active)', function(e) {
        var tabs = $(this).closest('.tabs');
        tabs.find('.active').removeClass('active');
        $(this).addClass('active');
        tabs.find($(this).attr('href')).addClass('active');
        e.preventDefault();
    });

    // Js toggle
    $('.js-slide-toggle').on('click', function (e) {
        $($(this).attr('href')).toggleClass('hidden');
        e.preventDefault();
    });

    // Tinymce
    if( $('.form__tinymce').length > 0 ) {
        tinymce.init({
            selector: '.form__tinymce',
            height: '350px',
            language: 'ru',
            convert_urls: false,
            plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak media",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table directionality emoticons paste responsivefilemanager code"
            ],
            toolbar: "undo redo | styleselect | bold italic underline | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link unlink anchor image media | code",
            image_advtab: true ,
            external_filemanager_path:"/assets/plugins/filemanager/",
            filemanager_title:"Responsive Filemanager" ,
            external_plugins: { "filemanager" : "/assets/plugins/filemanager/plugin.min.js"},
            image_class_list: [
                {title: 'None', value: ''},
                {title: 'Слева', value: 'content-images-1'},
                {title: 'По центру', value: 'content-images-2'},
                {title: 'Справа', value: 'content-images-3'},
                {title: 'Слева с превью', value: 'content-images-1 glightbox'},
                {title: 'По центру с превью', value: 'content-images-2 glightbox'},
                {title: 'Справа с превью', value: 'content-images-3 glightbox'}
            ]
        });
    }

    // Air Datepicker
    $('.form__date').datepicker({
        dateFormat: 'yy-mm-dd',
        showOtherMonths: true,
        selectOtherMonths: true
    });

    $('.form__date-time').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm',
        stepHour: 1,
        stepMinute: 5,
        showOtherMonths: true,
        selectOtherMonths: true,
        showButtonPanel: false,
    });

    $('.form__time').timepicker({
        timeFormat: 'HH:mm',
        stepHour: 1,
        stepMinute: 5,
        showButtonPanel: false,
    });


    // Change type
    $('select[name=type_id]').on('change', function () {
        $('.types-item').addClass('hidden');
        $('.types-item--' + this.value).removeClass('hidden');
    });


    // Type id select change
    $('select[name="type_id"]').on('change', function () {
        $('select[name="will_be"]').val('').trigger('change');
    });

    // Searching
    $('.js-searching').on('keyup', function (e) {
        var $this = $(this),
            $form = $('.searching__form'),
            $result = $('.searching__result');

        if(e.which === 27) {
            $this.val('');
        }
        $form.find('[name="byID"]').val('');

        if(e.which === 32) {
            if(/^[0-9 ]+$/.test($this.val())) {
                $form.find('[name="byID"]').val(1);
            }
        }

        if($this.val().length > 0) {
            $.ajax({
                url: $form.attr('action'),
                type: 'post',
                data: $form.serialize(),
                dataType: 'json',
                success: function (result) {
                    if(result.data.length) {
                        var $html = '';
                        result.data.forEach(function (item) {
                            $html += `<a href="/${result.entity}/${item.id}${result.type}" class="searching__result-item">`;
                            for(var k in item) {
                                if(k !== 'type_id') {
                                    $html += `<span>${item[k]}</span>`;
                                }
                                if(result.types && result.types[item[k]]) {
                                    $html += `<span>${result.types[item[k]]}</span>`;
                                }
                            }
                            $html += `</a>`;
                        });
                    } else {
                        $html = `<div class="searching__result-item">Не найдено ни одного совпадения</div>`;
                    }
                    $result.html($html).removeClass('hidden');
                },
                error: function (result) {
                    console.log('Error', result);
                }
            });
        } else {
            $result.addClass('hidden');
        }
    });

    // Searching submit form
    $(document).on('submit', '.searching__form', function (e) {
        e.preventDefault();
        if($('.searching__result a').length === 1) {
            $('.searching__result a:first-child')[0].click();
        }
    });







    // Григорий

    // Переключение статуса
    $(document).on('click', '.js-active-toggle', function (e) {
        var
            $this = $(this),
            data = $this.closest('tr').data();
            data['is_active'] = +$this.prop('checked');
            data['name']    = $(this).attr('name')
        ;

        $.ajax({
            url: '/ajax/activeToggle',
            type: 'post',
            data: data,
            dataType: 'json',
            success: function (result) {

                if (result.success) {

                    $.notify(result.message, "success");
                } else {

                    $.notify(result.message, "error");
                }
            },
            error: function (result) {
                $.notify(result.message, "error");
            }
        });
    });

    // Вызов модального окна при нажатии частной кнопки восстановления
    $('.js-restore-item').on('click', function () {

        $(this).closest('tr').find('.js-check').prop('checked', true);

        MicroModal.show('js-restore-modal');
    });

    // Вызов модального окна при нажатии общей кнопки восстановления
    $('.js-restore-items').on('click', function () {

        MicroModal.show('js-restore-modal');
    });

    // Восстановление ajax
    $('.js-submit-restore-form').on('click', function () {
        var form = $('#restore-form'),
            action = form.attr('action'),
            data = form.serialize();

        $('.js-restore-items').hide();

        MicroModal.close('js-restore-modal');

        $.ajax({
            url: action,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(result) {

                $.each(result.data, function (i, val) {

                    if (val.success) {

                        $.notify(val.message, "success");
                        $('tr[data-id="' + val.id + '"]').remove();
                    } else {

                        $.notify(val.message, "error");
                        $('tr[data-id="' + val.id + '"]').addClass('error').find('.js-check').prop('checked', true);
                        $('.js-restore-items').show();
                    }
                });
            },
            error: function(result) {

                $.notify(result.message, "error");
            }
        });
    });

    // Сохраняем список выводимых полей в куку
    $(document).on('click', '.js-fields-save-btn', function (e){
        e.preventDefault();
        var $this = $(this),
            form = $this.closest('form'),
            checkboxChecked = form.find('[type="checkbox"]:checked'),
            data = [],
            cookieName = "fields_" + $this.data('entity');

        $.each(checkboxChecked, function (k, i) {

            data[k] = $(i).val();
        });

        set_cookie(cookieName, JSON.stringify(data));

        location.reload();
    });

    // Записываем в куки сортировку
    $(document).on('click', '.js-sort-btn', function (e){
        e.preventDefault();
        var $this = $(this),
            sortColumn = $this.data('sortColumn'),
            sortDirection = $this.data('sortDirection'),
            entity = $this.data('entity'),
            sorting,
            cookieName = 'sorting_' + $this.data('entity');

        sorting = JSON.parse(get_cookie(cookieName, '[]'));

        // console.log(sortColumn)
        // console.log(sortDirection)
        // console.log(sorting)

        sorting = {
            sortColumn: sortColumn,
            sortDirection: sortDirection,
        };


        set_cookie(cookieName, JSON.stringify(sorting));

        location.reload();
    });

    // При клике на пагинацию запускаем и фильтр
    $(document).on('click', '.pagination a.btn', function (e){
        var $this = $(this),
            form = $('#filter-form'),
            href = $this.attr('href').trim(),
            page = href.substring(href.indexOf('=') + 1), html;

        if(Number(page)) {
            form.find('[name="page"]').val(page);
        }

        html = '<input type="hidden" name="pagination" value="1">';
        form.find('[name="page"]').after(html)

        e.preventDefault();
        form.submit();
    });


    $(document).on('click', '.js-add', function (e){
        e.preventDefault();
        var $this = $(this),
            groupItems = $this.closest('.js-group-items'),
            groupItem = groupItems.find('.js-group-item:first-child'),
            groupBtn = $this.closest('.js-group-btn'),
            groupItemClone = groupItem.clone()
        ;
        groupItemClone.find('.js-delete').removeClass('hidden');
        groupItemClone.find('.form__date-time').removeClass("hasDatepicker").attr('id', '');
        groupItemClone.find('input').val('');
        groupBtn.before(groupItemClone);
        $('.form__date-time').datetimepicker({
            dateFormat: 'yy-mm-dd',
            timeFormat: 'HH:mm',
            stepHour: 1,
            stepMinute: 5,
            showOtherMonths: true,
            selectOtherMonths: true,
            showButtonPanel: false,
        });
    });

    $(document).on('click', '.js-delete', function (e){
        e.preventDefault();
        var $this = $(this),
            groupItem = $this.closest('.js-group-item')
        ;

        groupItem.remove();
    });


    var vanillaMasker = document.querySelector(".js-vanilla-masker")

    if (vanillaMasker) {
        VMasker(vanillaMasker).maskPattern("9:99:99:999999999999");
    }

    files();
    image();
    images();

    setInterval(function () {

        saveDraftData();
    }, 30000)
})(jQuery);

// Добавление множества файлов
function files(){

    // Добавление множества фото
    $(document).on('change', '.js-upload-files', function (e){

        var
            $this = $(this),
            ajaxData = new FormData(),
            accept = $this.attr('accept'),
            formGroup = $this.closest('.form__group'),
            data = {
                group: formGroup.find('[name="group"]').val(),
                modelId: formGroup.find('[name="model_id"]').val(),
                modelMorphClass: formGroup.find('[name="model_morph_class"]').val()
            },
            filesBlock = formGroup.find('.js-files-block'),
            html
        ;

        if (!!accept && accept.length) {

            accept = accept.split(',').map(function (accept) {return accept.replace('.', '')})
        } else {

            accept = [];
        }

        $.each($this[0].files, function (i, file) {
            if (!file.name.length) {

                $.notify("Файл отсутствует!", "error");
                return false;
            }
            var ext = file.name.match(/\.([^\.]+)$/)[1];

            if (!accept.length || (accept.length && accept.includes(ext))) {

                ajaxData.append('files[' + i + ']', file);
            }
        });

        $.each(data, function (k, i) {

            ajaxData.append(k, i);
        });

        $.ajax({
            url: '/ajax/uploadFiles',
            type: 'POST',
            data: ajaxData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (result) {

                if (result.success) {

                    $.each(result.files, function (k, i){

                        html = '<div class="row row--small row--ai-center">' +
                            '<div class="col-xs-9 col-sm-10 col-md-11">' +
                            '<div class="form__element form__element--large">' +
                            '<a href="' + i.path + '" class="btn btn--full btn--text-left btn--medium btn--gray" download>' +
                            '<i class="fas fa-file-' + i.extension + '"></i>' +
                            '<span class="btn__text btn__text--right">' + i.name + '</span>' +
                            '</a>' +
                            '</div>' +
                            '</div>' +
                            '<div class="col-xs-3 col-sm-2 col-md-1">' +
                            '<button type="button" class="btn btn--medium btn--orange btn--default-square js-remove-file-item" data-model-id="' + result.id + '" data-morph-class="' + result.morphClass + '" data-file-id="' + i.id + '">' +
                            '<i class="fas fa-trash-alt"></i>' +
                            '</button>' +
                            '</div>' +
                            '</div>';

                        filesBlock.append(html);
                    });

                    $.notify(result.message, "success");
                } else {

                    $.notify(result.message, "error");
                }
            },
            error: function (result) {

                $.notify(result.message, "error");
            }
        });

        e.preventDefault();
    });

    // Удаление фото множества
    $(document).on('click', '.js-remove-file-item', function (e){

        var
            $this = $(this),
            fileBlock = $this.closest('.row'),
            ajaxData = {
                modelId: $this.data('modelId'),
                morphClass: $this.data('morphClass'),
                fileId: $this.data('fileId')
            };

        $.ajax({
            url: '/ajax/removeFile',
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(result) {

                if (result.success) {

                    fileBlock.remove();

                    $.notify(result.message, "success");
                } else {

                    $.notify(result.message, "success");
                }
            },
            error: function(result) {

                $.notify(result.message, "success");
            }
        });

        e.preventDefault();
    });
}

function image() {
    // Добавление одного фото
    $(document).on('change', '.js-upload-image', function (e) {
        e.preventDefault();
        var $this = $(this),
            ajaxData = new FormData(),
            formImage = $this.closest('.form__image'),
            data = formImage.data();

        formImage.find('.form__image-loader').addClass('show');

        $.each($this[0].files, function (i, file) {
            if (!file.name.length) {

                $.notify("Файл отсутствует!", "error");
                return false;
            }
            ajaxData.append('files[' + i + ']', file);
        });

        $.each(data, function (k, i) {

            ajaxData.append(k, i);
        });

        $.ajax({
            url: '/ajax/uploadImage',
            type: 'POST',
            data: ajaxData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (result) {

                if (result.success) {

                    formImage.find('.form__image-loader').removeClass('show');
                    formImage.find('[name="' + data['columnName'] + '"]').val(result.imageFilePath);
                    formImage.find('img').attr('src', result.miniature).removeClass('hidden');
                    formImage.find('.js-remove-image').removeClass('hidden');
                    formImage.data('imageFilePath', result.imageFilePath);

                    if (result.imageHeader) {

                        $('.header__profile-photo').attr('src', result.imageFilePath);
                    }

                    $.notify(result.message, "success");
                } else {

                    formImage.find('.form__image-loader').removeClass('show');
                    $.notify(result.message, "error");
                }
            },
            error: function (result) {

                formImage.find('.form__image-loader').removeClass('show');
                $.notify(result.message, "error");
            }
        });
    });

    // Добавление одного фото с кропом
    $(document).on('change', '.js-upload-image-crop', function (e) {
        var file = this.files;
        if (file.length > 0) {
            var fileReader = new FileReader();

            fileReader.onload = function (event) {
                var $img = $('.js-image-crop');
                $img.attr('src', event.target.result);
                $img.cropper({
                    zoomable: false
                });
            };

            fileReader.readAsDataURL(file[0]);
        }

        MicroModal.show('js-image-crop-modal');

    });
    // Crop image & send ajax
    $(document).on('click', '.js-crop-btn', function () {

        var $image = $('.js-image-crop');

        $image.cropper("getCroppedCanvas").toBlob( (blob) => {
            // FormData is a built-in javascript object
            var formData = new FormData(),
                formImage = $('.form__image'),
                data = formImage.data();

            $.each(data, function (k, i) {
                formData.append(k, i);
            });

            formData.append("files[]", blob);

            $.ajax({
                url: '/ajax/uploadImage',
                type: 'POST',
                data: formData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                success: function (result) {

                    if (result.success) {

                        formImage.find('.form__image-loader').removeClass('show');
                        formImage.find('[name="' + data['columnName'] + '"]').val(result.imageFilePath);
                        formImage.find('img').attr('src', result.miniature).removeClass('hidden');
                        formImage.find('.js-remove-image').removeClass('hidden');
                        formImage.data('imageFilePath', result.imageFilePath);

                        if (result.imageHeader) {

                            $('.header__profile-photo').attr('src', result.imageFilePath);
                        }

                        $('.js-image-crop').cropper('destroy');

                        $.notify(result.message, "success");

                        MicroModal.close('js-image-crop-modal');
                    } else {

                        formImage.find('.form__image-loader').removeClass('show');
                        $.notify(result.message, "error");
                    }
                },
                error: function (result) {

                    formImage.find('.form__image-loader').removeClass('show');
                    $.notify(result.message, "error");
                }
            });
        });
    });

    // Удаление одного фото
    $(document).on('click', '.js-remove-image', function (e){
        e.preventDefault();
        var $this = $(this),
            formImage = $this.closest('.form__image'),
            inputFile = formImage.find('.js-upload-image'),
            ajaxData = formImage.data(),
            headerProfilePhoto = $('.header__profile-photo');

        formImage.find('.form__image-loader').addClass('show');

        $.ajax({
            url: '/ajax/deleteImage',
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(result) {

                if (result.success) {

                    formImage.find('.form__image-loader').removeClass('show');
                    formImage.find('[name="' + ajaxData['columnName'] + '"]').val('');
                    formImage.find('img').attr('src', '').addClass('hidden');
                    formImage.find('.js-remove-image').addClass('hidden');
                    formImage.data('imageFilePath', '');
                    inputFile.val('');

                    if (result.imageHeader) {

                        headerProfilePhoto.attr('src', headerProfilePhoto.data('srcDefault'));
                    }

                    $.notify(result.message, "success");
                } else {

                    formImage.find('.form__image-loader').removeClass('show');
                    $.notify(result.message, "error");
                }
            },
            error: function(result) {

                formImage.find('.form__image-loader').removeClass('show');
                $.notify(result.message, "error");
            }
        });
    });

    $(".js-recommended-autocomplete").autocomplete({
        minLength: 2,
        source: function (request, response){
            // организуем кроссдоменный запрос
            $.ajax({
                method: "post",
                url: "/ajax/articles",
                dataType: "json",
                // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                data:{
                    search: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    response(data);
                }
            });
        },
        select: function (e, ui){

            var
                recommendedItems = $(this).closest('.panel__content').find('.js-recommended-items'),
                html,
                articleId = recommendedItems.data('id')
            ;

            if (ui.item.success) {

                html = '<div class="row row--small js-recommended-item">\n' +
                    '        <div class="col-xs-10">\n' +
                    '            <div class="form__group form__group--input">\n' +
                    '                <input type="text" class="form__input form__input--medium" value="' + ui.item.value + '" disabled>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '        <div class="col-xs-2">\n' +
                    '            <div class="form__group form__group--input">\n' +
                    '                <button class="btn btn--medium-square btn--red js-delete-recommended-item" data-id="' + ui.item.id + '">\n' +
                    '                    <i class="fas fa-trash-alt"></i>\n' +
                    '                </button>\n' +
                    '            </div>\n' +
                    '        </div>\n' +
                    '    </div>';

                recommendedItems.append(html);

                // Присоединяем выбраную статью
                $.ajax({
                    method: "post",
                    url: "/ajax/setRecommendedArticle",
                    dataType: "json",
                    // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                    data:{
                        article_id: articleId,
                        recommended_id: ui.item.id,
                    }
                }).done(function (response) {

                    if (response.success) {

                        $.notify(response.message, "success");
                    } else {

                        $.notify(response.message, "error");
                    }
                }).fail(function (jqXHR, textStatus) {});
            } else {

                $.notify(ui.item.message, "error");
            }
        },
        change: function (e, ui){}
    });

    $(document).on('click', '.js-delete-recommended-item', function (e){
        e.preventDefault();
        var $this = $(this),
            data = {
                article_id: $this.closest('.js-recommended-items').data('id'),
                recommended_id: $this.data('id'),
            };

        $this.closest('.js-recommended-item').remove();

        // Отсоединяем выбраную статью
        $.ajax({
            method: "post",
            url: "/ajax/deleteRecommendedArticle",
            dataType: "json",
            // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('click', '.js-set-filter', function (e){
        e.preventDefault();
        var
            $this = $(this),
            data = $this.data()
        ;

        // Отсоединяем выбраную статью
        $.ajax({
            method: "post",
            url: "/ajax/setFilter",
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");
                setTimeout(function () {

                    location.href = $this.attr('href')
                }, 20)
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('click', '.js-notify-questionnaires', function (e){
        e.preventDefault();
        var
            $this = $(this),
            data = $this.data()
        ;

        // Отсоединяем выбраную статью
        $.ajax({
            method: "post",
            url: $this.attr('href'),
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");

                setTimeout(function () {

                    location.reload();
                }, 5000);
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('click', '.js-add-to-forum', function (e){
        e.preventDefault();
        var
            $this = $(this),
            formGroup = $this.closest('.form__group'),
            data = formGroup.data()
        ;

        $.ajax({
            method: "post",
            url: $this.data('path'),
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");

                setTimeout(function () {

                    location.reload();
                }, 5000);
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('click', '.js-activate-on-forum', function (e){
        e.preventDefault();
        var
            $this = $(this),
            formGroup = $this.closest('.form__group'),
            data = formGroup.data()
        ;

        $.ajax({
            method: "post",
            url: $this.data('path'),
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");

                setTimeout(function () {

                    location.reload();
                }, 5000);
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('click', '.js-deactivate-on-forum', function (e){
        e.preventDefault();
        var
            $this = $(this),
            formGroup = $this.closest('.form__group'),
            data = formGroup.data()
        ;

        $.ajax({
            method: "post",
            url: $this.data('path'),
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");

                setTimeout(function () {

                    location.reload();
                }, 5000);
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('click', '.js-send-credentials-from-forum', function (e){
        e.preventDefault();
        var
            $this = $(this),
            formGroup = $this.closest('.form__group'),
            data = formGroup.data()
        ;

        $.ajax({
            method: "post",
            url: $this.data('path'),
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.notify(response.message, "success");
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });

    $(document).on('change', '.js-template-change-city', function (e){
        e.preventDefault();
        var
            $this = $(this),
            templateBlockAddress = $this.closest('.modal__body').find('.js-template-block-address'),
            data = {
                "city_id": $this.find('option:selected').val()
            },
            html = '<option value="" >Выбрать</option>'
        ;

        $.ajax({
            method: "post",
            url: '/ajax/templateBlockAddress',
            dataType: "json",
            data:data
        }).done(function (response) {

            if (response.success) {

                $.each(response.venuesList, function (id, value){

                    html += '<option value="' + id + '" >' + value + '</option>'
                });

                $('.js-template-block-address').html(html);

                $.notify(response.message, "success");
            } else {

                $.notify(response.message, "error");
            }
        }).fail(function (jqXHR, textStatus) {});
    });
}// Смена позиции у фото

// Добавление множества фото
function images(){

    // Добавление множества фото
    $(document).on('change', '.js-upload-images', function (e){

        var
            $this = $(this),
            ajaxData = new FormData(),
            btnBlock = $this.closest('.js-btn-block'),
            data = $this.data(),
            html,
            description
        ;

        btnBlock
            .find('.form__image-loader')
            .addClass('show')
        ;

        $.each($this[0].files, function (i, file) {
            if (!file.name.length) {

                $.notify("Файл отсутствует!", "error");
                return false;
            }

            ajaxData.append('files[' + i + ']', file);
        });

        $.each(data, function (k, i) {

            ajaxData.append(k, i);
        });

        console.log($this.data());

        $.ajax({
            url: '/ajax/uploadImages',
            type: 'POST',
            data: ajaxData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (result) {

                if (result.success) {

                    btnBlock.find('.form__image-loader').removeClass('show');

                    $.each(result.images, function (k, i){

                        if (!!$this.data('hasDescription')) {

                            if ($this.data('typeDescription') === 'input') {

                                description = '<input class="form__image-description" type="text" name="images[' + i.id + ']" value="" placeholder="Описание">'
                            } else if ($this.data('typeDescription') === 'textarea') {

                                description = '<textarea class="form__image-description" name="images[' + i.id + ']" placeholder="Описание"></textarea>';
                            } else {

                                description = '';
                            }
                        } else {

                            description = '';
                        }

                        html = '<div class="col-xs-6 col-sm-2 js-images-item ' + $this.data('group') + '" data-position="' + i.position + '" data-id="' + result.id + '" data-image-id="' + i.id + '" data-morph-class="' + result.morphClass + '">' +
                            '<div class="form__image form__image--fluid">' +
                            '<button class="form__image-delete js-remove-images">Удалить</button>' +
                            '<img src="' + i.path + '" alt="" class="form__image-img">' +
                            '</div>' +
                            description +
                            '</div>';

                        btnBlock.before(html);
                    });

                    btnBlock.find('label').find('i').addClass('fa-plus').removeClass('fa-camera');

                    $.notify(result.message, "success");
                } else {

                    btnBlock.find('.form__image-loader').removeClass('show');
                    $.notify(result.message, "error");
                }
            },
            error: function (result) {

                btnBlock.find('.form__image-loader').removeClass('show');
                $.notify(result.message, "error");
            }
        });

        e.preventDefault();
    });

    // Удаление фото множества
    $(document).on('click', '.js-remove-images', function (e){

        e.preventDefault();

        var
            $this = $(this),
            imagesList = $this.closest('.js-images-list'),
            imagesItem = $this.closest('.js-images-item'),
            btnBlock = imagesList.find('.js-btn-block'),
            ajaxData = {
                id: imagesItem.data('id'),
                imageId: imagesItem.data('imageId'),
                morphClass: imagesItem.data('morphClass'),
                group: imagesItem.data('group')
            };

        btnBlock.find('.form__image-loader').addClass('show');

        $.ajax({
            url: '/ajax/removeImages',
            type: 'POST',
            data: ajaxData,
            dataType: 'json',
            success: function(result) {

                if (result.success) {

                    imagesItem.remove();

                    btnBlock.find('.form__image-loader').removeClass('show');

                    if (!imagesList.find('.js-images-item').length) {

                        btnBlock.find('label').find('i').removeClass('fa-plus').addClass('fa-camera');
                    }

                    $.notify(result.message, "success");
                } else {

                    btnBlock.find('.form__image-loader').removeClass('show');

                    $.notify(result.message, "success");
                }
            },
            error: function(result) {

                btnBlock.find('.form__image-loader').removeClass('show');

                $.notify(result.message, "success");
            }
        });
    });

    // Изменение позиции фото
    $('.js-change-position-images').sortable({
        tolerance: 'pointer',
        update: function(e, ui) {

            var
                imagesList = $('.js-images-list'),
                btnBlock = imagesList.find('.js-btn-block'),
                ajaxData = {},
                images = {}
            ;

            $.each(imagesList.find('.js-images-item.' + btnBlock.data('group')), function (k, i){

                $(i).data('position', k);

                images[k] = {
                    'imageId': $(i).data('imageId'),
                };
            });

            ajaxData['images'] = images;

            btnBlock.find('.form__image-loader').addClass('show');

            $.ajax({
                url: '/ajax/changePositionImages',
                type: 'POST',
                data: ajaxData,
                dataType: 'json',
                success: function (result) {

                    if (result.success) {

                        btnBlock.find('.form__image-loader').removeClass('show');
                        $.notify(result.message, "success");
                    } else {

                        btnBlock.find('.form__image-loader').removeClass('show');
                        $.notify(result.message, "error");
                    }
                },
                error: function (result) {

                    btnBlock.find('.form__image-loader').removeClass('show');
                    $.notify(result.message, "error");
                }
            });
        }
    });
}

/** Функции для работы с куками*/
function set_cookie(name, value, options) {

    var expires, date, updatedCookie, propName, propValue

    options = options || {};

    expires = options.expires || 31622400;
    options.path = '/';

    if (typeof expires == "number" && expires) {

        date = new Date();
        date.setTime(date.getTime() + expires * 1000);
        expires = options.expires = date;
    }
    if (expires && expires.toUTCString) {

        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    updatedCookie = name + "=" + value;

    for (propName in options) {

        updatedCookie += "; " + propName;
        propValue = options[propName];

        if (propValue !== true) {

            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

function delete_cookie(name) {

    set_cookie(name, "", {
        expires: -1
    })
}

function get_cookie(name, value) {

    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));

    return matches ? decodeURIComponent(matches[1]) : value;
}

function saveDraftData() {

    var
        form = $('form.js-save-draft-data'),
        url = form.data('ajaxUrl'),
        data = form.serialize()
    ;

    if (!!url) {

        $.ajax({
            url: url,
            type: 'post',
            data: data,
            dataType: 'json',
            success: function(result) {

                if (!!result.redirect) {

                    setTimeout(function () {

                        location.href = result.redirect;
                    }, 3000);
                }

                // $.notify(val.message, "success");
            },
            error: function(result) {

                // $.notify(result.message, "error");
            }
        });
    }
}
