@if(count($breadcrumbs))
    @foreach($breadcrumbs as $key => $breadcrumb)
        @if(count($breadcrumbs) !== $key+1)
            <li class="breadcrumb-item">
                <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a>
            </li>
        @else
            @if(count($breadcrumbs) > 1)
                <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
            @else
                <li class="breadcrumb-item">
                    <a href="javascript:void(0)">{{ $breadcrumb['name'] }}</a>
                </li>
            @endif
        @endif
    @endforeach
@endif
