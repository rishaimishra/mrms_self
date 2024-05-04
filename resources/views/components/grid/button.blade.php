<button onclick="window.location='{{ $button['url']($item) ?? '#' }}'"
        class="btn btn-primary btn-xs"
        type="button"
        >
    {{ $button['label'] }}
</button>
