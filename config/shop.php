<?php

return [
    'bank_account' => env('SHOP_BANK_ACCOUNT', 'PL00 0000 0000 0000 0000 0000 0000'),
    'bank_recipient' => env('SHOP_BANK_RECIPIENT', 'Gouda & Spółka Sp. z o.o.'),
    'cod_fee' => (int) env('SHOP_COD_FEE', 500),

    // Przykładowe paczkomaty (lista na sztywno, docelowo z geowidgetu InPost)
    'paczkomaty' => [
        'KRA01APP' => 'KRA01APP - Kraków, ul. Serowa 1',
        'WAW42M'   => 'WAW42M - Warszawa, ul. Mleczna 42',
        'GDA07N'   => 'GDA07N - Gdańsk, ul. Portowa 7',
        'POZ15A'   => 'POZ15A - Poznań, Rynek 15',
        'WRO23B'   => 'WRO23B - Wrocław, Rynek 23',
    ],
];
