@extends('layouts.app')

@section('header-title', 'SETTINGS')
@section('header-subtitle', 'Manage your account preferences and security protocols.')

@section('content')

    <div class="w-full h-full">

        {{-- TABS NAVIGATION --}}
        <div class="mb-8">
            <ul class="flex flex-wrap gap-10 -mb-px text-lg font-bold" id="settings-tab"
                data-tabs-toggle="#settings-tab-content"
                role="tablist">

                {{-- Tab 1: Personal Information --}}
                <li role="presentation">
                    <button
                        class="inline-block pb-2 text-gray-400 border-b-4 border-transparent hover:text-gray-600 hover:border-gray-300 transition-all"
                        id="personal-info-tab"
                        data-tabs-target="#personal-info"
                        type="button"
                        role="tab"
                        aria-controls="personal-info"
                        aria-selected="false">
                        Personal Information
                    </button>
                </li>

                {{-- Tab 2: Security --}}
                <li role="presentation">
                    <button
                        class="inline-block pb-2 text-[#0C0B50] border-b-4 border-[#0C0B50]"
                        id="security-tab"
                        data-tabs-target="#security"
                        type="button"
                        role="tab"
                        aria-controls="security"
                        aria-selected="true">
                        Security
                    </button>
                </li>
            </ul>
        </div>

        {{-- TAB CONTENT --}}
        <div id="settings-tab-content">
            <div class="hidden" id="personal-info" role="tabpanel">
                <livewire:actions.settings-form />
            </div>

            <div class="" id="security" role="tabpanel">
                <livewire:layouts.settings.security-form />
            </div>
        </div>
    </div>

@endsection
