<div class="modal" id="js-show-history-type-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container">
            <button type="button" class="modal__close js-close" data-micromodal-close></button>
            <div class="modal__header">
                <h4 class="reset-m">@lang("app.{$entity}.history")</h4>
            </div>
            <div class="modal__body">
                <table class="panel__table">
                    @if ($history->isNotEmpty())
                    <thead>
                        <tr>
                            <th>@lang("app.{$entity}.created_at")</th>
                            <th>@lang("app.{$entity}.value")</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($history as $key => $item)
                        <tr>
                            <td>{{ $item->created_at->format('d.m.Y H:i:s') }}</td>
                            <td>{{ $item->value }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    @else
                    <tbody>
                        <tr>
                            <td colspan="3" style="text-align: center; vertical-align: middle;">@lang('app.common.elements_are_missing')</td>
                        </tr>
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
