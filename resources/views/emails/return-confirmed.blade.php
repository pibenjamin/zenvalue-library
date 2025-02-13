@extends('emails.layouts.base')

@section('content')
<p>Bonjour {{ $loan->borrower->name }},</p>

<p>Nous vous confirmons le retour du livre suivant :</p>

<div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
    <h3 style="margin-top: 0;">📚 {{ $loan->book->title }}</h3>
    <p style="margin-bottom: 0;">
        <strong>Date d'emprunt :</strong> {{ $loan->borrowed_at->format('d/m/Y') }}<br>
        <strong>Date de retour :</strong> {{ $loan->returned_at->format('d/m/Y') }}
    </p>
</div>

<p>Merci d'avoir utilisé notre service de bibliothèque !</p>
@endsection 