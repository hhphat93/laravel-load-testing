<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $connection   = 'employees';
    protected $table        = 'employees';
    protected $primaryKey   = 'emp_no';
    public    $incrementing = false;
    public    $timestamps   = false;
}
