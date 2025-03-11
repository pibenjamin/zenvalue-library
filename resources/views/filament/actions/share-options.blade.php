<div class="space-y-4 p-4">



<div
    class="teams-share-button"
    data-href="{{ url('/admin/books/' . $record->id) }}"
    data-msg-text="Je te recommande ce livre : {{ $record->title }}"
    data-preview="false">
 </div>




    <a href="https://teams.microsoft.com/share?href={{ url("/admin/books/{$record->id}") }}&text=Je te recommande ce livre : {{ $record->title }}"
       target="_blank"
       class="flex items-center gap-2 p-3 text-primary-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
        <x-heroicon-o-share class="w-5 h-5" />
        Partager sur Teams
    </a>
    <a href="mailto:?subject=Recommandation de livre&body=Je te recommande ce livre : {{ $record->title }}%0D%0A{{ url("/admin/books/{$record->id}") }}"
       class="flex items-center gap-2 p-3 text-primary-600 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
        <x-heroicon-o-envelope class="w-5 h-5" />
        Partager par email
    </a>
</div> 


