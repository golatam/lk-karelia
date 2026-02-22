@if(Route::has("{$entity}.export.list") && auth()->user()->hasPermissions(['other.show_admin', 'other.show_committee']))
<div class="panel__head">
    <div class="panel__actions">
        <div class="panel__actions-item">
            <a href="{{ route("{$entity}.export.list", ['type' => 'pdf']) }}" class="btn btn--medium btn--gray" target="_blank">
                <i class="fas fa-file-pdf btn__icon btn--red"></i>
                <span class="btn__text btn__text--right">Экспорт реестра в Pdf</span>
            </a>
        </div>
        <div class="panel__actions-item">
            <a href="{{ route("{$entity}.export.list", ['type' => 'excel']) }}" class="btn btn--medium btn--gray" target="_blank">
                <i class="fas fa-file-excel btn__icon btn--green"></i>
                <span class="btn__text btn__text--right">Экспорт реестра в Excel</span>
            </a>
        </div>
    </div>
</div>
@endif
