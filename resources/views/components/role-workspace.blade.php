@props([
    'eyebrow',
    'title',
    'description',
    'actions' => [],
])

<section class="workspace-hero">
    <div class="workspace-hero-grid"></div>
    <div class="workspace-hero-content">
        <div class="workspace-hero-copy">
            <p class="workspace-eyebrow"><span></span>{{ $eyebrow }}</p>
            <div class="mt-3 max-w-2xl">
                <h3>{{ $title }}</h3>
                <p>{{ $description }}</p>
            </div>
            @if ($actions !== [])
                <div class="workspace-actions">
                    @foreach ($actions as $action)
                        <a href="{{ $action['url'] }}" class="{{ $action['primary'] ?? false ? 'workspace-action-primary' : 'workspace-action-secondary' }}">
                            {{ $action['label'] }} <span aria-hidden="true">&rarr;</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <div class="workspace-signal" aria-hidden="true"><span class="signal-label">{{ __('LIVE INDEX') }}</span><strong>02<span>/</span>24</strong><span class="signal-line"></span><small>{{ __('Research streams synced') }}</small></div>
    </div>
</section>
