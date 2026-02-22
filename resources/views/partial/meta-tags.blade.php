@if($model->methodExists('metaTags'))
<div class="panel__head">
    <h4 class="reset-m">@lang("common.meta_tags")</h4>
</div>
<div class="panel__body">
    <div class="panel__content">
        <div class="row form__group-wrap">
            <div class="col-xs-12">
                @include('partial.fields.input', [
                                            'type' => 'text',
                                            'field_name' => 'meta_title',
                                            'field_value' => $model->metaTitle,
                                            'required' => false
                                            ])

                @include('partial.fields.input', [
                                            'type' => 'text',
                                            'field_name' => 'meta_keywords',
                                            'field_value' => $model->metaKeywords,
                                            'required' => false
                                            ])
                @include('partial.fields.input', [
                                            'type' => 'text',
                                            'field_name' => 'meta_description',
                                            'field_value' => $model->metaDescription,
                                            'required' => false
                                            ])

            </div>
        </div>
    </div>
</div>
@endif
