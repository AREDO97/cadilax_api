<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    // allowed
    protected $fillable = [
        'user_id',
        'action',
        'body'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // self inject logs
    public static function Log($userId,$action,$body)
    {
        self::create([
            'user_id'=>$userId,
            'action'=>$action,
            'body'=>$body,
            'status'=>'active'
        ]);
    }
}
