@extends('emails.layouts.base')

@section('title')
Rappel de retour de livre 📚
@endsection

@section('content')
    <p>Bonjour {{ $loan->borrower->name }},</p>

    <p>Nous vous rappelons que vous devez retourner le livre suivant dans {{ $daysUntilDue }} jours :</p>

    <div class="book-info">
        <h2>{{ $loan->book->title }}</h2>
        <p>par 
            @foreach($loan->book->authors as $key => $author)
                {{ $author->name }}@if(!$loop->last){{ $loop->iteration === $loop->count - 1 ? ' et ' : ', ' }}@endif
            @endforeach
        </p>
        <p class="due-date">Date de retour : {{ $loan->to_be_returned_at->format('d/m/Y') }}</p>
    </div>

    <p>Pour rappel, ce livre a été emprunté le {{ $loan->borrowed_at->format('d/m/Y') }}.</p>
@endsection

@section('footer-extra')
<p>En cas de question, n'hésitez pas à nous contacter.</p>
<p>
    {{ config('app.name') }}<br>
    {{ config('app.url') }}
</p>
@endsection