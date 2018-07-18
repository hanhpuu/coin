<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Validator;

class Source extends Model
{
    protected $table = 'sources';
    public $timestamps = false;
    protected $fillable = ['name'];
	
	public static function checkSourceValidation($request)
	{
		$validator  = Validator::make($request->all(), [
            'name'  => 'required|unique:sources|max:25',
        ]);
		return $validator;
	}
}
