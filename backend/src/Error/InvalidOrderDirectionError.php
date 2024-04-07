<?php

namespace App\Error;

use Error;

class InvalidOrderDirectionError extends Error
{
    protected $message = 'Invalid order direction (asc|desc)';
}