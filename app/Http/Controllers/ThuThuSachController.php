<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuThuSachController extends Controller
{
    private function kiemTraThuThu()
    {
        $user = session('library_user');
        if (!$user || session('role') !== 'librarian') {
            abort(response()->json(['loi' => 'Bạn không có quyền thực hiện thao tác này.'], 403));
        }
    }

    public function handle(Request $request)
    {
        $this->kiemTraThuThu();

        if ($request->isMethod('GET')) {
            $hanhDong = $request->input('hanhDong', 'danhSach');
            $trang = max(1, (int)$request->input('trang', 1));
            $gioiHan = min(50, max(1, (int)$request->input('gioiHan', 8)));
            $layTatCa = ($request->input('layTatCa') === '1');
            $tuKhoa = trim($request->input('tuKhoa', ''));

            if ($hanhDong === 'danhMuc') {
                $duLieu = DB::table('khoa')->select('maKhoa as maDanhMuc', 'ma', 'ten')->orderBy('ten')->get();
                return response()->json(['duLieu' => $duLieu]);
            }

            if ($hanhDong === 'nhaXuatBan') {
                $query = DB::table('nhaxuatban as nxb')
                    ->leftJoin('tailieu as tl', 'tl.maNhaXuatBan', '=', 'nxb.maNhaXuatBan')
                    ->select('nxb.maNhaXuatBan', 'nxb.ma', 'nxb.ten', 'nxb.diaChi', 'nxb.soDienThoai', DB::raw('COUNT(tl.maTaiLieu) as soTaiLieu'))
                    ->groupBy('nxb.maNhaXuatBan')
                    ->orderBy('nxb.ten');

                if ($tuKhoa) {
                    $query->where(function($q) use ($tuKhoa) {
                        $q->where('nxb.ma', 'like', "%$tuKhoa%")
                          ->orWhere('nxb.ten', 'like', "%$tuKhoa%")
                          ->orWhere('nxb.diaChi', 'like', "%$tuKhoa%");
                    });
                }

                $tong = $query->count();
                if (!$layTatCa) {
                    $query->skip(($trang - 1) * $gioiHan)->take($gioiHan);
                }
                $duLieu = $query->get();

                return response()->json(['duLieu' => $duLieu, 'tong' => $tong, 'trang' => $trang, 'gioiHan' => $gioiHan]);
            }

            $maKhoa = (int)$request->input('maKhoa', 0);
            $query = DB::table('tailieu as tl')
                ->leftJoin('khoa as k', 'k.maKhoa', '=', 'tl.maKhoa')
                ->leftJoin('nhaxuatban as nxb', 'nxb.maNhaXuatBan', '=', 'tl.maNhaXuatBan')
                ->select(
                    'tl.maTaiLieu', 'tl.ma', 'tl.tieuDe', 'tl.maIsbn', 'tl.viTriKe', 'tl.namXuatBan', 'tl.anhBia', 'tl.tomTat',
                    'tl.soLuongTong as tongBan', 'tl.soLuongCon as soBanSanSang', 'k.ten as khoa', 'nxb.maNhaXuatBan', 'nxb.ten as nhaXuatBan'
                );

            if ($tuKhoa) {
                $query->where(function($q) use ($tuKhoa) {
                    $q->where('tl.ma', 'like', "%$tuKhoa%")
                      ->orWhere('tl.tieuDe', 'like', "%$tuKhoa%")
                      ->orWhere('nxb.ten', 'like', "%$tuKhoa%");
                });
            }

            if ($maKhoa) {
                $query->where('tl.maKhoa', $maKhoa);
            }

            $tong = $query->count();
            $query->orderBy('tl.tieuDe');

            if (!$layTatCa) {
                $query->skip(($trang - 1) * $gioiHan)->take($gioiHan);
            }

            $duLieu = $query->get();
            return response()->json(['duLieu' => $duLieu, 'tong' => $tong, 'trang' => $trang, 'gioiHan' => $gioiHan]);
        }

        $hanhDong = $request->input('hanhDong', '');

        if ($hanhDong === 'luuTomTat') {
            $maTaiLieu = (int)$request->input('maTaiLieu', 0);
            $tomTat = trim($request->input('tomTat', ''));

            if (!$maTaiLieu) return response()->json(['loi' => 'Hãy chọn tài liệu cần cập nhật.'], 422);
            if (mb_strlen($tomTat) > 3000) return response()->json(['loi' => 'Tóm tắt không được vượt quá 3.000 ký tự.'], 422);

            $exists = DB::table('tailieu')->where('maTaiLieu', $maTaiLieu)->exists();
            if (!$exists) return response()->json(['loi' => 'Không tìm thấy tài liệu.'], 404);

            DB::table('tailieu')->where('maTaiLieu', $maTaiLieu)->update(['tomTat' => $tomTat ?: null]);
            return response()->json(['thanhCong' => true]);
        }

        if (in_array($hanhDong, ['luuNhaXuatBan', 'xoaNhaXuatBan'], true)) {
            if ($hanhDong === 'xoaNhaXuatBan') {
                $maNXB = (int)$request->input('maNhaXuatBan', 0);
                try {
                    $deleted = DB::table('nhaxuatban')->where('maNhaXuatBan', $maNXB)->delete();
                    if (!$deleted) return response()->json(['loi' => 'Không tìm thấy nhà xuất bản.'], 404);
                    return response()->json(['thanhCong' => true]);
                } catch (\Exception $e) {
                    return response()->json(['loi' => 'Không thể xóa nhà xuất bản đang có tài liệu.'], 409);
                }
            }

            $maNXB = (int)$request->input('maNhaXuatBan', 0);
            $ma = strtoupper(trim($request->input('ma', '')));
            $ten = trim($request->input('ten', ''));

            if (!preg_match('/^[A-Z0-9-]{2,30}$/', $ma) || mb_strlen($ten) < 2) {
                return response()->json(['loi' => 'Mã hoặc tên nhà xuất bản chưa hợp lệ.'], 422);
            }

            try {
                if ($maNXB) {
                    DB::table('nhaxuatban')->where('maNhaXuatBan', $maNXB)->update([
                        'ma' => $ma,
                        'ten' => $ten,
                        'diaChi' => trim($request->input('diaChi', '')) ?: null,
                        'soDienThoai' => trim($request->input('soDienThoai', '')) ?: null,
                    ]);
                } else {
                    $maNXB = DB::table('nhaxuatban')->insertGetId([
                        'ma' => $ma,
                        'ten' => $ten,
                        'diaChi' => trim($request->input('diaChi', '')) ?: null,
                        'soDienThoai' => trim($request->input('soDienThoai', '')) ?: null,
                    ]);
                }
                return response()->json(['thanhCong' => true, 'maNhaXuatBan' => $maNXB]);
            } catch (\Exception $e) {
                return response()->json(['loi' => 'Mã nhà xuất bản đã tồn tại.'], 409);
            }
        }

        if ($hanhDong === 'luu') {
            $maTaiLieu = (int)$request->input('maTaiLieu', 0);
            $ma = strtoupper(trim($request->input('ma', '')));
            $tieuDe = trim($request->input('tieuDe', ''));
            $maKhoa = (int)$request->input('maDanhMuc', 0);
            $maNhaXuatBan = (int)$request->input('maNhaXuatBan', 0);
            $namXuatBan = (int)$request->input('namXuatBan', 0);
            $maIsbn = trim($request->input('maIsbn', ''));
            $keSach = strtoupper(trim($request->input('keSach', '')));
            $tangKe = (int)$request->input('tangKe', 0);
            $nganKe = (int)$request->input('nganKe', 0);
            $anhBia = trim($request->input('anhBiaHienTai', ''));
            $soLuongTong = (int)$request->input('soLuongTong', -1);

            if (!preg_match('/^[A-Z0-9-]{3,30}$/', $ma) || mb_strlen($tieuDe) < 2 || !$maKhoa || !$maNhaXuatBan || !preg_match('/^[A-Z]{1,2}$/', $keSach) || $tangKe < 1 || $tangKe > 10 || $nganKe < 1 || $nganKe > 50 || $soLuongTong < 0) {
                return response()->json(['loi' => 'Thông tin tài liệu, khoa hoặc nhà xuất bản chưa hợp lệ.'], 422);
            }

            $viTriKe = "Kệ $keSach · Tầng $tangKe · Ngăn " . str_pad((string)$nganKe, 2, '0', STR_PAD_LEFT);

            if ($request->hasFile('anhBia')) {
                $file = $request->file('anhBia');
                if (!$file->isValid() || $file->getSize() > 5 * 1024 * 1024) {
                    return response()->json(['loi' => 'Không thể tải ảnh hoặc ảnh vượt quá 5 MB.'], 422);
                }
                $ext = $file->getClientOriginalExtension();
                $filename = bin2hex(random_bytes(16)) . '.' . $ext;
                $file->move(public_path('assets/images/sach'), $filename);
                $anhBia = 'assets/images/sach/' . $filename;
            }

            try {
                DB::beginTransaction();
                if ($maTaiLieu) {
                    $cu = DB::table('tailieu')->where('maTaiLieu', $maTaiLieu)->lockForUpdate()->first();
                    if (!$cu) throw new \Exception('Không tìm thấy tài liệu.');

                    $soDangMuon = (int)$cu->soLuongTong - (int)$cu->soLuongCon;
                    if ($soLuongTong < $soDangMuon) throw new \Exception('Số lượng tổng không được nhỏ hơn số sách đang mượn.');

                    DB::table('tailieu')->where('maTaiLieu', $maTaiLieu)->update([
                        'ma' => $ma,
                        'maKhoa' => $maKhoa,
                        'maNhaXuatBan' => $maNhaXuatBan,
                        'tieuDe' => $tieuDe,
                        'maIsbn' => $maIsbn ?: null,
                        'viTriKe' => $viTriKe,
                        'namXuatBan' => $namXuatBan ?: null,
                        'anhBia' => $anhBia ?: null,
                        'soLuongTong' => $soLuongTong,
                        'soLuongCon' => $soLuongTong - $soDangMuon,
                    ]);
                } else {
                    DB::table('tailieu')->insert([
                        'ma' => $ma,
                        'maKhoa' => $maKhoa,
                        'maNhaXuatBan' => $maNhaXuatBan,
                        'tieuDe' => $tieuDe,
                        'maIsbn' => $maIsbn ?: null,
                        'viTriKe' => $viTriKe,
                        'namXuatBan' => $namXuatBan ?: null,
                        'anhBia' => $anhBia ?: null,
                        'duocMuon' => 'co',
                        'soLuongTong' => $soLuongTong,
                        'soLuongCon' => $soLuongTong,
                    ]);
                }
                DB::commit();
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => $e->getMessage() ?: 'Không thể lưu tài liệu.'], 409);
            }
        }

        if ($hanhDong === 'xoa') {
            try {
                $deleted = DB::table('tailieu')->where('maTaiLieu', (int)$request->input('maTaiLieu', 0))->delete();
                if (!$deleted) return response()->json(['loi' => 'Không tìm thấy tài liệu.'], 404);
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                return response()->json(['loi' => 'Không thể xóa tài liệu đã có lịch sử mượn.'], 409);
            }
        }

        return response()->json(['loi' => 'Thao tác không hợp lệ.'], 404);
    }
}
