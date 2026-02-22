<div id="filter" class="panel__filter panel__content hidden">
    <form action="{{ route("{$entity}.filter") }}" method="post" enctype="multipart/form-data" class="form" id="filter-form">
        @csrf
        <input type="hidden" name="page" value="{{ request()->input('page') }}">
        <input type="hidden" name="sort_column" value="{{ $columnDefault }}">
        <input type="hidden" name="sort_direction" value="{{ $directionDefault }}">
        <input type="hidden" name="method" value="{{ $redirectRouteName }}">
    </form>
</div>
