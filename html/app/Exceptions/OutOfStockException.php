<?php

namespace App\Exceptions;

use Exception;

class OutOfStockException extends Exception
{
    public $data;

    public function __construct($message = 'Out of stock', $data = [], $code = 400)
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'error' => 'OUT_OF_STOCK',
            'message' => $this->getMessage(),
            'data' => $this->data,
        ], $this->getCode() ?: 400);
    }
}
