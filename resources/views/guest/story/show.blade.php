@push('styles')
    @vite(['resources/css/content-styles.css', 'resources/css/grid.css'])
@endpush

<x-guest-layout>
    <div class="container mx-auto flex-grow">
        @include('partials.nav')

        <article class="p-4 rounded-md ring-1 mb-8">
            <x-guest-header :content="$story" />
            @if($story->is_filterable)
                <livewire:area-filter />
            @else
                <livewire:i-need-alpine />
            @endif
            <div class="pt-10 ck-content">

                {!! Blade::render($story->html) !!}

            </div>

            <x-reviews />
        </article>
    </div>
    <div class="container mx-auto">
        @include('partials.footer')
    </div>
    @include('partials.footer-end')

</x-guest-layout>
