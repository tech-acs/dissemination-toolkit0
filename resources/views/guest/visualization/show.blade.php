<x-guest-layout>
    <div class="container mx-auto">
        @include('partials.nav')

        <main class="py-5 border-t border-b">
            <x-guest-header :data="$visualization" :show-embed="true" :show-pdf="false"/>
            {{--@if($visualization->isFilterable)
                <livewire:geographical-area-filter :dataParams="$visualization->data_params" />
            @endif--}}

            @livewire($visualization->livewire_component, ['vizId' => $visualization->id])

        </main>

        @include('partials.footer')
    </div>
    @include('partials.footer-end')


</x-guest-layout>
