@if ($item['isPermitted'])
    <li class="{{ $item['isSubmenu'] ? 'has-sub' : '' }}{{ $item['isActive'] ? ' active' : '' }}{{ $item['isOpen'] ? ' open' : '' }}">
        <a href="{{ url("{$item['url']}") }}">
            @if (!empty($item['icon']))
                <i class="fas fa-{{ $item['icon'] }} sidebar__menu-icon"></i>
            @endif
            {{ $item['name'] }}
            @if ($item['isSubmenu'])
                <i class="fas fa-chevron-right"></i>
            @endif
        </a>
        @if ($item['isSubmenu'])
            <ul class="sidebar__sub-menu{{ $item['isActive'] ? ' active' : '' }}">
                @each("partial.sidebar.item", $item['submenu'], 'item')
            </ul>
        @endif
    </li>
@endif

