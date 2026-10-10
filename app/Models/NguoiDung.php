<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class NguoiDung extends Authenticatable
{
    protected $table = 'nguoidung';
    protected $primaryKey = 'maNguoiDung';
    public $timestamps = false;

    protected $fillable = [
        'maVaiTro', 'tenDangNhap', 'matKhau', 'hoTen', 'thuDienTu', 'soDienThoai', 'ngaySinh', 'trangThai', 'ngayTao'
    ];

    protected $hidden = [
        'matKhau',
    ];

    public function getAuthPassword()
    {
        return $this->matKhau;
    }

    public function vaiTro()
    {
        return $this->belongsTo(VaiTro::class, 'maVaiTro', 'maVaiTro');
    }

    public function hoSoDocGia()
    {
        return $this->hasOne(HoSoDocGia::class, 'maNguoiDung', 'maNguoiDung');
    }
}