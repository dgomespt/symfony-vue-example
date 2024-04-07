<?php

namespace App\Error;

use Error;

class InvalidFilterFieldError extends Error
{
    protected $message = 'Trying to filter by unknown property';
}