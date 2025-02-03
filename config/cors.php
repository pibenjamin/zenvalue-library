<?php

return [

    'paths' => ['api/*', 'storage/*'], // Ajoute storage/* si tu héberges des images
    
    'allowed_methods' => ['*'],
    
    'allowed_origins' => ['*'], // Autorise toutes les origines (à restreindre en production)
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => false,
    
    ];    
?>