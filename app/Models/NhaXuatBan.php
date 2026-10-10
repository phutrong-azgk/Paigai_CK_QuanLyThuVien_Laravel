<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhaXuatBan extends Model
{
    protected $table = 'nhaxuatban';
    protected $primaryKey = 'maNhaXuatBan';
    public $timestamps = false;
    protected $fillable = ['ma', 'ten', 'diaChi', 'soDienThoai'];
}