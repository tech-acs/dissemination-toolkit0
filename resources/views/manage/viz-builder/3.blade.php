<x-app-layout>

    <div class="flex flex-col max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <x-message-display />

        <section class="shadow sm:rounded-md sm:overflow-hidden py-5 bg-white sm:p-6">
            @include('manage.viz-builder.nav')

            <div class="flex w-full">
                <div class="w-1/2 border-r pr-4 py-8">
                    <livewire:visualizer
                        :designated-component="session('step2.vizType')"
                        :raw-data="session('step1.data')"
                        :data="session('step2.data')"
                        :layout="session('step2.layout')"
                    />
                </div>

                <div class="w-1/2 pr-12">
                    <form id="step3" action="{{ route('manage.viz-builder-wizard.update.{currentStep}', 3) }}" method="post" class="w-10/12 mx-auto space-y-4 py-6">
                        @csrf
                        <div>
                            <x-label for="title" value="{{ __('Title') }} *" />
                            {{--<x-multi-lang-input wire:model="title" type="text" />--}}
                            <input type="text" name="title" value="{{ session('step1.indicatorName') }}" class="min-w-0 mt-1 flex-1 rounded-md border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 block w-full px-3" />
                            <x-input-error for="title" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="description" value="{{ __('Description') }}" class="inline" /><x-locale-display />
                            <x-textarea name="description" rows="3"></x-textarea>
                            <x-input-error for="description" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="topic" value="{{ __('Topic') }} *" />
                            <select name="topicId" class="mt-1 space-y-1 text-base p-1 pr-10 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                                <option value="">{{ __('Select topic') }}</option>
                                @foreach($topics ?? [] as $topic)
                                    <option class="p-2 rounded-md" value="{{ $topic?->id }}" @selected($topic->id == ($visualization->topic->id ?? null))>
                                        {{ $topic->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="topic_id" class="mt-2" />
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
