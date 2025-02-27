<div class="fi-modal-content space-y-4">
    <div class="fi-section">
        <div class="fi-section-content">
            <div class="overflow-hidden">
                <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Champ
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Base de données
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Open Library
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        État
                                    </th>
                                    <th scope="col" class="fi-ta-header-cell px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ([
                                    'title' => 'Titre',
                                    'author' => 'Auteur',
                                    'publish_date' => 'Date de publication',
                                    'publisher' => 'Éditeur',
                                    'pages' => 'Nombre de pages',
                                    'lang' => 'Langue',
                                ] as $field => $label)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30">
                                        <td class="fi-ta-cell whitespace-nowrap px-3 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $label }}
                                        </td>
                                        <td class="fi-ta-cell whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $record->$field }}
                                        </td>
                                        <td class="fi-ta-cell whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $ol_data[$field] ?? 'Non disponible' }}
                                        </td>
                                        <td class="fi-ta-cell px-3 py-4 text-sm">
                                            @if((($ol_data[$field] ?? '') != $record->$field || empty($record->$field)) && isset($ol_data[$field]))
                                                <div class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                                                    <x-heroicon-m-exclamation-triangle class="h-5 w-5 text-warning-500" />
                                                    <span class="text-warning-500">
                                                        @if(empty($record->$field))
                                                            Donnée manquante
                                                        @else
                                                            Différent
                                                        @endif
                                                    </span>
                                                </div>
                                            @else
                                                <div class="inline-flex items-center space-x-1 rtl:space-x-reverse">
                                                    <x-heroicon-m-check-circle class="h-5 w-5 text-success-500" />
                                                    <span class="text-success-500">Identique</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="fi-ta-cell px-3 py-4 text-sm">
                                            @if((($ol_data[$field] ?? '') != $record->$field || empty($record->$field)) && isset($ol_data[$field]))
                                                <form wire:submit="updateField('{{ $field }}', '{{ $ol_data[$field] }}')">
                                                    <button
                                                        type="submit"
                                                        class="fi-btn fi-btn-size-sm relative inline-flex items-center justify-center rounded-lg bg-primary-600 px-3 py-1 text-sm font-semibold text-white outline-none transition duration-75 hover:bg-primary-500 focus:ring-2 focus:ring-primary-500/50 dark:bg-primary-500 dark:hover:bg-primary-400 dark:focus:ring-primary-400/50"
                                                    >
                                                        <x-heroicon-m-arrow-path class="h-4 w-4 mr-1" />
                                                        Mettre à jour {{$field . ' ' . $ol_data[$field]}}
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="fi-section mt-6">
        <div class="fi-section-content">
            <div class="prose dark:prose-invert max-w-none">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center gap-x-3">
                        <x-heroicon-m-information-circle class="h-5 w-5 text-gray-500 dark:text-gray-400" />
                        <h3 class="text-base font-medium text-gray-900 dark:text-gray-100">
                            Légende
                        </h3>
                    </div>
                    <div class="mt-2.5 text-sm text-gray-500 dark:text-gray-400">
                        <ul class="list-inside space-y-1">
                            <li class="flex items-center gap-x-2">
                                <x-heroicon-m-check-circle class="h-5 w-5 text-success-500" />
                                <span>Les données correspondent entre les deux sources</span>
                            </li>
                            <li class="flex items-center gap-x-2">
                                <x-heroicon-m-exclamation-triangle class="h-5 w-5 text-warning-500" />
                                <span>Les données diffèrent entre les deux sources</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>