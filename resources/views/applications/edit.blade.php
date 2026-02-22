@extends('layouts.app')

@section('title'){{ $titleEdit }}@endsection

@push('header')
    <h1 class="h2 reset-m">@yield('title')</h1>
    <ol class="breadcrumb">
        {!! $breadcrumbs !!}
    </ol>
@endpush

@section('content')
    @if (session()->exists('success') && session('success'))
        <div class="alert alert-success" role="alert">{{ session('message') }}</div>
    @elseif(session()->exists('success') && !session('success'))
        <div class="alert alert-danger" role="alert">{{ session('message') }}</div>
    @else
        @if($errors->isNotEmpty())
        @foreach($errors->all() as $key => $error)
        <div class="alert alert-danger" role="alert">{{ $error }}</div>
        @endforeach
        @endif
    @endif
    @if($contest)
        <form action="{{ route("applications.{$entity}.update", $model) }}{{ isset($locale) ? $locale : '' }}" method="post" enctype="multipart/form-data" @if(!$model->exists || ($model->exists && ($model->status === 'draft' || empty($model->status)))) class="js-save-draft-data" data-ajax-url="/ajax/saveDraftData" @endif>
            @method('put')
            @csrf
            @if(!$model->exists || ($model->exists && ($model->status === 'draft' || empty($model->status))))
                <input type="hidden" name="morph_class" value="{{ $model->getMorphClass() }}">
                <input type="hidden" name="model_id" value="{{ $model->exists ? $model->id : 0 }}">
            @endif
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__actions">
                        <div class="panel__actions-item">
                            <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                                <i class="fas fa-reply btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.back')</span>
                            </a>
                        </div>
                        @includeIf("applications.{$entity}.buttons-edit")
                        @if($model->exists && $model->status === 'draft')
                            <div class="panel__actions-item right">
                                <button type="submit" name="draft" value="1" class="btn btn--medium btn--green">
                                    <i class="fas fa-check btn__icon"></i>
                                    <span class="btn__text btn__text--right">@lang('common.save_as_draft')</span>
                                </button>
                            </div>
                        @endif
                        @if(auth()->user()->hasPermissions(['other.show_admin']) || (!auth()->user()->hasPermissions(['other.show_admin']) && in_array($model->contest->is_active, [1])))
                        <div class="panel__actions-item{{ $model->exists && $model->status === 'draft' ? '' : ' right' }}">
                            <button type="submit" name="published" value="1" class="btn btn--medium btn--green">
                                <i class="fas fa-check btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.save')</span>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                @includeIf("applications.{$entity}.export-item-btn")
                @include("applications.{$entity}.form")
                <div class="panel__footer">
                    <div class="panel__actions">
                        <div class="panel__actions-item">
                            <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                                <i class="fas fa-reply btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.back')</span>
                            </a>
                        </div>
                        @if($model->exists && $model->status === 'draft')
                            <div class="panel__actions-item right">
                                <button type="submit" name="draft" value="1" class="btn btn--medium btn--green">
                                    <i class="fas fa-check btn__icon"></i>
                                    <span class="btn__text btn__text--right">@lang('common.save_as_draft')</span>
                                </button>
                            </div>
                        @endif
                        @if(auth()->user()->hasPermissions(['other.show_admin']) || (!auth()->user()->hasPermissions(['other.show_admin']) && in_array($model->contest->is_active, [1])))
                        <div class="panel__actions-item{{ $model->exists && $model->status === 'draft' ? '' : ' right' }}">
                            <button type="submit" name="published" value="1" class="btn btn--medium btn--green">
                                <i class="fas fa-check btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.save')</span>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </form>
        @if((auth()->user()->isShowComittee() || auth()->user()->hasPermissions(['other.show_admin'])) && isset($estimateColumns))
            <div class="panel__body">
                <div class="panel__content">
                    <h4>Оценка</h4>
                    <form action="{{ Route::has("applications.{$entity}.estimate") ? route("applications.{$entity}.estimate") : '' }}" method="POST">
                        @csrf
                        <input type="hidden" name="morph_class" value="{{ $model->getMorphClass() }}">
                        <input type="hidden" name="model_id" value="{{ $model->exists ? $model->id : 0 }}">
                        @foreach($estimateColumns as $key => $column)
                            @if($loop->index === 6 && $estimates->where('application_score_column_id', $column->id + 1)->first()
                                && $estimates->where('application_score_column_id', $column->id + 2)->first() && $model instanceof \App\Models\LPTOSApplication)
                                Финансовая эффективность проекта (доля привлеченных средств и результаты реализованной практики (проекта) в том числе):
                                <input  type="number" disabled
                                        value="{{ $estimates->where('application_score_column_id', $column->id)->first()->value +
                                                $estimates->where('application_score_column_id', $column->id + 1)->first()->value
                                                }}" class="form__input form__input--large">

                            @endif
                            {{ $column->name }}@if($model instanceof \App\Models\LPTOSApplication) <br><small>(Коэффициент значимости - {{ $column->significance_factor }})</small> @endif
                            <input  required type="number" step="0.01" max="{{ $column->max_rating }}" name="columns[{{ $column->id }}]"
                                    value="{{ $estimates->where('application_score_column_id', $column->id)->first()?->value }}" class="form__input form__input--large">

                        @endforeach
                        <br>

                        <button type="submit" class="btn btn--medium btn--green">Отправить</button>

                    </form>
                    @if($totalRating)
                        <p>Общий балл - {{ $totalRating }}</p>
                    @endif
                </div>
            </div>
        @endif
    @else
        <div class="panel__body">
            <div class="panel__content">
                <div class="row form__group-wrap">
                    <div class="col-xs-12">
                        {{ 'Отсутствует актуальный конкурс!!!' }}
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection
