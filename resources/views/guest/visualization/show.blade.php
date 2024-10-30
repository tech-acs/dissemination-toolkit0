<x-guest-layout>
    <div class="container mx-auto">
        @include('partials.nav')

        <article class="p-4 rounded-md ring-1 mb-8">
            <x-guest-header :content="$visualization" :show-embed="true" />
            {{--@if($visualization->isFilterable)
                <livewire:geographical-area-filter :dataParams="$visualization->data_params" />
            @endif--}}

            <livewire:visualizer :designated-component="$visualization->livewire_component" :viz-id="$visualization->id" />

            <x-reviews :subject="$visualization" />

        </article>

        @include('partials.footer')
    </div>
    @include('partials.footer-end')

</x-guest-layout>
