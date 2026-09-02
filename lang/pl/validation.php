<?php

return [
    'required' => 'Pole :attribute jest wymagane.',
    'email' => 'Pole :attribute musi być prawidłowym adresem e-mail.',
    'string' => 'Pole :attribute musi być tekstem.',
    'integer' => 'Pole :attribute musi być liczbą całkowitą.',
    'numeric' => 'Pole :attribute musi być liczbą.',
    'boolean' => 'Pole :attribute musi mieć wartość prawda albo fałsz.',
    'confirmed' => 'Potwierdzenie pola :attribute nie zgadza się.',
    'unique' => 'Taki :attribute jest już zajęty.',
    'exists' => 'Wybrany :attribute jest nieprawidłowy.',
    'image' => 'Pole :attribute musi być obrazem.',
    'mimes' => 'Pole :attribute musi być plikiem typu: :values.',
    'regex' => 'Format pola :attribute jest nieprawidłowy.',
    'current_password' => 'Hasło jest nieprawidłowe.',
    'lowercase' => 'Pole :attribute musi być zapisane małymi literami.',

    'min' => [
        'numeric' => 'Pole :attribute musi wynosić co najmniej :min.',
        'string' => 'Pole :attribute musi mieć co najmniej :min znaków.',
    ],
    'max' => [
        'numeric' => 'Pole :attribute nie może być większe niż :max.',
        'string' => 'Pole :attribute nie może mieć więcej niż :max znaków.',
        'file' => 'Pole :attribute nie może być większe niż :max kilobajtów.',
    ],

    'custom' => [
        'postal_code' => [
            'regex' => 'Kod pocztowy musi mieć format 00-000.',
        ],
    ],
    'attributes' => [
        'email' => 'adres e-mail',
        'name' => 'imię',
        'surname' => 'nazwisko',
        'phone' => 'telefon',
        'street' => 'ulica',
        'house_number' => 'nr domu',
        'apartment_number' => 'nr mieszkania',
        'postal_code' => 'kod pocztowy',
        'city' => 'miasto',
        'password' => 'hasło',
        'price' => 'cena',
        'stock_quantity' => 'stan magazynowy',
        'quantity' => 'ilość',
        'slug' => 'slug',
        'category_id' => 'kategoria',
        'min_portion' => 'minimalna porcja',
        'weight_per_unit' => 'waga na jednostkę',
        'unit' => 'jednostka',
        'description' => 'opis',
        'image' => 'zdjęcie',
    ],
];
