@props([
    'icon' => '◈',
    'label',
    'value',
    'change' => null,
    'type' => 'default'
])

<div class="stat-card">

    <div class="stat-top">

        <span class="stat-label">
            {{ $label }}
        </span>

        <div class="stat-icon">
            {{ $icon }}
        </div>

    </div>


    <div class="stat-value">
        {{ $value }}
    </div>


    @if($change)

        <div class="stat-change">
            {{ $change }}
        </div>

    @endif

</div>