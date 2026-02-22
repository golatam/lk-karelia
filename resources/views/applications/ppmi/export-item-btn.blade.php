@if(Route::has("applications.{$entity}.export.item") && $model->isPublished)
<div class="panel__head">
    <div class="panel__actions">
        <div class="panel__actions-item">
            <a href="{{ route("applications.{$entity}.export.item", ['type' => 'pdf', 'application' => $model->id]) }}" class="btn btn--medium btn--gray" target="_blank">
                <i class="fas fa-file-pdf btn__icon btn--red"></i>
                <span class="btn__text btn__text--right">Экспорт заявки в Pdf</span>
            </a>
        </div>
        <div class="panel__actions-item">
            <a href="{{ route("applications.{$entity}.export.item", ['type' => 'word', 'application' => $model->id]) }}" class="btn btn--medium btn--gray" target="_blank">
                <i class="fas fa-file-word btn__icon btn--blue"></i>
                <span class="btn__text btn__text--right">Экспорт заявки в Word</span>
            </a>
        </div>
    </div>
</div>
@endif
