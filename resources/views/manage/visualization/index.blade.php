<x-app-layout>

    <x-slot name="header">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            {{ __('Visualizations') }}
        </h3>
        <p class="mt-2 max-w-7xl text-sm text-gray-500">
            {{ __('Manage visualizations here') }}
        </p>
    </x-slot>

    <div class="flex flex-col max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">

        <div class="flex justify-end gap-4">
            <a href="{{ route('manage.viz-builder-wizard.show.{currentStep}', 1) }}"><x-button>{{ __('Create New') }}</x-button></a>
        </div>

        <x-smart-table :$smartTableData custom-action-sub-view="manage.visualization.custom-action" />

    </div>
</x-app-layout>
