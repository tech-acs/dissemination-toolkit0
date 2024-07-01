<footer class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 pt-5 sm:px-8">
    @if ($currentStep === 1)
        <form action="{{ route('manage.viz-builder-wizard.update.{currentStep}', $currentStep) }}" method="post">
            @csrf
            <input type="hidden" name="reset" value="1">
            <button type="submit" class="text-sm font-semibold text-gray-900 rounded-md bg-gray-100 hover:bg-gray-200 px-3 py-2">
                Reset
            </button>
        </form>
    @endif

    @if (in_array($currentStep, [2, 3]))
        <a href="{{ route('manage.viz-builder-wizard.show.{currentStep}', $currentStep - 1) }}" class="text-sm font-semibold leading-6 text-gray-900">
            Back
        </a>
    @endif

    <form action="{{ route('manage.viz-builder-wizard.update.{currentStep}', $currentStep) }}" method="post">
        @csrf
        <input type="hidden" name="viz" value="{{ request('viz', session('step2.vizType', \App\Livewire\Visualizations\Table::class)) }}">
        <input type="hidden" name="chart-data" id="chart-data">
        <input type="hidden" name="chart-layout" id="chart-layout">
        @if ($currentStep === 1)
            <button type="submit" class="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Next
            </button>
        @endif
        @if ($currentStep === 2)
            <button class="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Next
            </button>
        @endif
        @if($currentStep === 3)
            <button form="step3" class="cursor-pointer rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                Save
            </button>
        @endif
    </form>

</footer>
