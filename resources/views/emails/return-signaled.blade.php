@extends('emails.layouts.base')

@section('content')
<p>Bonjour,</p>

<p>Un retour de livre a été signalé :</p>

<div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
    <h3 style="margin-top: 0;">📚 Détails du livre</h3>
    <ul>
        <li>Titre : <strong>{{ $loan->book->title }}</strong></li>
        <li>Auteur : <strong>{{ $loan->book->author }}</strong></li>
        <li>ISBN : <strong>{{ $loan->book->isbn }}</strong></li>
    </ul>

    <h3 style="margin-top: 20px;">👤 Détails de l'emprunt</h3>
    <ul>
        <li>Emprunteur : <strong>{{ $loan->borrower->name }}</strong></li>
        <li>Date d'emprunt : <strong>{{ $loan->borrowed_at->format('d/m/Y') }}</strong></li>
        <li>Date de retour prévue : <strong>{{ $loan->to_be_returned_at->format('d/m/Y') }}</strong></li>
    </ul>
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ $confirmUrl }}?token={{ $loan->return_confirmation_token }}" 
       style="background-color: #4CAF50;
              color: white;
              padding: 15px 32px;
              text-align: center;
              text-decoration: none;
              display: inline-block;
              font-size: 16px;
              margin: 4px 2px;
              cursor: pointer;
              border-radius: 4px;
              font-weight: bold;">
        ✓ CONFIRMER LE RETOUR
    </a>
</div>

<p style="color: #666; font-size: 12px; text-align: center; margin-top: 20px;">
    Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :<br>
    {{ $confirmUrl }}?token={{ $loan->return_confirmation_token }}
</p>
@endsection 