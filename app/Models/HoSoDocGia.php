<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HoSoDocGia extends Model
{
    protected $table = 'hosodocgia';
    protected $primaryKey = 'maDocGia';
    public $timestamps = false;
    protected $fillable = ['maNguoiDung', 'maKhoa', 'maSo', 'loaiDocGia', 'trangThaiThe'];

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'maNguoiDung', 'maNguoiDung');
    }

    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'maKhoa', 'maKhoa');
    }
}