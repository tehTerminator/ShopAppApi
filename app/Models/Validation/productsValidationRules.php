<?php

return [
    'create' => [
        'title' => 'required|unique:App/Product,title|string|max:255',
        'rate' => 'required|numeric|min:0',
    ],
    'update' => [
        'title' => 'required|string|max:255',
        'rate' => 'required|numeric|min:0',
    ],
];