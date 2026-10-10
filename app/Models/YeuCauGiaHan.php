<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauGiaHan extends Model
{
    protected $table = 'yeucaugiahan';
    protected $primaryKey = 'maYeuCauGiaHan';
    public $timestamps = false;
    protected $fillable = ['maChiTietPhieuMuon', 'ngayYeuCau', 'ngayGiaHanMoi', 'lyDo', 'trangThai', 'ghiChuPhanHoi'];

    public function chiTietPhieuMuon()
    {
        return $this->belongsTo(ChiTietPhieuMuon::class, 'maChiTietPhieuMuon', 'maChiTietPhieuMuon');
    }
}