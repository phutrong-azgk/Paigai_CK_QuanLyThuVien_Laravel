<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocGiaController extends Controller
{
    private function kiemTraDocGia()
    {
        $user = session('library_user');
        if (!$user || session('role') !== 'reader') {
            abort(response()->json(['loi' => 'Bạn không có quyền thực hiện thao tác này.'], 403));
        }

        $maDocGia = DB::table('hosodocgia')->where('maNguoiDung', $user['id'])->value('maDocGia');
        if (!$maDocGia) {
            abort(response()->json(['loi' => 'Không tìm thấy hồ sơ độc giả.'], 404));
        }

        return [$user, $maDocGia];
    }

    public function handle(Request $request)
    {
        list($nguoiDung, $maDocGia) = $this->kiemTraDocGia();
        $hanhDong = $request->input('hanhDong', '');

        if ($hanhDong === 'chiTietTaiLieu') {
            $ma = trim($request->input('ma', ''));
            if (!$ma) return response()->json(['loi' => 'Thiếu mã tài liệu.'], 422);

            $taiLieu = DB::table('tailieu as tl')
                ->leftJoin('khoa as k', 'k.maKhoa', '=', 'tl.maKhoa')
                ->leftJoin('nhaxuatban as nxb', 'nxb.maNhaXuatBan', '=', 'tl.maNhaXuatBan')
                ->select('tl.maTaiLieu', 'tl.ma', 'tl.tieuDe', 'tl.namXuatBan', 'tl.anhBia', 'tl.tomTat', 'tl.soLuongCon as soBan', 'tl.duocMuon', 'k.ten as khoa', 'nxb.ten as nhaXuatBan')
                ->where('tl.ma', $ma)
                ->first();

            if (!$taiLieu) return response()->json(['loi' => 'Không tìm thấy tài liệu.'], 404);
            return response()->json(['duLieu' => $taiLieu]);
        }

        if ($hanhDong === 'hoSo') {
            if ($request->isMethod('GET')) {
                $hoSo = DB::table('nguoidung as nd')
                    ->join('hosodocgia as dg', 'dg.maNguoiDung', '=', 'nd.maNguoiDung')
                    ->leftJoin('khoa as k', 'k.maKhoa', '=', 'dg.maKhoa')
                    ->select('nd.hoTen', 'nd.thuDienTu', 'nd.soDienThoai', 'nd.ngaySinh', 'dg.maSo', 'dg.loaiDocGia', 'k.ten as khoa')
                    ->where('nd.maNguoiDung', $nguoiDung['id'])
                    ->first();
                return response()->json(['duLieu' => $hoSo]);
            }

            if ($request->isMethod('POST')) {
                $hoTen = trim($request->input('hoTen', ''));
                $thuDienTu = trim($request->input('thuDienTu', ''));
                $soDienThoai = trim($request->input('soDienThoai', ''));
                $ngaySinh = $request->input('ngaySinh');

                if (mb_strlen($hoTen) < 3 || !filter_var($thuDienTu, FILTER_VALIDATE_EMAIL)) {
                    return response()->json(['loi' => 'Dữ liệu hồ sơ chưa hợp lệ.'], 422);
                }

                $kiemTra = DB::table('nguoidung')
                    ->where('thuDienTu', $thuDienTu)
                    ->where('maNguoiDung', '<>', $nguoiDung['id'])
                    ->exists();

                if ($kiemTra) return response()->json(['loi' => 'Email đã được tài khoản khác sử dụng.'], 409);

                DB::table('nguoidung')->where('maNguoiDung', $nguoiDung['id'])->update([
                    'hoTen' => $hoTen,
                    'thuDienTu' => $thuDienTu,
                    'soDienThoai' => $soDienThoai ?: null,
                    'ngaySinh' => $ngaySinh ?: null,
                ]);

                // Update session
                $u = session('library_user');
                $u['name'] = $hoTen;
                $u['email'] = $thuDienTu;
                $u['phone'] = $soDienThoai;
                $u['birth_date'] = $ngaySinh;
                $u['initials'] = mb_strtoupper(mb_substr($hoTen, 0, 1));
                session(['library_user' => $u]);

                return response()->json(['thanhCong' => true]);
            }
        }

        if ($hanhDong === 'tongQuan') {
            $dangMuon = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->where('pm.maDocGia', $maDocGia)
                ->whereNull('ct.ngayTra')
                ->count();

            $sapDenHan = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->where('pm.maDocGia', $maDocGia)
                ->whereNull('ct.ngayTra')
                ->where('ct.hanTra', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 3 DAY)'))
                ->count();

            $choDuyet = DB::table('yeucaumuon')
                ->where('maDocGia', $maDocGia)
                ->where('trangThai', 'choDuyet')
                ->count();

            $daTra = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->where('pm.maDocGia', $maDocGia)
                ->whereNotNull('ct.ngayTra')
                ->count();

            $dangMuonGanDay = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                ->select('ct.maChiTietPhieuMuon', 'tl.ma', 'tl.tieuDe', 'ct.hanTra')
                ->where('pm.maDocGia', $maDocGia)
                ->whereNull('ct.ngayTra')
                ->orderBy('ct.hanTra')
                ->limit(2)
                ->get();

            $maKhoaDocGia = DB::table('hosodocgia')->where('maDocGia', $maDocGia)->value('maKhoa');

            $goiY = DB::table('tailieu as tl')
                ->leftJoin('khoa as k', 'k.maKhoa', '=', 'tl.maKhoa')
                ->leftJoin('nhaxuatban as nxb', 'nxb.maNhaXuatBan', '=', 'tl.maNhaXuatBan')
                ->select('tl.ma', 'tl.tieuDe', 'k.ten as khoa', 'nxb.ten as nhaXuatBan')
                ->where('tl.soLuongCon', '>', 0)
                ->where('tl.duocMuon', 'co')
                ->orderByRaw('CASE WHEN tl.maKhoa = ? THEN 0 ELSE 1 END', [$maKhoaDocGia ?: 0])
                ->orderBy('tl.tieuDe')
                ->first();

            return response()->json([
                'duLieu' => [
                    'dangMuon' => $dangMuon,
                    'sapDenHan' => $sapDenHan,
                    'choDuyet' => $choDuyet,
                    'daTra' => $daTra,
                ],
                'dangMuonGanDay' => $dangMuonGanDay,
                'goiY' => $goiY ?: null,
            ]);
        }

        if ($hanhDong === 'taiLieu') {
            $taiLieu = DB::table('tailieu as tl')
                ->leftJoin('khoa as k', 'k.maKhoa', '=', 'tl.maKhoa')
                ->leftJoin('nhaxuatban as nxb', 'nxb.maNhaXuatBan', '=', 'tl.maNhaXuatBan')
                ->select('tl.maTaiLieu', 'tl.ma', 'tl.tieuDe', 'tl.anhBia', 'k.ten as danhMuc', 'nxb.ten as nhaXuatBan', 'tl.soLuongCon as soBan')
                ->orderBy('tl.tieuDe')
                ->get();
            return response()->json(['duLieu' => $taiLieu]);
        }

        if ($hanhDong === 'phieuMuon') {
            $lichSu = ($request->input('loai') === 'lichSu');
            $query = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                ->select(
                    'ct.maChiTietPhieuMuon', 'tl.ma', 'tl.tieuDe', 'pm.ngayMuon', 'ct.hanTra', 'ct.ngayTra', 'ct.trangThai',
                    DB::raw('(SELECT yg.trangThai FROM yeucaugiahan yg WHERE yg.maChiTietPhieuMuon = ct.maChiTietPhieuMuon ORDER BY yg.ngayYeuCau DESC LIMIT 1) as trangThaiGiaHan')
                )
                ->where('pm.maDocGia', $maDocGia);

            if ($lichSu) {
                $query->whereNotNull('ct.ngayTra');
            } else {
                $query->whereNull('ct.ngayTra');
            }

            $duLieu = $query->orderByDesc('pm.ngayMuon')->get();
            return response()->json(['duLieu' => $duLieu]);
        }

        if ($hanhDong === 'lichSuHoatDong') {
            $daTra = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                ->select(
                    'tl.ma', 'tl.tieuDe', 'pm.ngayMuon as ngayHoatDong', 'ct.ngayTra',
                    DB::raw("'daTra' as trangThaiHoatDong"),
                    'ct.tinhTrangKhiTra as ghiChu'
                )
                ->where('pm.maDocGia', $maDocGia)
                ->whereNotNull('ct.ngayTra');

            $tuChoi = DB::table('yeucaumuon as yc')
                ->join('chitietyeucaumuon as ct', 'ct.maYeuCauMuon', '=', 'yc.maYeuCauMuon')
                ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                ->select(
                    DB::raw("GROUP_CONCAT(tl.ma SEPARATOR ', ') as ma"),
                    DB::raw("GROUP_CONCAT(tl.tieuDe SEPARATOR ', ') as tieuDe"),
                    'yc.ngayYeuCau as ngayHoatDong',
                    DB::raw("NULL as ngayTra"),
                    DB::raw("'tuChoi' as trangThaiHoatDong"),
                    'yc.lyDoTuChoi as ghiChu'
                )
                ->where('yc.maDocGia', $maDocGia)
                ->where('yc.trangThai', 'tuChoi')
                ->groupBy('yc.maYeuCauMuon', 'yc.ngayYeuCau', 'yc.lyDoTuChoi');

            $duLieu = $daTra->unionAll($tuChoi)->orderByDesc('ngayHoatDong')->get();
            return response()->json(['duLieu' => $duLieu]);
        }

        if ($hanhDong === 'yeuCauMuon' && $request->isMethod('GET')) {
            $duLieu = DB::table('yeucaumuon as yc')
                ->join('chitietyeucaumuon as ct', 'ct.maYeuCauMuon', '=', 'yc.maYeuCauMuon')
                ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                ->select(
                    'yc.maYeuCauMuon',
                    'yc.ngayYeuCau',
                    DB::raw("GROUP_CONCAT(CONCAT(tl.ma, '|', tl.tieuDe) SEPARATOR '||') as taiLieu")
                )
                ->where('yc.maDocGia', $maDocGia)
                ->where('yc.trangThai', 'choDuyet')
                ->groupBy('yc.maYeuCauMuon', 'yc.ngayYeuCau')
                ->orderByDesc('yc.ngayYeuCau')
                ->get();

            return response()->json(['duLieu' => $duLieu]);
        }

        if ($hanhDong === 'yeuCauMuon' && $request->isMethod('POST')) {
            $maTaiLieuList = array_values(array_unique(array_filter(array_map('intval', (array)$request->input('maTaiLieu', [])))));
            if (empty($maTaiLieuList)) return response()->json(['loi' => 'Vui lòng chọn ít nhất một tài liệu.'], 422);

            $docGiaInfo = DB::table('hosodocgia')->where('maDocGia', $maDocGia)->first();
            if ($docGiaInfo->trangThaiThe !== 'hoatDong') return response()->json(['loi' => 'Thẻ thư viện của bạn đang bị khóa.'], 403);

            $chinhSach = DB::table('chinhsachmuon')
                ->where('loaiDocGia', $docGiaInfo->loaiDocGia)
                ->where('trangThai', 'dangApDung')
                ->orderByDesc('ngayApDung')
                ->first();

            $soSachToiDa = $chinhSach ? $chinhSach->soSachToiDa : 3;

            $dangMuon = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->where('pm.maDocGia', $maDocGia)
                ->whereNull('ct.ngayTra')
                ->count();

            $dangCho = DB::table('chitietyeucaumuon as ct')
                ->join('yeucaumuon as yc', 'yc.maYeuCauMuon', '=', 'ct.maYeuCauMuon')
                ->where('yc.maDocGia', $maDocGia)
                ->where('yc.trangThai', 'choDuyet')
                ->count();

            if ($dangMuon + $dangCho + count($maTaiLieuList) > $soSachToiDa) {
                return response()->json(['loi' => "Bạn chỉ được mượn tối đa $soSachToiDa tài liệu (bao gồm đang mượn và đang chờ duyệt)."], 422);
            }

            try {
                DB::beginTransaction();
                $maYeuCau = DB::table('yeucaumuon')->insertGetId([
                    'maDocGia' => $maDocGia,
                    'ngayYeuCau' => now(),
                    'trangThai' => 'choDuyet',
                ]);

                foreach ($maTaiLieuList as $tlId) {
                    DB::table('chitietyeucaumuon')->insert([
                        'maYeuCauMuon' => $maYeuCau,
                        'maTaiLieu' => $tlId,
                    ]);
                }

                DB::commit();
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => 'Không thể gửi yêu cầu mượn.'], 500);
            }
        }

        if ($hanhDong === 'huyYeuCauMuon' && $request->isMethod('POST')) {
            $maYeuCau = (int)$request->input('maYeuCauMuon');
            $updated = DB::table('yeucaumuon')
                ->where('maYeuCauMuon', $maYeuCau)
                ->where('maDocGia', $maDocGia)
                ->where('trangThai', 'choDuyet')
                ->update(['trangThai' => 'daHuy']);

            if (!$updated) return response()->json(['loi' => 'Không tìm thấy yêu cầu chờ duyệt phù hợp.'], 404);
            return response()->json(['thanhCong' => true]);
        }

        if ($hanhDong === 'yeuCauGiaHan' && $request->isMethod('POST')) {
            $maChiTiet = (int)$request->input('maChiTietPhieuMuon');
            $lyDo = trim($request->input('lyDo', ''));

            $chiTiet = DB::table('chitietphieumuon as ct')
                ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                ->where('ct.maChiTietPhieuMuon', $maChiTiet)
                ->where('pm.maDocGia', $maDocGia)
                ->whereNull('ct.ngayTra')
                ->first();

            if (!$chiTiet) return response()->json(['loi' => 'Không tìm thấy thông tin mượn sách hợp lệ.'], 404);

            $dangChoGiaHan = DB::table('yeucaugiahan')
                ->where('maChiTietPhieuMuon', $maChiTiet)
                ->where('trangThai', 'choDuyet')
                ->exists();

            if ($dangChoGiaHan) return response()->json(['loi' => 'Tài liệu này đã có yêu cầu gia hạn đang chờ duyệt.'], 409);

            DB::table('yeucaugiahan')->insert([
                'maChiTietPhieuMuon' => $maChiTiet,
                'ngayYeuCau' => now(),
                'lyDo' => $lyDo ?: null,
                'trangThai' => 'choDuyet',
            ]);

            return response()->json(['thanhCong' => true]);
        }

        return response()->json(['loi' => 'Hành động không hợp lệ.'], 404);
    }
}
