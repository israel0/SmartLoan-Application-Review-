<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpenTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id' , 'department' , 'branch_id','user_id', 'message', 'subject' , 'sender_email' , 'priority'
    ];
    
}
