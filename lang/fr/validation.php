<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le champs :attribute doit être accepté.',
    'accepted_if' => 'Le champs :attribute doit être accepté lorsque :other est :value.',
    'active_url' => 'Le champs :attribute doit être une URL valide.',
    'after' => 'Le champs :attribute doit être une date après :date.',
    'after_or_equal' => 'Le champs :attribute doit être une date après ou égale à :date.',
    'alpha' => 'Le champs :attribute ne doit contenir que des lettres.',
    'alpha_dash' => 'Le champs :attribute ne doit contenir que des lettres, des chiffres, des tirets et des underscores.',
    'alpha_num' => 'Le champs :attribute ne doit contenir que des lettres et des chiffres.',
    'array' => 'Le champs :attribute doit être un tableau.',
    'ascii' => 'Le champs :attribute ne doit contenir que des caractères alphanumériques et des symboles.',
    'before' => 'Le champs :attribute doit être une date avant :date.',
    'before_or_equal' => 'Le champs :attribute doit être une date avant ou égale à :date.',
    'between' => [
        'array' => 'Le champs :attribute doit contenir entre :min et :max éléments.',
        'file' => 'Le champs :attribute doit être entre :min et :max kilobytes.',
        'numeric' => 'Le champs :attribute doit être entre :min et :max.',
        'string' => 'Le champs :attribute doit être entre :min et :max caractères.',
    ],
    'boolean' => 'Le champs :attribute doit être vrai ou faux.',
    'can' => 'Le champs :attribute contient une valeur non autorisée.',
    'confirmed' => 'La confirmation du champs :attribute ne correspond pas.',
    'contains' => 'Le champs :attribute est manquant d\'une valeur requise.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champs :attribute doit être une date valide.',
    'date_equals' => 'Le champs :attribute doit être une date égale à :date.',
    'date_format' => 'Le champs :attribute doit correspondre au format :format.',
    'decimal' => 'Le champs :attribute doit avoir :decimal décimales.',
    'declined' => 'Le champs :attribute doit être refusé.',
    'declined_if' => 'Le champs :attribute doit être refusé lorsque :other est :value.',
    'different' => 'Le champs :attribute et :other doivent être différents.',
    'digits' => 'Le champs :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champs :attribute doit contenir entre :min et :max chiffres.',
    'dimensions' => 'Le champs :attribute a des dimensions d\'image invalides.',
    'distinct' => 'Le champs :attribute a une valeur en double.',
    'doesnt_end_with' => 'Le champs :attribute ne doit pas se terminer par l\'un des éléments suivants: :values.',
    'doesnt_start_with' => 'Le champs :attribute ne doit pas commencer par l\'un des éléments suivants: :values.',
    'email' => 'Le champs :attribute doit être une adresse email valide.',
    'ends_with' => 'Le champs :attribute doit se terminer par l\'un des éléments suivants: :values.',
    'enum' => 'Le champs :attribute est invalide.',
    'exists' => 'Le champs :attribute est invalide.',
    'extensions' => 'Le champs :attribute doit avoir l\'une des extensions suivantes: :values.',
    'file' => 'Le champs :attribute doit être un fichier.',
    'filled' => 'Le champs :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champs :attribute doit contenir plus de :value éléments.',
        'file' => 'Le champs :attribute doit être plus grand que :value kilobytes.',
        'numeric' => 'Le champs :attribute doit être plus grand que :value.',
        'string' => 'Le champs :attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champs :attribute doit contenir :value éléments ou plus.',
        'file' => 'Le champs :attribute doit être plus grand ou égal à :value kilobytes.',
        'numeric' => 'Le champs :attribute doit être plus grand ou égal à :value.',
        'string' => 'Le champs :attribute doit contenir :value caractères ou plus.',
    ],
    'hex_color' => 'Le champs :attribute doit être une couleur hexadécimale valide.',
    'image' => 'Le champs :attribute doit être une image.',
    'in' => 'Le champs :attribute est invalide.',
    'in_array' => 'Le champs :attribute doit exister dans :other.',
    'integer' => 'Le champs :attribute doit être un entier.',
    'ip' => 'Le champs :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champs :attribute doit être une adresse IP v4 valide.',
    'ipv6' => 'Le champs :attribute doit être une adresse IP v6 valide.',
    'json' => 'Le champs :attribute doit être une chaîne JSON valide.',
    'list' => 'Le champs :attribute doit être une liste.',
    'lowercase' => 'Le champs :attribute doit être en minuscule.',
    'lt' => [
        'array' => 'Le champs :attribute doit contenir moins de :value éléments.',
        'file' => 'Le champs :attribute doit être plus petit que :value kilobytes.',
        'numeric' => 'Le champs :attribute doit être plus petit que :value.',
        'string' => 'Le champs :attribute doit être plus petit que :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champs :attribute doit contenir moins de :value éléments.',
        'file' => 'Le champs :attribute doit être plus petit ou égal à :value kilobytes.',
        'numeric' => 'Le champs :attribute doit être plus petit ou égal à :value.',
        'string' => 'Le champs :attribute doit être plus petit ou égal à :value caractères.',
    ],
    'mac_address' => 'Le champs :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champs :attribute doit contenir moins de :max éléments.',
        'file' => 'Le champs :attribute doit être plus grand que :max kilobytes.',
        'numeric' => 'Le champs :attribute doit être plus grand que :max.',
        'string' => 'Le champs :attribute doit contenir :max caractères ou moins.',
    ],
    'max_digits' => 'Le champs :attribute doit contenir moins de :max chiffres.',
    'mimes' => 'Le champs :attribute doit être un fichier de type: :values.',
    'mimetypes' => 'Le champs :attribute doit être un fichier de type: :values.',
    'min' => [
        'array' => 'Le champs :attribute doit contenir au moins :min éléments.',
        'file' => 'Le champs :attribute doit être au moins :min kilobytes.',
        'numeric' => 'Le champs :attribute doit être au moins :min.',
        'string' => 'Le champs :attribute doit être au moins :min caractères.',
    ],
    'min_digits' => 'Le champs :attribute doit contenir au moins :min chiffres.',
    'missing' => 'Le champs :attribute doit être manquant.',
    'missing_if' => 'Le champs :attribute doit être manquant lorsque :other est :value.',
    'missing_unless' => 'Le champs :attribute doit être manquant sauf lorsque :other est :value.',
    'missing_with' => 'Le champs :attribute doit être manquant lorsque :values est présent.',
    'missing_with_all' => 'Le champs :attribute doit être manquant lorsque :values sont présents.',
    'multiple_of' => 'Le champs :attribute doit être un multiple de :value.',
    'not_in' => 'Le champs :attribute est invalide.',
    'not_regex' => 'Le format du champs :attribute est invalide.',
    'numeric' => 'Le champs :attribute doit être un nombre.',
    'password' => [
        'letters' => 'Le champs :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le champs :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le champs :attribute doit contenir au moins un nombre.',
        'symbols' => 'Le champs :attribute doit contenir au moins un symbole.',
        'uncompromised' => 'Le champs :attribute a été révélé dans une fuite de données. Veuillez choisir un autre :attribute.',
    ],
    'present' => 'Le champs :attribute doit être présent.',
    'present_if' => 'Le champs :attribute doit être présent lorsque :other est :value.',
    'present_unless' => 'Le champs :attribute doit être présent sauf lorsque :other est :value.',
    'present_with' => 'Le champs :attribute doit être présent lorsque :values est présent.',
    'present_with_all' => 'Le champs :attribute doit être présent lorsque :values sont présents.',
    'prohibited' => 'Le champs :attribute est interdit.',
    'prohibited_if' => 'Le champs :attribute est interdit lorsque :other est :value.',
    'prohibited_unless' => 'Le champs :attribute est interdit sauf lorsque :other est dans :values.',
    'prohibits' => 'Le champs :attribute interdit :other de être présent.',
    'regex' => 'Le format du champs :attribute est invalide.',
    'required' => 'Le champs :attribute est requis.',
    'required_array_keys' => 'Le champs :attribute doit contenir les entrées suivantes: :values.',
    'required_if' => 'Le champs :attribute est requis lorsque :other est :value.',
    'required_if_accepted' => 'Le champs :attribute est requis lorsque :other est accepté.',
    'required_if_declined' => 'Le champs :attribute est requis lorsque :other est refusé.',
    'required_unless' => 'Le champs :attribute est requis sauf lorsque :other est dans :values.',
    'required_with' => 'Le champs :attribute est requis lorsque :values est présent.',
    'required_with_all' => 'Le champs :attribute est requis lorsque :values sont présents.',
    'required_without' => 'Le champs :attribute est requis lorsque :values n\'est pas présent.',
    'required_without_all' => 'Le champs :attribute est requis lorsque aucun des champs :values n\'est présent.',
    'same' => 'Le champs :attribute doit correspondre à :other.',
    'size' => [
        'array' => 'Le champs :attribute doit contenir :size éléments.',
        'file' => 'Le champs :attribute doit être :size kilobytes.',
        'numeric' => 'Le champs :attribute doit être :size.',
        'string' => 'Le champs :attribute doit être :size caractères.',
    ],
    'starts_with' => 'Le champs :attribute doit commencer par l\'un des éléments suivants: :values.',
    'string' => 'Le champs :attribute doit être une chaîne.',
    'timezone' => 'Le champs :attribute doit être une timezone valide.',
    'unique' => 'Le champs :attribute existe déjà.',
    'uploaded' => 'The :attribute failed to upload.',
    'uppercase' => 'The :attribute field must be uppercase.',
    'url' => 'Le champs :attribute doit être une URL valide.',
    'ulid' => 'Le champs :attribute doit être un ULID valide.',
    'uuid' => 'Le champs :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
