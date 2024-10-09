@props([
    'content',
    'showEmbed' => false,
    'showPdf' => false,
])
<header class="p-2">
    <div class="flex justify-between items-center">
        <div class="text-xs">
            <span class="text-stone-500 font-semibold uppercase tracking-wider mr-3"> Topics </span>
            <span class="text-gray-600 tracking-wide">{{ $content?->topics?->pluck('name')->join(' | ') }}</span>
        </div>
        <div class="flex items-center">
            @if($showEmbed)
                <x-embed>
                    <x-slot name="trigger">
                        <button type="button" class="mr-4 rounded-full bg-white px-3 py-1.5 text-sm font-semibold text-gray-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                            <svg class="size-5 text-gray-600 inline" viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M7 8l-4 4l4 4" /><path d="M17 8l4 4l-4 4" /><path d="M14 4l-4 16" /></svg>
                            Embed
                        </button>
                    </x-slot>
                    <x-slot name="embedCode">{{ $content->embedCode() }}</x-slot>
                </x-embed>

            @endif

            <x-share-links url="{{ url()->full() }}" content="{{ $content->title }}" />
        </div>
    </div>

    <div class="py-5 w-3/4 text-4xl text-indigo-800 font-bold tracking-tight">{{ $content->title }}</div>
    <div class="text-base text-gray-500 pb-2">{{ $content->description }}</div>
    <div class="pb-2"><x-tag-cloud :tags="$content->tags"/></div>
    <div class="border-y w-full flex justify-between items-center py-2 my-2">
        <div>
            <span class="mr-1 text-gray-700 font-medium">Published</span> <span class="text-gray-500">{{ $content->updated_at->format('l jS \\of F Y h:i A e') }} </span>
        </div>
        <div>
            <div class="flex items-center mb-1 space-x-1 rtl:space-x-reverse">
                <h3 class="mt-0.5 mr-2 text-base font-semibold text-gray-800">4/5</h3>
                <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                </svg>
                <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                </svg>
                <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                </svg>
                <svg class="w-4 h-4 text-yellow-300" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                </svg>
                <svg class="w-4 h-4 text-gray-300 dark:text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 20">
                    <path d="M20.924 7.625a1.523 1.523 0 0 0-1.238-1.044l-5.051-.734-2.259-4.577a1.534 1.534 0 0 0-2.752 0L7.365 5.847l-5.051.734A1.535 1.535 0 0 0 1.463 9.2l3.656 3.563-.863 5.031a1.532 1.532 0 0 0 2.226 1.616L11 17.033l4.518 2.375a1.534 1.534 0 0 0 2.226-1.617l-.863-5.03L20.537 9.2a1.523 1.523 0 0 0 .387-1.575Z"/>
                </svg>
                <h3 class="mt-0.5 pl-1 text-base text-gray-600">(6 reviews)</h3>
            </div>
            {{--<x-average-rating :average-rating="$content->averageRating"/>--}}
        </div>
    </div>

    {{--<div class="flex justify-between">
        <div class="w-4/6">
            <div class="flex justify-end gap-x-1">
                <div class="w-full md:w-1/4">
                    <livewire:rating :id="$content->id" type="{{class_basename($content)}}"/>
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
                        <x-slot name="embedCode">{{ $content->embedCode() }}</x-slot>
                    </x-embed>
                @endif

                <x-share-buttons url="{{ url()->full() }}" content="{{ $content->title }}"/>
            </div>

        </div>
    </div>--}}

</header>
