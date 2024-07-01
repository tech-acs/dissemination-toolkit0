<section class="shadow sm:rounded-md sm:overflow-hidden py-5 bg-white sm:p-6">
    @include('livewire.visualization-builder.nav')

    <div class="py-2">
        @forelse($dataShaperSelections as $key => $value)
            <x-simple-badge><span class="font-bold">{{ ucfirst($key) }}:</span>&nbsp;{{ $value }}</x-simple-badge>
        @empty
            <span class="text-gray-600">Please select your desired parameters and press the fetch button</span>
        @endforelse
    </div>

    <div x-data="{ step: @entangle('currentStep'), hasData: @entangle('hasData') }">
        @include("livewire.visualization-builder.step1")
        @include("livewire.visualization-builder.step2")
        @include("livewire.visualization-builder.step3")
    </div>

    @include('livewire.visualization-builder.footer')
    @include('livewire.partials.save-notification')
    <x-toast />
</section>
