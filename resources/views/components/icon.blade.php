@props(['name', 'class' => 'w-5 h-5'])

<x-app-icon :name="$name" :class="$class" {{ $attributes }} />
