@extends('layouts.app')

@section('title'){{ $titleIndex }}@endsection

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
                    <a href="{{ route("{$entity}.create") }}" class="btn btn--medium-square-xs btn--green">
                        <i class="fas fa-plus btn__icon"></i>
                        <span class="btn__text btn__text--right xs-hide">@lang('common.create')</span>
                    </a>
                </div>
                @include("{$entity}.partial.fields")
            </div>
        </div>
        @include("{$entity}.filter")
        <div class="panel__body">
            <div class="panel__table-wrap">
                <table class="panel__table">
                    @if($models->isNotEmpty())
                    <thead>
                        <tr>
                            <th class="id">
                            @if(in_array('id', $fieldsSorting))
                                <a class="js-sort-btn" data-sort-column="id" data-sort-direction="{{ 'id' === $column ? $directions->diff([$direction])->first() : $directionDefault }}" data-entity="{{ $entity }}" href="javascript:void(0);">
                                    @lang("{$entity}.id")
                                    <i class="fas fa-sort{{ 'id' === $column ? $direction === 'asc' ? '-down' : '-up' : '' }}"></i>
                                </a>
                            @else
                                @lang("{$entity}.id")
                            @endif
                            </th>
                            @foreach($fieldsSelected as $field)
                            @if($field === 'id')
                            @elseif(in_array($field, $fieldsImages))
                                <th class="img">@lang("{$entity}.{$field}")</th>
                            @else
                                <th class="@if (in_array($field, $fieldsFull)){{ 'full' }}@endif">
                                @if(in_array($field, $fieldsSorting))
                                    <a class="js-sort-btn" data-sort-column="{{ $field }}" data-sort-direction="{{ $field === $column ? $directions->diff([$direction])->first() : $directionDefault }}" data-entity="{{ $entity }}" href="javascript:void(0);">
                                        @lang("{$entity}.{$field}")
                                        <i class="fas fa-sort{{ $field === $column ? $direction === 'asc' ? '-down' : '-up' : '' }}"></i>
                                    </a>
                                @else
                                    @lang("{$entity}.{$field}")
                                @endif
                                </th>
                            @endif
                            @endforeach
                            <th></th>{{--                           @lang('common.actions')--}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($models as $key => $model)
                            <tr data-id="{{ Arr::get($model, 'id', '') }}" data-model-full-name="{{ $modelFullName }}" data-entity="{{ $entity }}">
                                <td class="id">
                                    @if(in_array('id', $fieldsLinks))
                                        <a href="{{ route("{$entity}.edit", Arr::get($model, 'id', '')) }}" class="btn btn--white btn--default">{{ Arr::get($model, 'id', '') }}</a>
                                    @else
                                        {{ Arr::get($model, 'id', '') }}
                                    @endif
                                </td>
{{--                                    @dd($model)--}}
                                @foreach($fieldsSelected as $field)
                                @if($field === 'id')
                                @elseif(in_array($field, $fieldsCheckbox))
                                    <td class="full">
                                        <label class="form__toggle">
                                            <input class="form__toggle-input js-active-toggle" type="checkbox" name="{{ $field }}" {{ Arr::get($model, $field, 0) ? 'checked' : '' }} {{ 'disabled' }}>
                                            <span class="form__toggle-icon"></span>
                                        </label>
                                    </td>
                                @else
                                    <td class="full">
                                    @if(in_array($field, $fieldsLinks))
                                        <a href="{{ route("{$entity}.edit", Arr::get($model, 'id', '')) }}">
                                            @if (in_array($field, array_keys($fieldsRelationships)))
                                                {!!  isset($model->{$fieldsRelationships[$field]}->name) ? $model->{$fieldsRelationships[$field]}->name : '---'  !!}
                                            @elseif (in_array($field, array_keys($fieldsConfig)))
                                                {!!  isset(${$fieldsConfig[$field]}[$model->{$field}]) ? ${$fieldsConfig[$field]}[$model->{$field}] : '---'  !!}
                                            @else
                                                {!!  Arr::get($model, $field, '')  !!}
                                            @endif
                                        </a>
                                    @else
                                        @if (in_array($field, array_keys($fieldsRelationships)))
                                            {!! isset($model->{$fieldsRelationships[$field]}->name) ? $model->{$fieldsRelationships[$field]}->name : '---' !!}
                                        @elseif (in_array($field, array_keys($fieldsConfig)))
                                            {!!  isset(${$fieldsConfig[$field]}[$model->{$field}]) ? ${$fieldsConfig[$field]}[$model->{$field}] : '---'  !!}
                                        @else
                                            {!! Arr::get($model, $field, '') !!}
                                        @endif
                                    @endif
                                    </td>
                                @endif
                                @endforeach
                                <td></td>
                            </tr>
                        @endforeach
                    </tbody>
                    @else
                    <tbody>
                        <tr>
                            <td colspan="{{ count($fieldsSelected) }}" style="text-align: center; vertical-align: middle;">@lang('common.elements_are_missing')</td>
                        </tr>
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
        @if($models->hasPages())
        <div class="panel__footer">
            {{ $models->links("partial.pagination") }}
        </div>
        @endif
    </div>
@endsection
