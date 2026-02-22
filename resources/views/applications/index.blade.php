@extends('layouts.app')

@section('title'){{ $titleIndex }}@endsection

@push('header')
    <h1 class="h2 reset-m">@yield('title')</h1>
    <ol class="breadcrumb">
        {!! $breadcrumbs !!}
    </ol>
@endpush

@section('content')
    @if(count($searching))
    <div class="searching">
        <form action="{{ url('/ajax/searching') }}" class="searching__form form">
            @csrf
            <input type="hidden" name="db" value="{{ $entity }}">
            <input type="hidden" name="byID" value="">
            <span class="searching__icon">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="s" class="form__input form__input--large js-searching" placeholder="Поиск по базе...">
        </form>
        <div class="searching__result hidden"></div>
    </div>
    @endif
    @if(session()->has('message'))
    <div class="panel">
        <div class="alert alert-{{ session()->get('status') === 'success' ? 'success' : 'danger' }}">
            {{ session()->get('message') }}
        </div>
    </div>
    @endif
    <div class="panel">
        <div class="panel__head">
            <div class="panel__actions">
                @if (Route::has("applications.{$entity}.create"))
                <div class="panel__actions-item">
                @can('create', $model)
                    <a href="{{ route("applications.{$entity}.create") }}" class="btn btn--medium-square-xs btn--green">
                        <i class="fas fa-plus btn__icon"></i>
                        <span class="btn__text btn__text--right xs-hide">@lang('common.create')</span>
                    </a>
                @endcan
                </div>
                @endif
                @if (Route::has("applications.{$entity}.destroy"))
                <div class="panel__actions-item">
                @can('delete', $model)
                    <button type="button" class="btn btn--medium-square-xs btn--orange js-delete-items" style="display: none;">
                        <i class="fas fa-trash-alt btn__icon"></i>
                        <span class="btn__text btn__text--right xs-hide">@lang('common.delete')</span>
                    </button>
                @endcan
                </div>
                @endif
                @includeIf("applications.{$entity}.buttons")
                <div class="panel__actions-item right">
                @if (Route::has("applications.{$entity}.restore"))
                @can('restore', $model)
                    <a href="{{ route("applications.{$entity}.restore") }}" class="btn btn--medium-square-xs btn--white">
                        <i class="fas fa-trash-restore-alt btn__icon"></i>
                        <span class="btn__text btn__text--right xs-hide">@lang('common.trash')</span>
                    </a>
                @endcan
                @endif
                </div>
                <div class="panel__actions-item">
                    <a href="#filter" class="btn btn--white btn--medium-square js-slide-toggle">
                        <i class="fas fa-filter"></i>
                        @if($filter['used'] && $models->isNotEmpty() && isset($models_count))
                        <span class="btn__text btn__text--right">{{ $models_count }}</span>
                        @endif
                    </a>
                </div>
                @if($filter['used'])
                <div class="panel__actions-item">
                    <a class="btn btn--medium-square btn--orange" href="{{ route("applications.{$entity}.filter") }}?method={{ $redirectRouteName }}&reset">
                        <i class="fas fa-filter"></i>
                        <span class="btn__reset-line"></span>
                    </a>
                </div>
                @endif
                @include("applications.partial.fields")
            </div>
        </div>
        @include("applications.{$entity}.filter")
        @includeIf("applications.{$entity}.export-list-btn")
        <div class="panel__body">
            @if (Route::has("applications.{$entity}.destroy"))
            <form action="{{ route("applications.{$entity}.destroy") }}" id="delete-form" method="post" enctype="multipart/form-data">
            @endif
                @method('DELETE')
                @csrf
                <div class="panel__table-wrap">
                    <table class="panel__table">
                        @if($models->isNotEmpty())
                        <thead>
                            <tr>
                                @if (Route::has("applications.{$entity}.destroy"))
                                <th class="check">
                                    <label class="form__checkbox">
                                        <input class="form__checkbox-input js-checks" type="checkbox">
                                        <span class="form__checkbox-icon"></span>
                                    </label>
                                </th>
                                @endif
                                <th class="id">
                                @if(in_array('id', $fieldsSorting))
                                    <a class="js-sort-btn" data-sort-column="id" data-sort-direction="{{ 'id' === $column ? $directions->diff([$direction])->first() : $directionDefault }}" data-entity="{{ $model->entity() }}" href="javascript:void(0);">
                                        @lang("{$entity}_applications.id")
                                        <i class="fas fa-sort{{ 'id' === $column ? $direction === 'asc' ? '-down' : '-up' : '' }}"></i>
                                    </a>
                                @else
                                    @lang("{$entity}_applications.id")
                                @endif
                                </th>
                                @foreach($model->fieldsSelected() as $field)
                                @if($field === 'id')
                                @elseif(in_array($field, $fieldsImages))
                                    <th class="img">@lang("{$entity}_applications.{$field}")</th>
                                @else
                                    <th class="@if (in_array($field, $fieldsFull)){{ 'full' }}@endif">
                                    @if(in_array($field, $fieldsSorting))
                                        <a class="js-sort-btn" data-sort-column="{{ $field }}" data-sort-direction="{{ $field === $column ? $directions->diff([$direction])->first() : $directionDefault }}" data-entity="{{ $model->entity() }}" href="javascript:void(0);">
                                            {{ Str::limit(__("{$entity}_applications.{$field}"), 20, '...') }}
                                            <i class="fas fa-sort{{ $field === $column ? $direction === 'asc' ? '-down' : '-up' : '' }}"></i>
                                        </a>
                                    @else
                                            {{ Str::limit(__("{$entity}_applications.{$field}"), 20, '...') }}
                                    @endif
                                    </th>
                                @endif
                                @endforeach
                                @if (Route::has("applications.{$entity}.destroy"))
                                <th>@lang('common.actions')</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($models as $key => $model)
                                <tr data-id="{{ $model->id }}" data-model-full-name="{{ $modelFullName }}" data-entity="{{ $model->entity() }}">
                                    @if (Route::has("applications.{$entity}.destroy"))
                                    <td class="check">
                                        <label class="form__checkbox">
                                            <input class="form__checkbox-input js-check" value="{{ $model->id }}" name="ids[]" type="checkbox">
                                            <span class="form__checkbox-icon"></span>
                                        </label>
                                    </td>
                                    @endif
                                    <td class="id">
                                        @if(in_array('id', $fieldsLinks) && auth()->user()->can('update', $model))
                                            <a href="{{ route("applications.{$entity}.edit", $model) }}" class="btn btn--white btn--default">{{ $model->id }}</a>
                                        @else
                                            {{ $model->id }}
                                        @endif
                                    </td>
                                    @if($model->getTable() === 'settings' && $model->type === 'image')
                                        @php(array_push($fieldsImages, 'value'))
                                    @endif
                                    @foreach($model->fieldsSelected() as $field)
                                    @if($field === 'id')
                                    @elseif(in_array($field, $fieldsImages))
                                        <td class="img">
                                        @if(in_array($field, $fieldsLinks) && auth()->user()->can('update', $model))
                                            <a href="{{ route("applications.{$entity}.edit", $model) }}">
                                                @if(empty($model->{$field}))
                                                    <img src="{{ asset("assets/images/no-photo.jpg") }}" alt="{{ $model->{$field} }}">
                                                @else
                                                    <img src="{{ asset(image_path("{$model->$field}", 'thumbnail')) }}" alt="{{ $model->{$field} }}">
                                                @endif
                                            </a>
                                        @else
                                            @if(empty($model->{$field}))
                                                <img src="{{ asset("assets/images/no-photo.jpg") }}" alt="{{ $model->{$field} }}">
                                            @else
                                                <img src="{{ asset(image_path("{$model->$field}", __("{$entity}.{$field}") !== 'Фото широкое' ? 'thumbnail' : '')) }}" alt="{{ $model->{$field} }}">
                                            @endif
                                        @endif
                                        </td>
                                    @elseif(in_array($field, $fieldsCheckbox))
                                        <td class="full">
                                            <label class="form__toggle">
                                                <input class="form__toggle-input js-active-toggle" type="checkbox" name="{{ $field }}" {{ $model->{$field} ? 'checked' : '' }} @cannot('update', $model){{ 'disabled' }}@endcannot>
                                                <span class="form__toggle-icon"></span>
                                            </label>
                                        </td>
                                    @else
                                        <td class="full">
                                        @if(in_array($field, $fieldsLinks) && auth()->user()->can('update', $model))
                                            <a href="{{ route("applications.{$entity}.edit", $model) }}">
                                                @if (in_array($field, array_keys($fieldsRelationships)))
                                                    {!!  isset($model->{$fieldsRelationships[$field]}->name) ? Str::limit($model->{$fieldsRelationships[$field]}->name, 20, '...') : '---'  !!}
                                                @elseif (in_array($field, array_keys($fieldsConfig)))
                                                    {!!  isset(${$fieldsConfig[$field]}[$model->{$field}]) ? Str::limit(${$fieldsConfig[$field]}[$model->{$field}], 20, '...') : '---'  !!}
                                                @else
                                                    {!!  Str::limit($model->{$field}, 20, '...')  !!}
                                                @endif
                                            </a>
                                        @else
                                            @if (in_array($field, array_keys($fieldsRelationships)))
                                                {!! isset($model->{$fieldsRelationships[$field]}->name) ? Str::limit($model->{$fieldsRelationships[$field]}->name, 20, '...') : '---' !!}
                                            @elseif (in_array($field, array_keys($fieldsConfig)))
                                                {!!  isset(${$fieldsConfig[$field]}[$model->{$field}]) ? Str::limit(${$fieldsConfig[$field]}[$model->{$field}], 20, '...') : '---'  !!}
                                            @else
                                                {!! Str::limit($model->{$field}, 20, '...') !!}
                                            @endif
                                        @endif
                                        </td>
                                    @endif
                                    @endforeach
                                    @if (Route::has("applications.{$entity}.destroy"))
                                    <td>
                                        @includeIf("{$entity}.menu-items-btn")
                                        @includeIf("{$entity}.show-btn")
                                        @can('delete', $model)
                                        <button type="button" class="btn btn--orange btn--default-square js-delete-item">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @if(Route::has("applications.{$entity}.export.item") && $model->isPublished)
                                        <a href="{{ route("applications.{$entity}.export.item", ['type' => 'pdf', 'application' => $model->id]) }}" class="btn btn--default-square btn--gray" target="_blank" title="Экспорт заявки в Pdf">
                                            <i class="fas fa-file-pdf btn--red"></i>
                                        </a>
                                        <a href="{{ route("applications.{$entity}.export.item", ['type' => 'word', 'application' => $model->id]) }}" class="btn btn--default-square btn--gray" target="_blank" title="Экспорт заявки в Word">
                                            <i class="fas fa-file-word"></i>
                                        </a>
                                        @endif
                                        @endcan
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        @else
                        <tbody>
                            <tr>
                                <td colspan="{{ count($model->fieldsSelected()) }}" style="text-align: center; vertical-align: middle;">@lang('common.elements_are_missing')</td>
                            </tr>
                        </tbody>
                        @endif
                    </table>
                </div>
            </form>
        </div>
        @if($models->hasPages())
        <div class="panel__footer">
            {{ $models->links("partial.pagination") }}
        </div>
        @endif
    </div>
    @include("modals.remove")
    @includeIf("{$entity}.modals")
@endsection
