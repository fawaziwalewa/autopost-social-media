<div>
    <x-filament-panels::page x-data="{ activeTab: 'twitter' }">
        <x-filament::tabs>
            {{-- Twitter (x.com) --}}
            <x-filament::tabs.item alpine-active="activeTab === 'twitter'" x-on:click="activeTab = 'twitter'">
                Twitter (x.com)
            </x-filament::tabs.item>
            {{-- Facebook --}}
            <x-filament::tabs.item alpine-active="activeTab === 'facebook'" x-on:click="activeTab = 'facebook'">
                Facebook
            </x-filament::tabs.item>
        </x-filament::tabs>

        {{-- Twitter (x.com) --}}
        <div x-show="activeTab === 'twitter'">
            <x-filament-panels::form wire:submit="saveTwitter">
                {{-- Twitter Form --}}
                {{ $this->twitterForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getTwitterActions()" />
            </x-filament-panels::form>
        </div>

        {{-- Facebook --}}
        <div x-show="activeTab === 'facebook'">
            <x-filament-panels::form wire:submit="saveFacebook">
                {{-- Facebook Form --}}
                {{ $this->facebookForm }}
                {{-- Submit BTN --}}
                <x-filament-panels::form.actions :actions="$this->getFacebookActions()" />
            </x-filament-panels::form>
        </div>

        <x-filament-actions::modals />
    </x-filament-panels::page>
</div>
