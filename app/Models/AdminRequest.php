<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRequest extends Model
{
    use HasFactory;

    /*
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */

    protected $guarded = [''];
    protected $hidden = [
        'created_at', 'updated_at', 'requester_id','approver_id'
    ];
    protected $casts = [
        'payload' => 'array'
    ];

    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = json_encode($value);
    }
}