<nav class="sidebar__nav">
    <button class="sidebar__close" type="button">Закрыть меню</button>
    <div class="sidebar__title">Личный кабинет</div>
    <ul class="sidebar__menu">
        @each("partial.sidebar.item", $sidebar, 'item')
    </ul>
</nav>
