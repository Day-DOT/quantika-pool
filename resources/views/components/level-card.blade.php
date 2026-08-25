@props([
    'number',
    'name',
    'description',
    'category' => null,
    'tag' => null,
    'image' => null,
    'animal' => null,
    'alt' => null,
    'href' => '#',
    'linkText' => 'Ver nivel →',
])

<div class="level-card">
    @if ($image)
        <div class="animal-area">
            <img
                src="{{ asset($image) }}"
                alt="{{ $alt ?? $name }}"
                class="animal-image"
            >
        </div>
    @elseif ($animal)
        <div class="level-animal">
            {{ $animal }}
        </div>
    @endif

    <div class="level-content">
        <span class="level-number">
            NIVEL {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
        </span>

        <h2 class="level-name">
            {{ $name }}
        </h2>

        <p class="level-description">
            {{ $description }}
        </p>

        <div class="level-footer">
            <span class="level-tag">
                {{ $tag ?? $category }}
            </span>

            <a href="{{ $href }}" class="view-btn">
                {{ $linkText }}
            </a>
        </div>
    </div>
</div>