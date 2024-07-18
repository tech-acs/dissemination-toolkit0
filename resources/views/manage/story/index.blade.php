<x-app-layout>

    <x-slot name="header">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ __('Stories') }}
        </h3>
        <p class="mt-2 max-w-7xl text-sm text-gray-500">
            {{ __('Manage stories here') }}
        </p>
    </x-slot>

    <div class="flex flex-col max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <div class="text-right">
            <a href="{{route('manage.story.create')}}"><x-button>{{ __('Create new') }}</x-button></a>
        </div>
        <x-message-display />
        <x-error-display />

        <div class="mt-2 flex flex-col">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg" x-data="confirmedDeletion">

                        <x-delete-confirmation />

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Title') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Topic') }}
                                </th>
                                <th scope="col" class="py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Published') }}
                                </th>
                                <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Featured') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Author') }}
                                </th>
                                <th scope="col" class="py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                    {{ __('Last updated') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($records as $record)
                            <tr>
                                <td class="w-1/3 px-6 py-4 text-sm font-medium text-gray-900 break-words">
                                    {{$record->title}} @if($record->is_filterable) <x-icon.filter /> @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{$record->topic?->name}}
                                </td>
                                <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <x-yes-no value="{{$record->published}}" />
                                </td>
                                <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <x-yes-no value="{{$record->featured}}" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ $record->user->name }}
                                </td>
                                <td class="py-4 whitespace-nowrap text-sm text-gray-500 text-right">
                                    {{$record->updated_at->diffForHumans()}}
                                </td>
                                <td class="px-3 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('story.show', $record) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('View') }}</a>
                                    <span class="text-gray-400 px-1">|</span>
                                    <a href="{{route('manage.story.duplicate', $record->id)}}" class="text-amber-600 hover:text-amber-900">{{ __('Duplicate') }}</a>
                                    @if($record->is_owner)
                                        <span class="text-gray-400 px-1">|</span>
                                        <a href="{{ route('manage.story.edit', $record->id) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <span class="text-gray-400 px-1">|</span>
                                        <a href="{{route('manage.story-builder.edit', $record->id)}}" class="text-green-600 hover:text-green-900">{{ __('Build') }}</a>
                                        <span class="text-gray-400 px-1">|</span>
                                        <a href="{{ route('manage.story.destroy', $record->id) }}" x-on:click.prevent="confirmThenDelete($el)" class="text-red-600">{{ __('Delete') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400">
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
