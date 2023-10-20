<?php

return [
    'create' => [
        'stock_item_id' => 'required|exists:App/Stock,id',
        'user_id' => 'required|exists:users,id',
        'quantity' => 'required|numeric',
        'narration' => 'string'
    ],
    'update' => [
        'id' => 'required|numeric|exists:App/Stock,id',
        'stock_item_id' => 'required|exists:App/Stock,id',
        'user_id' => 'required|exists:users,id',
        'quantity' => 'required|numeric',
        'narration' => 'string'
    ]
];
