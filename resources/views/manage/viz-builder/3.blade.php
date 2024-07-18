<x-app-layout>

    <div class="flex flex-col max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <x-message-display />

        <section class="shadow sm:rounded-md sm:overflow-hidden py-5 bg-white sm:p-6">
            @include('manage.viz-builder.nav')

            <div class="flex w-full">
                <div class="w-1/2 border-r pr-4 py-8">
                    <livewire:visualizer
                        :designated-component="session('step2.vizType', 'App\Livewire\Visualizations\Table')"
                        :raw-data="session('step1.data', [])"
                        :data="session('step2.data', [])"
                        :layout="session('step2.layout', [])"
                    />
                </div>

                <div class="w-1/2 pr-12">
                    <div class="w-10/12 mx-auto -mb-4"><x-error-display /></div>
                    <form id="step3" action="{{ route('manage.viz-builder-wizard.update.{currentStep}', 3) }}" method="post" class="w-10/12 mx-auto space-y-4 py-6">
                        @csrf
                        <div>
                            <x-label for="title" value="{{ __('Title') }} *" />
                            <input type="text" name="title" value="{{ session('step1.indicatorName') }}" class="min-w-0 mt-1 flex-1 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full px-3" />
                            <x-input-error for="title" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="description" value="{{ __('Description') }}" class="inline" /><x-locale-display />
                            <x-textarea name="description" rows="3">{{ session('step3.description') }}</x-textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="topic" value="{{ __('Topic') }} *" />
                            <select name="topicId" class="mt-1 space-y-1 text-base p-1 pr-10 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <option value="">{{ __('Select topic') }}</option>
                                @foreach($topics ?? [] as $topic)
                                    <option class="p-2 rounded-md" value="{{ $topic?->id }}" @selected($topic->id == session('step3.topicId'))>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="topic_id" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="topic" value="{{ __('Filterable') }}" />
                            <div class="flex items-center mt-3 ml-3" x-data="{enabled: @json(session('step3.filterable')) }" x-cloak>
                                <label for="featured">
                                    <span class="text-sm text-gray-500">{{ __('No') }}</span>
                                </label>
                                <input type="hidden" name="featured" :value="enabled">
                                <button
                                    x-on:click="enabled = ! enabled"
                                    :class="enabled ? 'bg-indigo-600' : 'bg-gray-200'"
                                    type="button"
                                    class="ml-3  relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    role="switch"
                                    id="featured"
                                >
                                    <span aria-hidden="true" :class="enabled ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
                                </button>
                                <label for="featured" class="ml-3">
                                    <span class="text-sm text-gray-900">{{ __('Yes') }}</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <x-label for="tags" value="{{ __('Tags') }}" />
                            <x-tags value="[]" class="mt-1" />
                            <x-input-error for="tags" class="mt-2" />
                        </div>
                    </form>
                </div>
            </div>

            @include('manage.viz-builder.footer')
            <x-toast />
        </section>
    </div>
</x-app-layout>
