@extends('layouts.app')

@section('title'){{ $titleRestore }}@endsection

@push('header')
    <h1 class="h2 reset-m">@yield('title')</h1>
    <ol class="breadcrumb">
        {!! $breadcrumbs !!}
    </ol>
@endpush

@section('content')
    <div class="panel">
        <div class="panel__head">
            <div class="panel__actions">
                <div class="panel__actions-item">
                    <a href="{{ route("{$entity}.index") }}" class="btn btn--medium-square-xs btn--gray">
                        <i class="fas fa-reply btn__icon"></i>
                        <span class="btn__text btn__text--right xs-hide">@lang('common.back')</span>
                    </a>
                </div>
                <div class="panel__actions-item">
                @can('restore', $model)
                    <button type="button" class="btn btn--medium-square-xs btn--green js-restore-items" style="display: none;">
                        <i class="fas fa-trash-restore-alt btn__icon"></i>
                        <span class="btn__text btn__text--right xs-hide">@lang('common.restore')</span>
                    </button>
                @endcan
                </div>
                <div class="panel__actions-item right">
                    <a href="#filter" class="btn btn--{{ $filter['used'] ? 'orange' : 'white' }} btn--medium-square js-slide-toggle">
                        <i class="fas fa-filter"></i>
                    </a>
                </div>
                @include("partial.fields")
            </div>
        </div>
        @include("{$entity}.filter")
        <div class="panel__body">
            <form action="{{ route("{$entity}.restore") }}" method="post" enctype="multipart/form-data" id="restore-form">
                <div class="panel__table-wrap">
                    @csrf
                    <table class="panel__table">
                        @if($models->isNotEmpty())
                        <thead>
                            <tr>
                                <th class="check">
                                    <label class="form__checkbox">
                                        <input class="form__checkbox-input js-checks" type="checkbox">
                                        <span class="form__checkbox-icon"></span>
                                    </label>
                                </th>
                                <th class="id">
                                    @if(in_array('id', $fieldsSorting))
                                        <a class="js-sort-btn" data-sort-column="id" data-sort-direction="{{ 'id' === $column ? $directions->diff([$direction])->first() : $directionDefault }}" data-entity="{{ $model->entity() }}" href="javascript:void(0);">
                                            @lang("{$entity}.id")
                                            <i class="fas fa-sort{{ 'id' === $column ? $direction === 'asc' ? '-down' : '-up' : '' }}"></i>
                                        </a>
                                    @else
                                        @lang("{$entity}.id")
                                    @endif
                                </th>
                                @foreach($model->fieldsSelected() as $field)
                                    @if($field === 'id')
                                    @elseif(in_array($field, $fieldsImages))
                                        <th class="img">@lang("{$entity}.{$field}")</th>
                                    @else
                                        <th class="@if (in_array($field, $fieldsFull)){{ 'full' }}@endif">
                                            @if(in_array($field, $fieldsSorting))
                                                <a class="js-sort-btn" data-sort-column="{{ $field }}" data-sort-direction="{{ $field === $column ? $directions->diff([$direction])->first() : $directionDefault }}" data-entity="{{ $model->entity() }}" href="javascript:void(0);">
                                                    @lang("{$entity}.{$field}")
                                                    <i class="fas fa-sort{{ $field === $column ? $direction === 'asc' ? '-down' : '-up' : '' }}"></i>
                                                </a>
                                            @else
                                                @lang("{$entity}.{$field}")
                                            @endif
                                        </th>
                                    @endif
                                @endforeach
                                <th>@lang('common.actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($models as $key => $model)
                                <tr data-id="{{ $model->id }}" data-model-full-name="{{ $modelFullName }}">
                                    <td class="check">
                                        <label class="form__checkbox">
                                            <input class="form__checkbox-input js-check" value="{{ $model->id }}" name="ids[]" type="checkbox">
                                            <span class="form__checkbox-icon"></span>
                                        </label>
                                    </td>
                                    <td class="id">
                                        {{ $model->id }}
                                    </td>
                                    @foreach($model->fieldsSelected() as $field)
                                        @if($field === 'id')
                                        @elseif(in_array($field, $fieldsImages))
                                        <td class="img">
                                            @if(empty($model->{$field}))
                                                <img src="{{ asset("assets/images/no-photo.jpg") }}" alt="{{ $model->{$field} }}">
                                            @else
                                                <img src="{{ asset(image_path("{$model->$field}", 'thumbnail')) }}" alt="{{ $model->{$field} }}">
                                            @endif
                                        </td>
                                        @elseif(in_array($field, $fieldsCheckbox))
                                        <td class="full">
                                            <label class="form__toggle">
                                                <input class="form__toggle-input js-active-toggle" type="checkbox" name="{{ $field }}" {{ $model->{$field} ? 'checked' : '' }} disabled>
                                                <span class="form__toggle-icon"></span>
                                            </label>
                                        </td>
                                        @else
                                        <td class="full">
                                            @if (in_array($field, array_keys($fieldsRelationships)))
                                                {!! isset($model->{$fieldsRelationships[$field]}->name) ? $model->{$fieldsRelationships[$field]}->name : '---' !!}
                                            @elseif (in_array($field, array_keys($fieldsConfig)))
                                                {!!  isset(${$fieldsConfig[$field]}[$model->{$field}]) ? ${$fieldsConfig[$field]}[$model->{$field}] : '---'  !!}
                                            @else
                                                {!! $model->{$field} !!}
                                            @endif
                                        </td>
                                        @endif
                                    @endforeach
                                    <td>
                                    @can('restore', $model)
                                        <button type="button" class="btn btn--green btn--default-square js-restore-item">
                                            <i class="fas fa-trash-restore-alt"></i>
                                        </button>
                                    @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        @else
                        <tbody>
                            <tr>
                                <td colspan="{{ count($model->fieldsSelected()) }}" style="text-align: center; vertical-align: middle;">
                                    @lang('common.elements_are_missing')
                                </td>
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
    @include("modals.restore")
@endsection
