<x-app-layout>

    <x-slot name="header">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ __('Dimensions') }}
        </h3>
        <p class="mt-2 max-w-7xl text-sm text-gray-500">
            {{ __('List of existing dimensions') }}
        </p>
    </x-slot>

    <div class="flex flex-col max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">

        <div class="text-right">
            <a href="{{ route('manage.dimension.create') }}"><x-button>{{ __('Create new') }}</x-button></a>
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
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Table name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Values') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Applies to') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($records as $record)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{$record->name}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{$record->table_name}}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900 items-center">
                                        {{$record->entries}}
                                        @if(! $record->is_complete)
                                            <a title="Value set is incomplete (must contain code _T)"><x-icon.exclamation-circle class="-mt-0.5" /></a>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                        {{ implode(' | ', $record->for) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('manage.dimension.edit', $record) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>
                                        <span class="text-gray-400 px-1">|</span>
                                        @if($record->table_created_at)
                                            <a href="{{ route('manage.dimension.entries.index', $record->id) }}" class="text-sky-600 hover:text-sky-900">{{ __('Values') }}</a>
                                            <span class="text-gray-400 px-1">|</span>
                                            <a href="{{ route('manage.dimension.delete-table',['id' => $record->id]) }}" x-on:click.prevent="confirmThenDelete($el)" class="text-red-600">{{ __('Delete Table') }}</a>
                                        @else
                                            <a href="{{ route('manage.dimension.create-table', ['id' => $record->id]) }}" class="text-green-600 hover:text-green-900">{{ __('Create Table') }}</a>
                                            <span class="text-gray-400 px-1">|</span>
                                            <a href="{{ route('manage.dimension.destroy', $record) }}" x-on:click.prevent="confirmThenDelete($el)" class="text-red-600">{{ __('Delete') }}</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400">
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
