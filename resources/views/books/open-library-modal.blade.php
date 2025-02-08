<div class="space-y-4">
    @if(isset($bookData['error']))
        <div class="text-red-600">
            Aucune donnée trouvée pour cet ISBN
        </div>
    @else
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h3 class="font-bold">Titre</h3>
                <p>{{ $bookData['title'] ?? 'N/A' }}</p>
            </div>
            <div>
                <h3 class="font-bold">Auteur</h3>
                <p>{{ $bookData['authors'][0]['name'] ?? 'N/A' }}</p>
            </div>
            <!-- Ajoutez d'autres champs selon vos besoins -->
        </div>
    @endif
</div> 