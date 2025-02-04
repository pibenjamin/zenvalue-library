@extends('emails.layouts.base')

@section('title')
    Retour de livre signalé 📚
@endsection

@section('content')
    <p>Bonjour 👋,</p>

    <p>L'utilisateur <strong>{{ $userName }}</strong> a signalé le retour du livre suivant :</p>

    <ul>
        <li>📖 Titre : <strong>{{ $bookTitle }}</strong></li>
        <li>📅 Date de retour signalée : {{ $returnedAt->format('d/m/Y H:i') }}</li>
    </ul>

    <p>Pour valider ce retour, veuillez cliquer sur le bouton ci-dessous :</p>

    <a href="{{ url('/admin/validate-return/' . $validationToken) }}" class="button">
        Valider le retour
    </a>

    <p>Si le bouton ne fonctionne pas, vous pouvez copier-coller ce lien dans votre navigateur :</p>
    <p class="text-break">
        {{ url('/admin/validate-return/' . $validationToken) }}
    </p>
@endsection

@section('footer-extra')
    <p>Si vous n'êtes pas l'administrateur destinataire de cet email, merci de l'ignorer.</p>
@endsection 