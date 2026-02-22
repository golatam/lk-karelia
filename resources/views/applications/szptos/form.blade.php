<div class="row">
    <div class="col-xs-12">
        <div class="tabs">
            <ul class="tabs__links">
                <li>
                    <a class="tabs__link active" href="#common">@lang("{$entity}_applications.common")</a>
                </li>
                @if($model->exists && !empty($model->register_id))
                    <li>
                        <a class="tabs__link" href="#registers">@lang("{$entity}_applications.registers") ({{ $model->register?->name_according_charter }})</a>
                    </li>
                @endif
            </ul>
            <div class="tabs__items">
                <div class="tabs__item active" id="common">
                    <div class="panel__body">
                        <div class="panel__content">
                            <div class="row form__group-wrap">
                                <div class="col-xs-12">
                                    @includeIf("applications.{$entity}.common")
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @if($model->exists && !empty($model->register_id))
                    <div class="tabs__item" id="registers">
                        <div class="panel__body">
                            <div class="panel__content">
                                <div class="row form__group-wrap">
                                    <div class="col-xs-12">
                                        @includeIf("applications.{$entity}.registers", ['register' => $model->register])
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
            </div>
        </div>
    </div>
</div>
