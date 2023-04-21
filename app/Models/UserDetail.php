<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserDetail extends Model
{
    use HasFactory, SoftDeletes;

    /*
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
    */

    protected $guarded = [''];

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'created_by',
        'approved_by',
        'is_approved'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'datetime:Y-m-d h:i:s',
    ];
    protected $dateFormat = 'Y-m-d h:i:s';

    /**
     * Relationship with the type of request made for User Detail
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function request_type()
    {
        return $this->belongsTo(RequestType::class, 'request_type_id');
    }

}
