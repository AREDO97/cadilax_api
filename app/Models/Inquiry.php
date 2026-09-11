<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    //
    /*
category, subject, message
* */
    protected $fillable = [
        'user_id',
        'category',
        'subject',
        'message',
        'is_replied'
    ];
    // user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // inquiry replies
    public function inquiryReplies()
    {
        return $this->hasMany(InquiryReply::class);
    }
}
