<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestType extends Model
{
    /*
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */

    protected $guarded = [''];

    protected $fillable = [
        'id',
        'name',
    ];
}
