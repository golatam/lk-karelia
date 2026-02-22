<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="date_registration_charter">@lang("registers.registration_date_charter")</label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form__element form__element--large">
                <div>{{ $register?->registration_date_charter }}</div>
            </div>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="is_tos_legal_entity">@lang("registers.is_legal_entity")</label>
        </div>
        <div class="col-xs-12 col-sm-6">
            <div class="form__element form__element--large">
                <div>{{ !!$register?->is_legal_entity ? 'Да' : 'Нет' }}</div>
            </div>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="nomenclature_number">@lang("registers.nomenclature_number")</label>
        </div>
        <div class="col-xs-12">
            @if(!!$register?->nomenclature_number)
            <div class="form__element form__element--large">
                <div>{{ $register?->nomenclature_number }}</div>
            </div>
            @else
            <input type="text" min="18" max="20" placeholder="0:00:00:000000000000" name="nomenclature_number" id="nomenclature_number" class="form__input form__input--medium js-vanilla-masker @error('nomenclature_number'){{ 'is-invalid' }}@enderror" value="{{ old('nomenclature_number') }}">
            @endif
        </div>
        <div class="col-xs-12">
            @error('nomenclature_number')
            <div class="form__message form__message--error">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="full_name_chairman_tos">@lang("registers.fio_chief")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__element form__element--large">
                <div>{{ $register?->fio_chief }}</div>
            </div>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="tos_address">@lang("registers.address")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__element form__element--large">
                <div>{{ $register?->address }}</div>
            </div>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="tos_phone">@lang("registers.phone_chief")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__element form__element--large">
                <div>{{ $register?->phone_chief }}</div>
            </div>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="tos_email">@lang("registers.email_chief")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__element form__element--large">
                <div>{{ $register?->email_chief }}</div>
            </div>
        </div>
    </div>
</div>
<div class="form__group form__group--input">
    <div class="row row--small row--ai-center">
        <div class="col-xs-12">
            <label class="form__label" for="population_size_in_tos">@lang("registers.number_citizens")</label>
        </div>
        <div class="col-xs-12">
            <div class="form__element form__element--large">
                <div>{{ $register?->number_citizens }}</div>
            </div>
        </div>
    </div>
</div>
