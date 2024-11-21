<?php

namespace ITelmenko\Logger\Laravel\Models;

use Illuminate\Database\Eloquent\Model;
use Rorecek\Ulid\HasUlid;


class Log extends Model
{

    use HasUlid;

    protected $fillable = [
        'instance',
        'channel',
        'message',
        'level',
        'context'
    ];

    protected $casts = [
        'context' => 'array',
    ];

    protected $dateFormat = 'Y-m-d H:i:s.u';

    const UPDATED_AT = null;
}
