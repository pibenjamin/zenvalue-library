@extends('emails.layouts.base')

@section('title', '⏰ Rappel : Retour de prêt en retard')

@section('content')
    <p>Bonjour {{ $loan->borrower->name }},</p>

    <p class="alert">

        @if($daysOverdue === 0)
            Nous vous rappelons que le livre suivant devait être retourné aujourd'hui :
        @else
            Nous vous rappelons que le livre suivant devait être retourné il y a {{ $daysOverdue }} jours :
        @endif
    </p>

    <div class="book-info">
        <h2>{{ $loan->book->title }}</h2>
        <p>Par 
            @foreach($loan->book->authors as $key => $author)
                {{ $author->name }}@if(!$loop->last){{ $loop->iteration === $loop->count - 1 ? ' et ' : ', ' }}@endif
            @endforeach
        </p>
        <p class="alert">Date de retour prévue : {{ $loan->to_be_returned_at->format('d/m/Y') }}</p>
    </div>

    <p>Pour rappel, ce livre a été emprunté le {{ $loan->borrowed_at->format('d/m/Y') }}.</p>

    <p>Merci de le rapporter dès que possible pour permettre à d'autres lecteurs d'en profiter.</p>
@endsection 