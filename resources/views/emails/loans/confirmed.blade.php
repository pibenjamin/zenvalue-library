# Confirmation d'emprunt 📚

Bonjour {{ $user->name }} 👋,

Nous confirmons votre emprunt du livre "{{ $book->title }}" ✨

**Détails de l'emprunt :** 📋
- Date d'emprunt : {{ $loan->borrowed_at->format('d/m/Y') }}
- Date de retour prévue : {{ $loan->to_be_returned_at->format('d/m/Y') }}

Nous vous souhaitons une excellente lecture ! 🎯

Cordialement,
{{ config('app.name') }} 