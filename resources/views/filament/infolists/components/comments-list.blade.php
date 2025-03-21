@if($getState() != null)
<div class="space-y-2">
    <div class="text-sm font-medium text-gray-950 dark:text-gray-400">
        {{ $getState()->count() }} commentaire{{ $getState()->count() > 1 ? 's' : '' }}
    </div>
    
    <div class="max-h-60 overflow-y-auto border rounded-lg">
        @forelse($getState() as $comment)
            <div class="p-3 border-b last:border-b-0">
                <div class="flex items-start gap-x-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-900 dark:text-white">
                            {{ $comment->comment }}
                        </p>
                        <div class="mt-1 text-xs text-gray-500">
                            {{ $comment->user->name }} - {{ $comment->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-3 text-sm text-gray-500 text-center">
                Aucun commentaire
            </div>
        @endforelse
    </div>
</div> 
@endif