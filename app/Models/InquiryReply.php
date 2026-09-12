<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryReply extends Model
{
    // allowed
  protected $fillable = [
        'inquiry_id',
        'user_id',
        'message',
  ];
  // inquiry
  public function inquiry()
  {
    $this->belongsTo(Inquiry::class);
  }
  // user
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
