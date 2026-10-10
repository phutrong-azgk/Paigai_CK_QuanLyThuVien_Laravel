<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuMuon extends Model
{
    protected $table = 'chitietphieumuon';
    protected $primaryKey = 'maChiTietPhieuMuon';
    public $timestamps = false;
    protected $fillable = ['maPhieuMuon', 'maTaiLieu', 'hanTra', 'ngayTra', 'trangThai', 'tinhTrangKhiTra', 'ghiChu'];

    public function phieuMuon()
    {
        return $this->belongsTo(PhieuMuon::class, 'maPhieuMuon', 'maPhieuMuon');
    }

    public function taiLieu()
    {
        return $this->belongsTo(TaiLieu::class, 'maTaiLieu', 'maTaiLieu');
    }
}