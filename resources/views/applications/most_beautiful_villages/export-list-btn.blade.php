@if(Route::has("applications.{$entity}.export.list") && auth()->user()->hasPermissions(['other.show_admin']))
<div class="panel__head">
    <div class="panel__actions">
        <div class="panel__actions-item">
            <a href="{{ route("applications.{$entity}.export.list", ['type' => 'pdf']) }}" class="btn btn--medium btn--gray" target="_blank">
                <i class="fas fa-file-pdf btn__icon btn--red"></i>
                <span class="btn__text btn__text--right">Экспорт рейтинговой таблицы проектов в Pdf</span>
            </a>
        </div>
        <div class="panel__actions-item">
            <a href="{{ route("applications.{$entity}.export.list", ['type' => 'excel']) }}" class="btn btn--medium btn--gray" target="_blank">
                <i class="fas fa-file-excel btn__icon btn--green"></i>
                <span class="btn__text btn__text--right">Экспорт рейтинговой таблицы проектов в Excel</span>
            </a>
        </div>
    </div>
</div>
@endif
