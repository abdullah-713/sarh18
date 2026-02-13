<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Hero: Branch Progress Widget (Weekly Stats) --}}
        <div class="animate-fadeInUp">
            <livewire:branch-progress-widget />
        </div>

        {{-- Row 2: Attendance --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="animate-fadeInUp" style="animation-delay: 0.1s">
                <livewire:attendance-widget />
            </div>
            <div class="animate-fadeInUp" style="animation-delay: 0.15s">
                <livewire:attendance-stats-widget />
            </div>
        </div>

        {{-- Row 3: Gamification + Circulars --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div class="animate-fadeInUp" style="animation-delay: 0.2s">
                <livewire:gamification-widget />
            </div>
            <div class="animate-fadeInUp" style="animation-delay: 0.25s">
                <livewire:circulars-widget />
            </div>
        </div>
    </div>
</x-filament-panels::page>
