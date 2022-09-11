<?php

namespace App\Services\SendinBlue;

use Illuminate\Support\Facades\Facade;

class SendinBlue extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sendinBlue';
    }
}
