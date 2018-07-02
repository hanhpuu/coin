<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $table = 'sources';
    public $timestamps = false;
    protected $fillable = ['name'];
}
