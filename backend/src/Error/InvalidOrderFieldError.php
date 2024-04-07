<?php

namespace App\Error;

use Error;

class InvalidOrderFieldError extends Error
{
    protected $message = 'Trying to order by unknown property';
}