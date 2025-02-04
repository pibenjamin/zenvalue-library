@extends('emails.layouts.base')

@section('title')
Confirmation d'emprunt 📚
@endsection


@section('content')
    <p>Confirmation d'emprunt,</p>

    <p>Bonjour {{ $user->name }} 👋,</p>
    <p>Nous confirmons votre emprunt du livre "{{ $book->title }}" ✨</p>

    <ul>
    <li>**Détails de l'emprunt :** 📋</li>
    <li>- 📅 Date d'emprunt : {{ $loan->borrowed_at->format('d/m/Y') }}</strong></li>
    <li>- 📅 Date de retour prévue : {{ $loan->to_be_returned_at->format('d/m/Y') }}</li>
    </ul>

    <p>Nous vous souhaitons une excellente lecture ! 🎯</p>
@endsection

@section('footer-extra')
    <p>Si vous n'êtes pas l'administrateur destinataire de cet email, merci de l'ignorer.</p>
@endsection 