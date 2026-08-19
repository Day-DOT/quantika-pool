@props([
    'number',
    'animal',
    'name',
    'category',
    'description'
])

<div class="level-card">

    <div class="level-number">
        {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
    </div>

    <div class="level-animal">
        {{ $animal }}
    </div>

    <h3>
        {{ $name }}
    </h3>

    <p>
        {{ $description }}
    </p>

    <span class="level-category">
        {{ $category }}
    </span>

</div>