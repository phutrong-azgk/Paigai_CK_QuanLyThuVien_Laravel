<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YeuCauMuon extends Model
{
    protected $table = 'yeucaumuon';
    protected $primaryKey = 'maYeuCauMuon';
    public $timestamps = false;
    protected $fillable = ['maDocGia', 'ngayYeuCau', 'ngayHenLay', 'trangThai', 'lyDoTuChoi', 'ngayDuyet', 'maNguoiDuyet'];

    public function docGia()
    {
        return $this->belongsTo(HoSoDocGia::class, 'maDocGia', 'maDocGia');
    }

    public function chiTiet()
    {
        return $this->hasMany(ChiTietYeuCauMuon::class, 'maYeuCauMuon', 'maYeuCauMuon');
    }
}