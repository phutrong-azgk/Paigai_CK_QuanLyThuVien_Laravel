<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhatKyHeThong extends Model
{
    protected $table = 'nhatkyhethong';
    protected $primaryKey = 'maNhatKy';
    public $timestamps = false;
    protected $fillable = ['maNguoiDung', 'hanhDong', 'doiTuong', 'maDoiTuong', 'ngayTao'];
}