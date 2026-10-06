<x-filament-widgets::widget>
    <div class="space-y-4">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">
                    System services
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Stato dei servizi e infrastruttura
                </p>
            </div>

            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                <span class="relative flex h-2.5 w-2.5">
                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-success-400 opacity-75"></span>
                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-success-500"></span>
                </span>

                Live monitoring
            </div>
        </div>

        {{-- Services --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            @foreach ($this->getServices() as $service)

                <div
                    class="
                        group relative overflow-hidden rounded-2xl
                        border border-gray-200 bg-white
                        p-5 shadow-sm
                        transition duration-200
                        hover:-translate-y-0.5 hover:shadow-md
                        dark:border-white/10 dark:bg-white/[0.03]
                    "
                >

                    {{-- Status indicator --}}
                    <div
                        @class([
                            'absolute right-4 top-4 h-2.5 w-2.5 rounded-full',
                            'bg-success-500 shadow-[0_0_0_4px_rgba(34,197,94,0.12)]' => $service['online'],
                            'bg-danger-500 shadow-[0_0_0_4px_rgba(239,68,68,0.12)]' => ! $service['online'],
                        ])
                    ></div>

                    {{-- Icon --}}
                    <div class="mb-5">
                        <div
                            @class([
                                'flex h-11 w-11 items-center justify-center rounded-xl',
                                'bg-success-50 text-success-600 dark:bg-success-500/10 dark:text-success-400' => $service['online'],
                                'bg-danger-50 text-danger-600 dark:bg-danger-500/10 dark:text-danger-400' => ! $service['online'],
                            ])
                        >
                            <x-dynamic-component
                                :component="$service['icon']"
                                class="h-5 w-5"
                            />
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <h3 class="font-semibold text-gray-950 dark:text-white">
                            {{ $service['name'] }}
                        </h3>

                        <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">
                            {{ $service['description'] }}
                        </p>
                    </div>

                    {{-- Status --}}
                    <div class="mt-5 flex items-end justify-between">

                        <div>
                            <div
                                @class([
                                    'text-sm font-medium',
                                    'text-success-600 dark:text-success-400' => $service['online'],
                                    'text-danger-600 dark:text-danger-400' => ! $service['online'],
                                ])
                            >
                                {{ $service['online'] ? 'Online' : 'Offline' }}
                            </div>

                            <div class="mt-1 text-xs text-gray-400">
                                {{ $service['host'] }}:{{ $service['port'] }}
                            </div>
                        </div>

                        @if ($service['online'])
                            <div class="text-right">
                                <div class="text-lg font-semibold tracking-tight text-gray-950 dark:text-white">
                                    {{ $service['latency'] }}<span class="ml-0.5 text-xs font-normal text-gray-400">ms</span>
                                </div>

                                <div class="text-[11px] text-gray-400">
                                    response
                                </div>
                            </div>
                        @else
                            <div class="text-xs font-medium text-danger-500">
                                Unreachable
                            </div>
                        @endif

                    </div>

                    {{-- Bottom status bar --}}
                    <div class="mt-5 h-1 overflow-hidden rounded-full bg-gray-100 dark:bg-white/5">
                        <div
                            @class([
                                'h-full rounded-full transition-all duration-500',
                                'w-full bg-success-500' => $service['online'],
                                'w-1/4 bg-danger-500' => ! $service['online'],
                            ])
                        ></div>
                    </div>

                </div>

            @endforeach

        </div>
    </div>
</x-filament-widgets::widget>