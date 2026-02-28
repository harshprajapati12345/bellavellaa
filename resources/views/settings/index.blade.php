@extends('layouts.app')

@section('title', 'App Theme Settings · Bellavella Admin')

@section('content')
    <div class="max-w-5xl mx-auto">
        <div class="flex flex-col md:flex-row gap-8 items-start mt-4">

            <!-- Left: Form -->
            <div class="flex-1 bg-white rounded-3xl p-8 shadow-[0_2px_16px_rgba(0,0,0,0.04)] border border-gray-50">
                <div class="mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">App Theme Configuration</h2>
                    <p class="text-sm text-gray-400 mt-1">Customize the look and feel of your client-side application.</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 text-emerald-600 text-sm font-medium flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 gap-6">

                        @php
                            $primary = $settings->where('key', 'primary_color')->first()->value ?? '#000000';
                            $secondary = $settings->where('key', 'secondary_color')->first()->value ?? '#6B7280';
                            $background = $settings->where('key', 'background_color')->first()->value ?? '#FFFFFF';
                        @endphp

                        <!-- Primary Color -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                                Primary Color
                            </label>
                            <div class="color-input-wrapper">
                                <input type="color" name="settings[primary_color]" id="primary_color" value="{{ $primary }}"
                                    oninput="updatePreview()">
                                <div class="color-info" id="primary_hex">{{ $primary }}</div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Used for buttons, icons, and main UI
                                highlights.</p>
                        </div>

                        <!-- Secondary Color -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-blue-400"></div>
                                Secondary Color
                            </label>
                            <div class="color-input-wrapper">
                                <input type="color" name="settings[secondary_color]" id="secondary_color"
                                    value="{{ $secondary }}" oninput="updatePreview()">
                                <div class="color-info" id="secondary_hex">{{ $secondary }}</div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1.5 ml-1">Used for accents, secondary buttons, and text
                                highlights.</p>
                        </div>

                        <!-- Background Color -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full bg-gray-200"></div>
                                App Background
                            </label>
                            <div class="color-input-wrapper">
                                <input type="color" name="settings[background_color]" id="background_color"
                                    value="{{ $background }}" oninput="updatePreview()">
                                <div class="color-info" id="background_hex">{{ $background }}</div>
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1.5 ml-1">The primary background color for pages and
                                containers.</p>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100 flex items-center gap-3">
                        <button type="submit"
                            class="flex-1 bg-black text-white px-6 py-3.5 rounded-xl font-semibold hover:bg-gray-800 transition-all shadow-lg shadow-black/5 flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Save Configuration
                        </button>
                        <button type="button" onclick="window.location.reload()"
                            class="px-6 py-3.5 rounded-xl border border-gray-200 text-gray-600 font-medium hover:bg-gray-50 transition-all">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Right: Preview -->
            <div class="w-full md:w-auto flex flex-col items-center flex-shrink-0 md:sticky md:top-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6">APK Live Preview</p>
                <div class="phone-mockup">
                    <div class="phone-screen" id="preview-screen">
                        <div class="phone-header flex items-center justify-between px-4 py-3">
                            <i data-lucide="menu" class="w-5 h-5 text-gray-800" id="preview-icon-menu"></i>
                            <span class="text-sm font-bold text-gray-800" id="preview-app-name">Bellavella</span>
                            <i data-lucide="shopping-bag" class="w-5 h-5 text-gray-800" id="preview-icon-cart"></i>
                        </div>
                        <div class="phone-content space-y-4 p-4">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900" id="preview-title">Exclusive Offers</h3>
                                <span class="text-xs font-semibold" id="preview-see-all">See All</span>
                            </div>

                            <div class="phone-card bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i data-lucide="sparkles" class="w-6 h-6" id="preview-icon-svc"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-3 w-2/3 bg-gray-100 rounded-full mb-2"></div>
                                        <div class="h-2 w-1/3 bg-gray-50 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="phone-card bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                                <div class="flex gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center">
                                        <i data-lucide="heart" class="w-6 h-6" id="preview-icon-heart"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="h-3 w-1/2 bg-gray-100 rounded-full mb-2"></div>
                                        <div class="h-2 w-1/4 bg-gray-50 rounded-full"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="phone-btn text-white shadow-md p-4 rounded-2xl text-center font-bold"
                                id="preview-main-btn">
                                Book Appointment
                            </div>

                            <div class="grid grid-cols-4 gap-2 mt-8">
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-100"
                                        id="preview-nav-1">
                                        <i data-lucide="home" class="w-4 h-4 text-white" id="preview-nav-icon-1"></i>
                                    </div>
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center bg-gray-50"
                                        id="preview-nav-2">
                                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Notch -->
                    <div
                        class="absolute top-0 left-1/2 -translate-x-1/2 w-24 h-6 bg-black rounded-b-xl flex items-end justify-center pb-1">
                        <div class="w-8 h-1 bg-white/20 rounded-full"></div>
                    </div>
                </div>
                <p class="mt-4 text-[10px] text-gray-400 font-medium">Visual representation only</p>
            </div>

        </div>
    </div>

    @push('styles')
        <style>
            .color-input-wrapper {
                position: relative;
                width: 100%;
                height: 48px;
                border-radius: 12px;
                border: 1px solid #e5e7eb;
                overflow: hidden;
                background: #fff;
                transition: all 0.2s;
            }

            .color-input-wrapper:hover {
                border-color: #000;
            }

            .color-input-wrapper input[type="color"] {
                position: absolute;
                top: -10px;
                left: -10px;
                width: 150%;
                height: 150%;
                cursor: pointer;
                padding: 0;
                border: none;
            }

            .color-info {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                pointer-events: none;
                font-size: 13px;
                font-weight: 500;
                color: #374151;
                background: rgba(255, 255, 255, 0.9);
                padding: 2px 8px;
                border-radius: 6px;
                border: 1px solid #f3f4f6;
            }

            .phone-mockup {
                width: 280px;
                height: 560px;
                background: #000;
                border-radius: 36px;
                padding: 10px;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
                position: relative;
            }

            .phone-screen {
                width: 100%;
                height: 100%;
                background: #fff;
                border-radius: 28px;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                position: relative;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function updatePreview() {
                const primary = document.getElementById('primary_color').value;
                const secondary = document.getElementById('secondary_color').value;
                const background = document.getElementById('background_color').value;

                document.getElementById('primary_hex').textContent = primary;
                document.getElementById('secondary_hex').textContent = secondary;
                document.getElementById('background_hex').textContent = background;

                document.getElementById('preview-screen').style.backgroundColor = background;
                document.getElementById('preview-main-btn').style.backgroundColor = primary;
                document.getElementById('preview-nav-1').style.backgroundColor = primary;
                document.getElementById('preview-icon-menu').style.color = primary;
                document.getElementById('preview-icon-cart').style.color = primary;
                document.getElementById('preview-icon-svc').style.color = primary;
                document.getElementById('preview-see-all').style.color = secondary;
                document.getElementById('preview-icon-heart').style.color = secondary;
            }

            document.addEventListener('DOMContentLoaded', updatePreview);
        </script>
    @endpush
@endsection