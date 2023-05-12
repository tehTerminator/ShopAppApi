<?php

return [
    'create' => [
        'title' => 'required|unique:customers|string',
        'address' => 'required|string',
        'mobile' => 'regex:/^[6-9][0-9]{9}$/',
        'ledger_id' => 'numeric|exists:App\Models\Ledger,id'
    ],
    'update' => [
        'id' => 'required|integer|exists:App/Customer,id',
        'title' => 'required|string',
        'address' => 'required|string',
        'mobile' => 'regex:/^[6-9][0-9]{9}$/'
    ],
    
]