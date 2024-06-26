@props([
    'tags' => []
])
<div class="space-x-0.5">
    @foreach($tags as $tag)
        <a href="?tag={{ $tag->id }}" class="inline-flex items-center bg-blue-100 text-blue-800 px-1.5 py-0.5 text-xs font-medium rounded border border-blue-400">
            {{ $tag->name }}
        </a>
    @endforeach
</div>
