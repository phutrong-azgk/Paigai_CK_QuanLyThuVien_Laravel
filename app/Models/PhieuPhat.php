<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhieuPhat extends Model
{
    protected $table = 'phieuphat';
    protected $primaryKey = 'maPhieuPhat';
    public $timestamps = false;
    protected $fillable = [
        'maChiTietPhieuMuon', 'maDocGia', 'loaiPhat', 'soTien', 'ngayLap', 'trangThai', 'maNguoiThu', 'ngayThanhToan'
    ];

    public function docGia()
    {
        return $this->belongsTo(HoSoDocGia::class, 'maDocGia', 'maDocGia');
    }
}