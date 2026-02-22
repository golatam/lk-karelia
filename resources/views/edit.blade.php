@extends('layouts.app')

@section('title'){{ $titleEdit }}@endsection

@push('header')
    <h1 class="h2 reset-m">@yield('title')</h1>
    <ol class="breadcrumb">
        {!! $breadcrumbs !!}
    </ol>
@endpush

@section('content')
    <form action="{{ route("{$entity}.update", $model) }}{{ isset($locale) ? $locale : '' }}" method="post" enctype="multipart/form-data">
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
