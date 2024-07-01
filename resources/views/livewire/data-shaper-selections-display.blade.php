<div class="py-2">
    @forelse($dataShaperSelections ?? [] as $key => $value)
        <x-simple-badge wire:transition.opacity><span class="font-bold">{{ ucfirst($key) }}:</span>&nbsp;{{ $value }}</x-simple-badge>
    @empty
        <span class="text-gray-600">Please select your desired parameters and press the fetch button</span>
    @endforelse
</div>
