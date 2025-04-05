@extends('emails.layouts.base')

@section('title')
📚 Votre livre a été ajouté au catalogue 📚
@endsection

@section('content')
    <p>Bonjour {{ $user->name }},</p>

    <p>Nous vous informons que votre livre "{{ $book->title }}" a été ajouté au catalogue !</p>

    @if (!is_null($qrCodeUrl))
        <p>Comme vous en aviez émis le souhait, voici le QR code de votre livre (également en pièce jointe) :</p>
        <img width="200" src="{{ $message->embed($qrCodeUrl) }}" alt="QR Code">
        <a href="{{ $qrCodeUrl }}" download>Télécharger le QR code</a>
    @endif

    <p>Vous pouvez désormais le retrouver sur la page <a href="{{ config('app.url') }}/admin/books?activeTab=Mes+livres">Mes livres</a>.</p>

    <p>Nous vous remercions pour votre contribution qui fait vivre l'esprit de partage et d'amélioration continue !</p>

@endsection

@section('footer-extra')
<p>En cas de question, n'hésitez pas à nous contacter.</p>
<p>
    {{ config('app.name') }}<br>
    {{ config('app.url') }}
</p>
@endsection