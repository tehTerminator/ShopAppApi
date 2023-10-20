<?php

return [
    'create' => [
        'product_id' => 'required|exists:products,id',
        'stock_item_id' => 'required|exists:stock_items,id',
        'quantity' => 'required|numeric',
    ],
    'update' => [
        'id' => 'required|integer|exists:App/StockTemplate,id',
        'product_id' => 'required|exists:products,id',
        'stock_item_id' => 'required|exists:stock_items,id',
        'quantity' => 'required|numeric',
    ]
];