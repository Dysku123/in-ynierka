<?php

return [
    'bank_account' => env('SHOP_BANK_ACCOUNT', 'PL00 0000 0000 0000 0000 0000 0000'),
    'bank_recipient' => env('SHOP_BANK_RECIPIENT', 'Gouda & Spółka Sp. z o.o.'),
    'cod_fee' => (int) env('SHOP_COD_FEE', 500)
];
