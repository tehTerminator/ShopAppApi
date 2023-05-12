<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidationService
{
    // Define specific validation rules based on the table and operation type

    public static function validateModel(Request $request, $table, $operationType = 'create')
    {
        // Get the validation rules for the specified table and operation type
        $validationRules = include('./../Models/Validation/' . $table . 'ValidationRules.php');

        // Get the specific validation rules based on the operation type
        $rules = $validationRules[$operationType];

        // Run validation
        $validator = Validator::make($request->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Return the validation error messages
            return $validator->errors();
        }

        // Validation passed
        return true;
    }

    public function __construct()
    {
    }
}
