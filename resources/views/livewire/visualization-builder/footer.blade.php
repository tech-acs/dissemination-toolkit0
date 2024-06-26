<footer class="flex items-center justify-end gap-x-6 border-t border-gray-900/10 px-4 pt-5 sm:px-8">
    <button wire:click="previousStep" type="button" class="text-sm font-semibold leading-6 text-gray-900">Back</button>
    @if ($currentStep < count($this->steps))
        <button wire:click="nextStep" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Next
        </button>
    @else
        <button wire:click.prevent="saveViz" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
            Save
        </button>
    @endif
</footer>
