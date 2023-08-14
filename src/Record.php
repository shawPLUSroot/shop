<?php

namespace Shawplusroot\Shop;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class Record extends AbstractModel
{
    protected $table = 'record';

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
