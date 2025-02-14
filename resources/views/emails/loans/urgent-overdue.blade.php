@extends('emails.layouts.base')

@section('title', 'URGENT : Livre très en retard')

@section('content')
    <p>Bonjour {{ $notifiable->name }},</p>

    <p class="alert">
        Un livre est très en retard (plus d'un mois) et nécessite votre attention :
    </p>

    <div class="book-info">
        <h2>{{ $loan->book->title }}</h2>

        
        <div class="details">
            <p><strong>Emprunteur :</strong> {{ $loan->borrower->name }} ({{ $loan->borrower->email }})</p>
            <p><strong>Date d'emprunt :</strong> {{ $loan->created_at->format('d/m/Y') }}</p>
            <p class="alert"><strong>Date de retour prévue :</strong> {{ $loan->to_be_returned_at->format('d/m/Y') }}</p>
            <p class="alert"><strong>Jours de retard :</strong> {{ $loan->to_be_returned_at->diffInDays(now()) }} jours</p>
        </div>
    </div>

    <p>Actions recommandées :</p>
    <ul>
        <li>Contacter l'emprunteur</li>
        <li>Vérifier si le livre n'a pas été retourné sans être enregistré</li>
        <li>Mettre à jour le statut du prêt si nécessaire</li>
    </ul>

    <a href="{{ url('/admin/loans/' . $loan->id) }}" class="button">
        Gérer ce prêt
    </a>
@endsection

@section('footer')
    @parent
    <p class="alert">
        Cette notification est envoyée uniquement aux bibliothécaires pour les prêts très en retard.
    </p>
@endsection 