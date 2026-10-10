<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChiTietYeuCauMuon extends Model
{
    protected $table = 'chitietyeucaumuon';
    protected $primaryKey = 'maChiTietYeuCauMuon';
    public $timestamps = false;
    protected $fillable = ['maYeuCauMuon', 'maTaiLieu', 'ghiChu'];

    public function taiLieu()
    {
        return $this->belongsTo(TaiLieu::class, 'maTaiLieu', 'maTaiLieu');
    }
}