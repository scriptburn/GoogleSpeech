<?php
namespace App\ScriptBurn\Facades;
use Illuminate\Support\Facades\Facade;

class ScriptBurnHelper extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'App\ScriptBurn\Facades\ScriptBurnHelper';
	}

}
