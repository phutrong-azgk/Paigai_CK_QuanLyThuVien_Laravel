<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    protected $table = 'vaitro';
    protected $primaryKey = 'maVaiTro';
    public $timestamps = false;
    protected $fillable = ['ma', 'ten'];
}