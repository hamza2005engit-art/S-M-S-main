<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    use HasFactory  ;

    protected $fillable = [
       'user_id',
       'salary_id',
    ];
}
