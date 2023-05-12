<?php

return [
    'create' => [
        'title' => 'required|unique:App/Product,title|string|max:255',
        'rate' => 'required|numeric|min:0',
    ],
    'update' => [
        'id' => 'required|exists:App/Product,id',
        'title' => 'required|string|max:255',
        'rate' => 'required|numeric|min:0',
    ],
    'delete' => [
        'id' => 'required|exists:App/Product,id',
    ]
];