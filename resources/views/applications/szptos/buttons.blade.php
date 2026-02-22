@if(Route::has("applications.{$entity}.reCalculation"))
<div class="panel__actions-item">
    <form action="{{ route("applications.{$entity}.reCalculation") }}" method="post">
        @csrf
        <button type="submit" class="btn btn--medium btn--blue">
            <i class="fas fa-recycle btn__icon"></i>
            <span class="btn__text btn__text--right">Пересчитать общие баллы по заявкам</span>
        </button>
    </form>
</div>
@endif
