@extends('emails.layouts.base')

@section('content')
<p>Bonjour {{ $owner->name }},</p>
<p>{{ $user->name }} souhaite emprunter votre livre <i>{{ $book->title }}</i>.</p>

<h3>📚 Détails du livre</h3>
<ul>
    <li>Titre : <strong>{{ $book->title }}</strong></li>
    <li>Auteur : <strong>{{ $book->author }}</strong></li>
    @if($book->isbn)
        <li>ISBN : <strong>{{ $book->isbn }}</strong></li>
    @endif
</ul>

<p>Ce livre est déclaré disponible pour un prêt. Vous pouvez bien entendu accepter ou refuser cette demande. 
    En lui répondant par courriel. <a href="mailto:{{$owner->email}}">{{$owner->email}}</a></p>
<p>Deux options s'offrent à vous :</p>
<ul>
    <li>Ajouter un QR Code au livre et rappeler au demandeur d'enregistrer son prêt afin vous garantir un suivi automatisé via la plateforme. (historique, rappels par courriel, etc)</li>
    <li>Ou gérer le prêt par vos propres moyens.</li>
</ul>

<p>Cordialement,</p>
<p>L'équipe de la bibliothèque</p>

@endsection 