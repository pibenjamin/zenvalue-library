<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-4">
            <div class="flex items-center gap-x-2">
                <x-filament::icon
                    icon="heroicon-o-users"
                    class="w-5 h-5 text-gray-500 dark:text-primary-500"
                />
                <h2 class="text-lg font-bold tracking-tight">
                    Les employés
                </h2>
            </div>
            <div class="flex items-center gap-x-2">
                <div class="flex -space-x-3 rtl:space-x-reverse">
                    @foreach ($users as $user)
                        <div x-data x-tooltip="'{{ $user->name }}'">
                            <x-filament::avatar
                                :src="$user->avatar == null ? asset('storage/avatars/default-avatar.png') : asset('storage/' . $user->avatar)"
                                :alt="$user->name"
                                class="ring-2 ring-white dark:ring-gray-900"
                                size="lg"
                            />
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>