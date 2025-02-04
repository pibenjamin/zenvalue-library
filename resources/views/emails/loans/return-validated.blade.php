@extends('emails.layouts.base')

@section('title')
    Retour de livre validé 📚
@endsection

@section('content')
    <p>Bonjour {{ $userName }} 👋,</p>

    <p>Nous confirmons que votre retour du livre suivant a bien été validé :</p>

    <ul>
        <li>📖 Titre : <strong>{{ $bookTitle }}</strong></li>
        <li>📅 Date de retour : {{ $returnedAt->format('d/m/Y H:i') }}</li>
    </ul>

    <p>Merci d'avoir utilisé notre service de bibliothèque ! 🙏</p>
    
    <p>N'hésitez pas à emprunter d'autres livres de notre collection. 📚</p>
@endsection 