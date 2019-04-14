<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GitLog extends Model
{
    protected $table = 'git_log';
    protected $guarded = ['id'];
}
