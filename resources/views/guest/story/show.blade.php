@push('styles')
    @vite(['resources/css/content-styles.css', 'resources/css/grid.css'])
@endpush

<x-guest-layout>
    <div class="container mx-auto flex-grow">
        @include('partials.nav')

        <article class="py-5 border-t border-b">
            <x-guest-header :data="$story" :show-pdf="true" :show-embed="false" />
            @if($story->is_filterable)
                <livewire:area-filter />
            @endif
            <div class="pt-10 ck-content">

                {!! Blade::render($story->html) !!}

            </div>
        </article>
    </div>
    <div class="container mx-auto">
        @include('partials.footer')
    </div>
    @include('partials.footer-end')

</x-guest-layout>
