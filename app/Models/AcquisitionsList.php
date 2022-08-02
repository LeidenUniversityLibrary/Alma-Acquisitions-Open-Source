<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcquisitionsList extends Model
{
    use HasFactory;

    protected $fillable = [
        'acquisitions_alma_source', // from which json file are we getting the acquisitions?
        'acquisitions_list_name', // how should this acquisition list be called?
        'url_path', // what URL path should this acquisition list have?
    ];
}
