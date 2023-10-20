<?php

return [
    'create' => [
        'transactions.*.item_id' => 'required|integer|min:1',
        'transactions.*.item_type' => 'required|in:LEDGER,PRODUCT',
        'transaction.*.description' => 'string',
        'transaction.*.quantity' => 'required|numberic|min:1',
        'transaction.*.rate' => 'required|numeric|min:1',
        'transaction.*.discount' => 'required|numeric|min:0|max:100'
    ]
];
