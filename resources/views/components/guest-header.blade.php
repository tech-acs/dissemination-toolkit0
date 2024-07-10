@props([
 'data',
 'showEmbed' => false,
 'showPdf' => true,
])
<header class="p-2 border-b">
    <div class="flex justify-between items-start">
        <div class="pr-4">
            <div
                class="mt-2 text-4xl text-indigo-800 font-bold tracking-tight line-clamp-1">{{ $data->title }}</div>
            <div class="mt-2 text-xs text-gray-500 inline-flex gap-x-2 items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"/>
                </svg>

                <span>{{ $data->updated_at->format('F j, Y \a\t h:i A') }} </span>

                <div class="w-96">
                    <x-average-rating :average-rating="$data->averageRating"/>
                </div>

            </div>
            <p class="mt-4 mb-2 text-sm text-gray-700 line-clamp-2">{{ strip_tags($data->description) }}</p>
        </div>
    </div>
    <div class="flex justify-between">
        <div class="w-2/6 self-end">
            <x-tag-cloud :tags="$data->tags"/>
        </div>
        <div class="w-4/6">
            <div class="flex justify-end gap-x-1">
                <div class="w-full md:w-1/4">
                    <livewire:rating :id="$data->id" type="{{class_basename($data)}}"/>
                </div>
                @if($showPdf)


                <button
                        class="group border border-red-800 px-2 h-7 uppercase hover:bg-red-800 rounded-md shadow-sm bg-white cursor-pointer inline-flex items-center gap-x-2 text-xs font-semibold text-blue-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 text-red-800 group-hover:text-white"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2"></path>
                        <path d="M7 11l5 5l5 -5"></path>
                        <path d="M12 4l0 12"></path>
                    </svg>
                    <span class="text-red-800 group-hover:text-white">PDF</span>
                </button>
                @endif
                @if($showEmbed)
                    <x-embed>
                        <x-slot name="trigger">
                            <x-viz-button icon="embed">Embed</x-viz-button>
                        </x-slot>
                        <x-slot name="embedCode">{{ $data->embedCode() }}</x-slot>
                    </x-embed>
                @endif

                <x-share-buttons url="{{ url()->full() }}" content="{{ $data->title }}"/>
            </div>

        </div>
    </div>


</header>
