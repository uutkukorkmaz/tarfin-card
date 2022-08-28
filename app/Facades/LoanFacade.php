<?php

namespace App\Facades;

use App\Services\LoanService;

class LoanFacade extends \Illuminate\Support\Facades\Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'loan';
    }

}
