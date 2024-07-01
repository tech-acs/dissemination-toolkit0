<x-app-layout>

    <div class="flex flex-col max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <x-message-display />
        <livewire:state-recorder />

        <section class="shadow sm:rounded-md sm:overflow-hidden py-5 bg-white sm:p-6">
            @include('manage.viz-builder.nav')

            <livewire:data-shaper-selections-display />

            <div class="grid grid-cols-3">
                <div class="border p-4">

                    <livewire:data-shaper />

                </div>
                <div class="border p-4 pt-8 col-span-2">

                    <livewire:visualizations.table :raw-data="session('step1.data', [])" />

                </div>
            </div>

            @include('manage.viz-builder.footer')
            <x-toast />
        </section>

    </div>
</x-app-layout>
