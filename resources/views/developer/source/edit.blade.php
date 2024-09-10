<x-app-layout>

    <x-slot name="header">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ __('Sources with their connections') }}
        </h3>
        <p class="mt-2 max-w-7xl text-sm text-gray-500">
            {{ __('Editing an existing source and database connection') }}
        </p>
    </x-slot>

    <div class="flex flex-col max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div x-cloak x-data="{
                selectedId: null,
                init() {
                    // Set the first available tab on the page on page load.
                    this.$nextTick(() => this.select('basics'))
                },
                select(id) {
                    this.selectedId = id
                },
                isSelected(id) {
                    return this.selectedId === id
                }
            }"
        >
        @if (session('message'))
            <div class="rounded-md bg-blue-50 p-4 py-3 my-4 mb-4 border border-blue-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/information-circle -->
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 md:flex md:justify-between">
                        <p class="text-sm text-blue-700">
                            {{session('message')}}
                        </p>
                    </div>
                </div>
            </div>
        @endif
            <div class="pt-6">
                    <form action="{{route('manage.developer.childsource.update', $source->id)}}" method="POST">
                        @csrf
                        @method('PATCH')
                        @include('developer.source.form')
                    </form>
            </div>
        </div>
    </div>
</x-app-layout>
