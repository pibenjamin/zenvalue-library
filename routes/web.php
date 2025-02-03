<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;

Route::get('/test-email', function () {
    Mail::raw('Test de l\'envoi d\'un e-mail dans les logs.', function ($message) {
        $message->to('test@example.com')
                ->subject('Test Mail Log tadda');
    });

    return 'E-mail envoyé et logué';

    exit;
});



Route::get('/', function () {
    return view('welcome');
});
