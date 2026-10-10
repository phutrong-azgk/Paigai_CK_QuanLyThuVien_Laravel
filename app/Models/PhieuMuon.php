<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuMuon extends Model
{
    protected $table = 'phieumuon';
    protected $primaryKey = 'maPhieuMuon';
    public $timestamps = false;
    protected $fillable = ['maDocGia', 'maThuThu', 'maChinhSach', 'maYeuCauMuon', 'ngayMuon', 'trangThai', 'ghiChu'];

    public function docGia()
    {
        return $this->belongsTo(HoSoDocGia::class, 'maDocGia', 'maDocGia');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietPhieuMuon::class, 'maPhieuMuon', 'maPhieuMuon');
    }

    public function chinhSach()
    {
        return $this->belongsTo(ChinhSachMuon::class, 'maChinhSach', 'maChinhSach');
    }
}