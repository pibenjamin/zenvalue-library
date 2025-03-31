@extends('emails.layouts.base')

@section('content')
<p>Bonjour,</p>

<p>Un livre a été déposé au bureau :</p>

<div style="margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
    <h3 style="margin-top: 0;">📚 Détails du livre</h3>
    <ul>
        <li>Titre : <strong>{{ $bookTitle }}</strong></li>
        <li>Date de dépot : <strong>{{ $dropOffAt->format('d/m/Y H:i') }}</strong></li>
        <li>Propriétaire : <strong>{{ $ownerName }}</strong></li>
        
    </ul>


</div>
@endsection 