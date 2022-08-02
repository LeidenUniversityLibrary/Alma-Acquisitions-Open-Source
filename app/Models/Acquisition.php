<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acquisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'MMS Id',
        'Title',
        'Author',
        'subjects',
        'Publisher',
        'Publication Date',
        'Resource Type',
        'Creation Date',
        'Subjects',
        'Start Range',
    ];
}
