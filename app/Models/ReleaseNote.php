<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReleaseNote extends Model
{
    use HasFactory;

    protected $table = "release_notes";

    public function poster()
    {
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }
}