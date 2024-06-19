<x-app-layout>

    <x-slot name="header">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ __('Census tables') }}
        </h3>
        <p class="mt-2 max-w-7xl text-sm text-gray-500">
            {{ __('List of existing census tables') }}
        </p>
    </x-slot>

    <div class="flex flex-col max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">

        <div class="text-right">
            <a href="{{ route('manage.census-table.create') }}">
                <x-button>{{ __('Create new') }}</x-button>
            </a>
        </div>
        @if (session('message'))
            <div class="rounded-md p-4 py-3 mt-4 mb-4 border bg-blue-50 border-blue-300">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/information-circle -->
                        <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                             fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 md:flex md:justify-between">
                        <p class="text-sm text-blue-700">
                            {{ session('message') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
        @if ($errors->any())
            <div class="rounded-md p-4 py-3 mt-4 mb-4 border bg-red-100 border-red-400">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <!-- Heroicon name: solid/information-circle -->
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-3 flex-1 md:flex md:justify-between text-sm text-red-700">
                        <ul class="">
                            @foreach($errors->all() as $error)
                                <li class="">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-2 flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
                         x-data="confirmedDeletion">

                        <x-scaffold::delete-confirmation/>

                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-500 sm:pl-6">{{ __('Title') }}</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-500"> {{ __('Type') }}</th>
                                <th scope="col"
                                    class="px-3 py-3.5 text-left text-sm font-semibold text-gray-500"> {{ __('Publisher') }}</th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('Author') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    {{ __('Published') }}
                                </th>
                                <th scope="col"
                                    class="py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    {{ __('Last updated') }}
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span
                                        class="sr-only">Edit</span></th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($records as $record)
                                <tr>
                                    <td class="w-1/3 py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        <a href="{{ route('census-table.show', $record->id) }}">
                                            {{ $record->title }}
                                        </a>
                                    </td>
                                    <td class="px-3 py-4 whitespace-normal text-sm text-gray-500">
                                        <x-dataset-type-badge :text="$record->dataset_type"
                                                              class="{{\App\Enums\CensusTableTypeEnum::getTypeClass($record->dataset_type)}}"/>
                                    </td>
                                    <td class="px-3 py-4 whitespace-normal text-sm text-gray-500">{{ $record->publisher }}</td>
                                    <td class="px-6 py-4 whitespace-normal text-sm text-gray-500 text-center">
                                        {{ $record->user->name }}
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <x-scaffold::yes-no value="{{$record->published}}"/>
                                    </td>
                                    <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                        {{$record->updated_at->diffForHumans()}}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <div class="flex justify-center items-center">
                                            <a href="{{route('census-table.show', $record->id)}}"
                                               class="text-gray-600 hover:text-gray-900 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </a>
                                            <span class="text-gray-400 px-0.5">|</span>
                                            <a href="{{route('manage.census-table.edit', $record->id)}}"
                                               class="text-indigo-600 hover:text-indigo-900 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/>
                                                </svg>
                                            </a>
                                            <span class="text-gray-400 px-0.5">|</span>
                                            <a href="{{ route('manage.census-table.destroy', $record->id) }}"
                                               x-on:click.prevent="confirmThenDelete($el)"
                                               class="text-red-600 hover:scale-110">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                     stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7"
                                        class="text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400">
                                        {{ __('There are no records to display') }}
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
</x-app-layout>
