<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FPPBHeader extends Model
{
    //
    protected $connection = 'sqlsrv';
    protected $table = 'tr_fppb_header';
}
