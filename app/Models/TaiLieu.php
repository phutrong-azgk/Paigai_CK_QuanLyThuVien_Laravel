<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaiLieu extends Model
{
    protected $table = 'tailieu';
    protected $primaryKey = 'maTaiLieu';
    public $timestamps = false;
    protected $fillable = [
        'maKhoa', 'maNhaXuatBan', 'ma', 'tieuDe', 'namXuatBan', 'soLuongTong', 'soLuongCon', 'duocMuon', 'maIsbn', 'anhBia', 'tomTat'
    ];

    public function khoa()
    {
        return $this->belongsTo(Khoa::class, 'maKhoa', 'maKhoa');
    }

    public function nhaXuatBan()
    {
        return $this->belongsTo(NhaXuatBan::class, 'maNhaXuatBan', 'maNhaXuatBan');
    }
}