<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Foundation\Application;

use Illuminate\Contracts\Encryption\Encrypter;
class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];


    public function __construct(Application $app, Encrypter $encrypter)
    {
    	$this->except=[];//[route('url_keyword.assign_label'),route('url_keyword.delete_label')];
 
        parent::__construct($app,$encrypter);
    }
}
