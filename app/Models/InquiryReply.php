<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InquiryReply extends Model
{
       use HasFactory;

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
