<?php

//namespace App\Models;
namespace LicenseClient\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    //
    protected $table = 'client_licenses';
    protected $fillable = ['license_code', 'valid_until'];
}
