@extends('emails.layouts.base')

@section('content')
<p>Bonjour {{ $user->name }},</p>
<p>Nous vous confirmons l'emprunt du livre suivant :</p>

<h3>📚 Détails du livre</h3>
<ul>
    <li>Titre : <strong>{{ $book->title }}</strong></li>
    <li>Auteur : <strong>{{ $book->author }}</strong></li>
    @if($book->isbn)
        <li>ISBN : <strong>{{ $book->isbn }}</strong></li>
    @endif
</ul>

<h3>📅 Détails du prêt</h3>
<ul>
    <li>Date d'emprunt : <strong>{{ $borrowDate->format('d/m/Y') }}</strong></li>
    <li>Date de retour prévue : <strong>{{ $returnDate->format('d/m/Y') }}</strong></li>
</ul>

@if($activeLoans->count() > 0)
    <h3>📚 Vos prêts en cours</h3>
    <ul>
        @foreach($activeLoans as $loan)
            <li>{{ $loan->book->title }} (à rendre le {{ Carbon\Carbon::parse($loan->to_be_returned_at)->format('d/m/Y') }})</li>
        @endforeach
    </ul>
@endif

{{-- @if(isset($calendarEvent))
    <p><a href="{{ $calendarEvent }}" class="button">Ajouter au calendrier</a></p>
@endif --}}
@endsection 