<x-filament-panels::page>
    <h1>from settings</h1>
    <div class="flex">
        <x-filament::button wire:click="increment">
            +
        </x-filament::button>
        <p class="font-bold">{{ $count }}</p>
        <x-filament::button wire:click="decrement">
            -
        </x-filament::button>
    </div>
    <div>
        <x-filament::modal>
            <x-slot name="trigger">
                <x-filament::button>
                    Open modal
                </x-filament::button>
            </x-slot>
        
            modal content
        </x-filament::modal>
    </div>
    <div>
        To find out more blade components, 
        <a href="https://filamentphp.com/docs/3.x/support/blade-components/overview" target="_blank">
            Click here
        </a>
    </div>
</x-filament-panels::page>