<?php

return [
    'create' => [
        'title' => 'required|unique:App/Stock,title|',
        'quantity' => 'required|integer|min:0'
    ],
    'update' => [
        'title' => 'string',
        'quantity' => 'integer'
    ]
];