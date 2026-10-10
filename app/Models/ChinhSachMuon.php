<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChinhSachMuon extends Model
{
    protected $table = 'chinhsachmuon';
    protected $primaryKey = 'maChinhSach';
    public $timestamps = false;
    protected $fillable = [
        'loaiDocGia', 'soSachToiDa', 'soNgayMuon', 'soLanGiaHan', 'soNgayGiaHan', 'tienPhatMoiNgay', 'tienPhatHuHongNhe', 'tienPhatHuHongNang', 'ngayApDung', 'trangThai'
    ];
}