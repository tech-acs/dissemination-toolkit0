@push('styles')
    @vite(['resources/css/content-styles.css'])
@endpush
<x-guest-layout>
    <div class="container mx-auto flex-grow">
        @include('partials.nav')
           <style>
                    {!! $story->css !!}
            </style>
        <article class="py-5 border-t border-b">
            <x-guest-header :data="$story" :show-pdf="true" :show-embed="false"/>
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
