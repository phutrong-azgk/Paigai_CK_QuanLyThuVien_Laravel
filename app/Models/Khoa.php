<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Khoa extends Model
{
    protected $table = 'khoa';
    protected $primaryKey = 'maKhoa';
    public $timestamps = false;
    protected $fillable = ['ma', 'ten'];
}