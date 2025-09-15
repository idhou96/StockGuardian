<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    protected $availableStock;
    protected $requestedQuantity;

    public function __construct(
        string $message, 
        int $availableStock = 0, 
        int $requestedQuantity = 0
    ) {
        parent::__construct($message);
        $this->availableStock = $availableStock;
        $this->requestedQuantity = $requestedQuantity;
    }

    public function getAvailableStock(): int
    {
        return $this->availableStock;
    }

    public function getRequestedQuantity(): int
    {
        return $this->requestedQuantity;
    }
}