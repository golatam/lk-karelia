@extends('layouts.app')

@section('title'){{ $titleCreate }}@endsection

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
    @if(!$contest)
        <div class="panel">
            <div class="panel__head">
                <div class="panel__actions">
                    <div class="panel__actions-item">
                        <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                            <i class="fas fa-reply btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.back')</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel__body">
                <div class="panel__content">
                    <div class="row form__group-wrap">
                        <div class="col-xs-12">
                            {{ 'Отсутствует актуальный конкурс!!!' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel__footer">
                <div class="panel__actions">
                    <div class="panel__actions-item">
                        <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                            <i class="fas fa-reply btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.back')</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @elseif(auth()->user()->verificationUserDataFilling())
        <div class="panel">
            <div class="panel__head">
                <div class="panel__actions">
                    <div class="panel__actions-item">
                        <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                            <i class="fas fa-reply btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.back')</span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="panel__body">
                <div class="panel__content">
                    <div class="row form__group-wrap">
                        <div class="col-xs-12">
                            {{ 'В профиле пользователя заполнены не все необходимые поля!!!' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel__footer">
                <div class="panel__actions">
                    <div class="panel__actions-item">
                        <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                            <i class="fas fa-reply btn__icon"></i>
                            <span class="btn__text btn__text--right">@lang('common.back')</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <form action="{{ route("applications.{$entity}.store") }}{{ isset($locale) ? $locale : '' }}" method="post" enctype="multipart/form-data" class="js-save-draft-data" data-ajax-url="/ajax/saveDraftData">
            @csrf
            <input type="hidden" name="morph_class" value="{{ $model->getMorphClass() }}">
            <input type="hidden" name="model_id" value="0">
            <div class="panel">
                <div class="panel__head">
                    <div class="panel__actions">
                        <div class="panel__actions-item">
                            <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                                <i class="fas fa-reply btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.back')</span>
                            </a>
                        </div>
                        <div class="panel__actions-item right">
                            <button type="submit" name="draft" value="1" class="btn btn--medium btn--green">
                                <i class="fas fa-check btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.save_as_draft')</span>
                            </button>
                        </div>
                        <div class="panel__actions-item">
                            <button type="submit" name="published" value="1" class="btn btn--medium btn--green">
                                <i class="fas fa-check btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.save')</span>
                            </button>
                        </div>
                    </div>
                </div>
                @include("applications.{$entity}.form")
                <div class="panel__footer">
                    <div class="panel__actions">
                        <div class="panel__actions-item">
                            <a href="{{ route("applications.{$entity}.index") }}{{ session()->has("{$entity}.page") ? "?page=" . session()->get("{$entity}.page") : '' }}" class="btn btn--medium btn--gray">
                                <i class="fas fa-reply btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.back')</span>
                            </a>
                        </div>
                        <div class="panel__actions-item right">
                            <button type="submit" name="draft" value="1" class="btn btn--medium btn--green">
                                <i class="fas fa-check btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.save_as_draft')</span>
                            </button>
                        </div>
                        <div class="panel__actions-item">
                            <button type="submit" name="published" value="1" class="btn btn--medium btn--green">
                                <i class="fas fa-check btn__icon"></i>
                                <span class="btn__text btn__text--right">@lang('common.save')</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
@endsection
