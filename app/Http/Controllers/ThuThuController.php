<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ThuThuController extends Controller
{
    private function kiemTraThuThu()
    {
        $user = session('library_user');
        if (!$user || session('role') !== 'librarian') {
            abort(response()->json(['loi' => 'Bạn không có quyền thực hiện thao tác này.'], 403));
        }
        return $user;
    }

    public function handle(Request $request)
    {
        $thuThu = $this->kiemTraThuThu();
        $hanhDong = $request->input('hanhDong', '');

        if ($request->isMethod('GET')) {
            if ($hanhDong === 'yeuCau') {
                $duLieu = DB::table('yeucaumuon as yc')
                    ->join('hosodocgia as dg', 'dg.maDocGia', '=', 'yc.maDocGia')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->join('chitietyeucaumuon as ct', 'ct.maYeuCauMuon', '=', 'yc.maYeuCauMuon')
                    ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                    ->select(
                        'yc.maYeuCauMuon',
                        'yc.ngayYeuCau',
                        'dg.maSo',
                        'dg.trangThaiThe',
                        'nd.hoTen',
                        'nd.trangThai as trangThaiTaiKhoan',
                        DB::raw("GROUP_CONCAT(CONCAT(tl.ma, ' · ', tl.tieuDe) SEPARATOR '||') as taiLieu"),
                        DB::raw("COUNT(ct.maChiTietYeuCauMuon) as soTaiLieu"),
                        DB::raw("SUM(tl.soLuongCon > 0) as soTaiLieuSanSang")
                    )
                    ->where('yc.trangThai', 'choDuyet')
                    ->groupBy('yc.maYeuCauMuon')
                    ->orderBy('yc.ngayYeuCau')
                    ->get();

                return response()->json(['duLieu' => $duLieu]);
            }

            if ($hanhDong === 'docGia') {
                $duLieu = DB::table('hosodocgia as dg')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->leftJoin('khoa as k', 'k.maKhoa', '=', 'dg.maKhoa')
                    ->select(
                        'dg.maDocGia',
                        'dg.maSo',
                        'dg.loaiDocGia',
                        'dg.trangThaiThe',
                        'nd.hoTen',
                        'nd.thuDienTu',
                        'nd.trangThai',
                        'k.ten as khoa',
                        DB::raw('(SELECT COUNT(*) FROM phieumuon pm JOIN chitietphieumuon ct ON ct.maPhieuMuon = pm.maPhieuMuon WHERE pm.maDocGia = dg.maDocGia AND ct.ngayTra IS NULL) as dangMuon'),
                        DB::raw('(SELECT COALESCE(SUM(soTien), 0) FROM phieuphat pp WHERE pp.maDocGia = dg.maDocGia) as tienPhat')
                    )
                    ->orderBy('nd.hoTen')
                    ->get();

                return response()->json(['duLieu' => $duLieu]);
            }

            if ($hanhDong === 'phat') {
                $duLieu = DB::table('phieuphat as pp')
                    ->join('hosodocgia as dg', 'dg.maDocGia', '=', 'pp.maDocGia')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->select('pp.maPhieuPhat', 'pp.loaiPhat', 'pp.soTien', 'pp.trangThai', 'pp.ngayLap', 'dg.maSo', 'nd.hoTen')
                    ->orderByDesc('pp.ngayLap')
                    ->get();

                return response()->json(['duLieu' => $duLieu]);
            }

            if ($hanhDong === 'tongQuan') {
                $choDuyet = DB::table('yeucaumuon')->where('trangThai', 'choDuyet')->count();
                $muonHomNay = DB::table('phieumuon')->whereDate('ngayMuon', now()->toDateString())->count();
                $traHomNay = DB::table('chitietphieumuon')->whereDate('ngayTra', now()->toDateString())->count();
                $quaHan = DB::table('chitietphieumuon')->whereNull('ngayTra')->where('hanTra', '<', now()->toDateString())->count();
                $chuaThu = DB::table('phieuphat')->where('trangThai', 'chuaThanhToan')->sum('soTien') ?: 0;
                $docGiaPhat = DB::table('phieuphat')->where('trangThai', 'chuaThanhToan')->distinct('maDocGia')->count('maDocGia');

                $denHan = DB::table('chitietphieumuon as ct')
                    ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                    ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                    ->join('hosodocgia as dg', 'dg.maDocGia', '=', 'pm.maDocGia')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->select('tl.tieuDe', 'nd.hoTen', 'dg.maSo', 'ct.hanTra')
                    ->whereNull('ct.ngayTra')
                    ->where('ct.hanTra', '<=', DB::raw('DATE_ADD(CURDATE(), INTERVAL 3 DAY)'))
                    ->orderBy('ct.hanTra')
                    ->limit(5)
                    ->get();

                return response()->json([
                    'duLieu' => compact('choDuyet', 'muonHomNay', 'traHomNay', 'quaHan', 'chuaThu', 'docGiaPhat'),
                    'denHan' => $denHan
                ]);
            }

            if ($hanhDong === 'traCuuDocGia') {
                $tuKhoa = trim($request->input('tuKhoa', ''));
                if (!$tuKhoa) return response()->json(['loi' => 'Vui lòng nhập mã độc giả.'], 422);

                $docGia = DB::table('hosodocgia as dg')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->leftJoin('khoa as k', 'k.maKhoa', '=', 'dg.maKhoa')
                    ->select(
                        'dg.maDocGia',
                        'dg.maSo',
                        'dg.loaiDocGia',
                        'dg.trangThaiThe',
                        'nd.hoTen',
                        'nd.trangThai',
                        'k.ten as khoa',
                        DB::raw('(SELECT COUNT(*) FROM phieumuon pm JOIN chitietphieumuon ct ON ct.maPhieuMuon = pm.maPhieuMuon WHERE pm.maDocGia = dg.maDocGia AND ct.ngayTra IS NULL) as dangMuon'),
                        DB::raw('(SELECT cs.soSachToiDa FROM chinhsachmuon cs WHERE cs.loaiDocGia = dg.loaiDocGia AND cs.trangThai = "dangApDung" ORDER BY cs.ngayApDung DESC LIMIT 1) as soSachToiDa'),
                        DB::raw('(SELECT cs.soNgayMuon FROM chinhsachmuon cs WHERE cs.loaiDocGia = dg.loaiDocGia AND cs.trangThai = "dangApDung" ORDER BY cs.ngayApDung DESC LIMIT 1) as soNgayMuon')
                    )
                    ->where('dg.maSo', $tuKhoa)
                    ->orWhere('nd.tenDangNhap', strtoupper($tuKhoa))
                    ->first();

                if (!$docGia) return response()->json(['loi' => 'Không tìm thấy độc giả.'], 404);
                return response()->json(['duLieu' => $docGia]);
            }

            if ($hanhDong === 'taiLieuChon') {
                $loai = $request->input('loai', 'muon');
                $tuKhoa = trim($request->input('tuKhoa', ''));

                if ($loai === 'tra') {
                    $duLieu = DB::table('chitietphieumuon as ct')
                        ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                        ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                        ->join('hosodocgia as dg', 'dg.maDocGia', '=', 'pm.maDocGia')
                        ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                        ->select('ct.maChiTietPhieuMuon', 'tl.ma', 'tl.tieuDe', 'nd.hoTen', 'dg.maSo', 'pm.ngayMuon', 'ct.hanTra')
                        ->whereNull('ct.ngayTra')
                        ->where(function($q) use ($tuKhoa) {
                            if ($tuKhoa) {
                                $q->where('tl.ma', 'like', "%$tuKhoa%")
                                  ->orWhere('tl.tieuDe', 'like', "%$tuKhoa%")
                                  ->orWhere('nd.hoTen', 'like', "%$tuKhoa%")
                                  ->orWhere('dg.maSo', 'like', "%$tuKhoa%");
                            }
                        })
                        ->orderBy('ct.hanTra')
                        ->get();
                } else {
                    $duLieu = DB::table('tailieu')
                        ->select('maTaiLieu', 'ma', 'tieuDe', 'soLuongCon')
                        ->where('duocMuon', 'co')
                        ->where('soLuongCon', '>', 0)
                        ->where(function($q) use ($tuKhoa) {
                            if ($tuKhoa) {
                                $q->where('ma', 'like', "%$tuKhoa%")
                                  ->orWhere('tieuDe', 'like', "%$tuKhoa%");
                            }
                        })
                        ->orderBy('tieuDe')
                        ->get();
                }

                return response()->json(['duLieu' => $duLieu]);
            }

            if ($hanhDong === 'uocTinhPhatTra') {
                $chiTiet = array_values(array_unique(array_filter(array_map('intval', explode(',', $request->input('maChiTietPhieuMuon', ''))))));
                $tinhTrangKhiTra = $request->input('tinhTrangKhiTra', 'nguyenVen');

                if (!in_array($tinhTrangKhiTra, ['nguyenVen', 'huHongNhe', 'huHongNang'], true)) {
                    return response()->json(['loi' => 'Tình trạng tài liệu không hợp lệ.'], 422);
                }

                if (empty($chiTiet)) {
                    return response()->json(['duLieu' => ['phi' => 0, 'phiQuaHan' => 0, 'phiHuHong' => 0]]);
                }

                $phiQuaHan = 0;
                $phiHuHong = 0;

                foreach ($chiTiet as $maChiTiet) {
                    $muc = DB::table('chitietphieumuon as ct')
                        ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                        ->select('ct.hanTra', DB::raw('GREATEST(0, DATEDIFF(CURDATE(), ct.hanTra)) as soNgayQuaHan'), 'pm.maChinhSach')
                        ->where('ct.maChiTietPhieuMuon', $maChiTiet)
                        ->whereNull('ct.ngayTra')
                        ->first();

                    if (!$muc) continue;

                    $mucPhat = DB::table('chinhsachmuon')
                        ->select('tienPhatMoiNgay', 'tienPhatHuHongNhe', 'tienPhatHuHongNang')
                        ->where('maChinhSach', $muc->maChinhSach)
                        ->first();

                    if ($mucPhat) {
                        $phiQuaHan += (int)$muc->soNgayQuaHan * (float)$mucPhat->tienPhatMoiNgay;
                        if ($tinhTrangKhiTra === 'huHongNhe') $phiHuHong += (float)$mucPhat->tienPhatHuHongNhe;
                        if ($tinhTrangKhiTra === 'huHongNang') $phiHuHong += (float)$mucPhat->tienPhatHuHongNang;
                    }
                }

                return response()->json([
                    'duLieu' => [
                        'phi' => $phiQuaHan + $phiHuHong,
                        'phiQuaHan' => $phiQuaHan,
                        'phiHuHong' => $phiHuHong
                    ]
                ]);
            }

            if ($hanhDong === 'yeuCauGiaHan') {
                $duLieu = DB::table('yeucaugiahan as yg')
                    ->join('chitietphieumuon as ct', 'ct.maChiTietPhieuMuon', '=', 'yg.maChiTietPhieuMuon')
                    ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                    ->join('tailieu as tl', 'tl.maTaiLieu', '=', 'ct.maTaiLieu')
                    ->join('hosodocgia as dg', 'dg.maDocGia', '=', 'pm.maDocGia')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->select(
                        'yg.maYeuCauGiaHan', 'ct.maChiTietPhieuMuon', 'tl.tieuDe', 'tl.ma', 'dg.maSo', 'nd.hoTen', 'ct.hanTra',
                        'yg.hanTraMoi'
                    )
                    ->where('yg.trangThai', 'choDuyet')
                    ->orderBy('yg.ngayYeuCau')
                    ->get();

                return response()->json(['duLieu' => $duLieu]);
            }
        }

        // POST ACTIONS
        if ($hanhDong === 'lapPhieuMuon') {
            $maDocGia = (int)$request->input('maDocGia', 0);
            $taiLieu = array_values(array_unique(array_map('intval', (array)$request->input('maTaiLieu', []))));

            if (!$maDocGia || empty($taiLieu)) return response()->json(['loi' => 'Chưa chọn độc giả hoặc tài liệu.'], 422);

            try {
                DB::beginTransaction();

                $docGia = DB::table('hosodocgia as dg')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->select('dg.loaiDocGia', 'dg.trangThaiThe', 'nd.trangThai')
                    ->where('dg.maDocGia', $maDocGia)
                    ->lockForUpdate()
                    ->first();

                if (!$docGia || $docGia->trangThaiThe !== 'hoatDong' || $docGia->trangThai !== 'hoatDong') {
                    throw new \Exception('Thẻ hoặc tài khoản độc giả không hoạt động.');
                }

                $chinhSach = DB::table('chinhsachmuon')
                    ->select('maChinhSach', 'soSachToiDa', 'soNgayMuon')
                    ->where('loaiDocGia', $docGia->loaiDocGia)
                    ->where('trangThai', 'dangApDung')
                    ->orderByDesc('ngayApDung')
                    ->first();

                if (!$chinhSach) throw new \Exception('Chưa có chính sách mượn phù hợp.');

                $dangMuon = DB::table('phieumuon as pm')
                    ->join('chitietphieumuon as ct', 'ct.maPhieuMuon', '=', 'pm.maPhieuMuon')
                    ->where('pm.maDocGia', $maDocGia)
                    ->whereNull('ct.ngayTra')
                    ->count();

                if ($dangMuon + count($taiLieu) > $chinhSach->soSachToiDa) {
                    throw new \Exception('Độc giả đã vượt số lượng tài liệu được phép mượn.');
                }

                $maPhieuMuon = DB::table('phieumuon')->insertGetId([
                    'maDocGia' => $maDocGia,
                    'maThuThu' => $thuThu['id'],
                    'maChinhSach' => $chinhSach->maChinhSach,
                    'ngayMuon' => now(),
                    'trangThai' => 'dangMuon',
                ]);

                foreach ($taiLieu as $tlId) {
                    $updated = DB::table('tailieu')
                        ->where('maTaiLieu', $tlId)
                        ->where('soLuongCon', '>', 0)
                        ->decrement('soLuongCon');

                    if (!$updated) throw new \Exception('Có tài liệu không còn sẵn sàng.');

                    DB::table('chitietphieumuon')->insert([
                        'maPhieuMuon' => $maPhieuMuon,
                        'maTaiLieu' => $tlId,
                        'hanTra' => now()->addDays($chinhSach->soNgayMuon)->toDateString(),
                        'trangThai' => 'dangMuon',
                    ]);
                }

                DB::commit();
                return response()->json(['thanhCong' => true, 'maPhieuMuon' => $maPhieuMuon]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => $e->getMessage() ?: 'Không thể lập phiếu mượn.'], 422);
            }
        }

        if ($hanhDong === 'xacNhanTra') {
            $chiTiet = array_values(array_unique(array_map('intval', (array)$request->input('maChiTietPhieuMuon', []))));
            $tinhTrangKhiTra = $request->input('tinhTrangKhiTra', 'nguyenVen');

            if (empty($chiTiet)) return response()->json(['loi' => 'Chưa chọn tài liệu cần trả.'], 422);
            if (!in_array($tinhTrangKhiTra, ['nguyenVen', 'huHongNhe', 'huHongNang'], true)) {
                return response()->json(['loi' => 'Tình trạng tài liệu không hợp lệ.'], 422);
            }

            try {
                DB::beginTransaction();
                $phi = 0;
                $phieuMuonIds = [];

                foreach ($chiTiet as $maChiTiet) {
                    $muc = DB::table('chitietphieumuon as ct')
                        ->join('phieumuon as pm', 'pm.maPhieuMuon', '=', 'ct.maPhieuMuon')
                        ->select('ct.maPhieuMuon', 'ct.maTaiLieu', 'ct.hanTra', 'pm.maDocGia', 'pm.maChinhSach')
                        ->where('ct.maChiTietPhieuMuon', $maChiTiet)
                        ->whereNull('ct.ngayTra')
                        ->lockForUpdate()
                        ->first();

                    if (!$muc) throw new \Exception('Có tài liệu không còn ở trạng thái đang mượn.');

                    $phieuMuonIds[] = $muc->maPhieuMuon;

                    DB::table('chitietphieumuon')->where('maChiTietPhieuMuon', $maChiTiet)->update([
                        'ngayTra' => now(),
                        'tinhTrangKhiTra' => $tinhTrangKhiTra,
                        'trangThai' => 'daTra',
                    ]);

                    DB::table('tailieu')->where('maTaiLieu', $muc->maTaiLieu)->increment('soLuongCon');

                    $mucPhat = DB::table('chinhsachmuon')->where('maChinhSach', $muc->maChinhSach)->first();

                    // Qua han
                    $hanTraDate = \Carbon\Carbon::parse($muc->hanTra);
                    $ngayQuaHan = max(0, now()->diffInDays($hanTraDate, false) < 0 ? abs(now()->diffInDays($hanTraDate, false)) : 0);
                    $soTienQuaHan = $ngayQuaHan * (float)($mucPhat->tienPhatMoiNgay ?? 0);

                    if ($soTienQuaHan > 0) {
                        DB::table('phieuphat')->insert([
                            'maDocGia' => $muc->maDocGia,
                            'maChiTietPhieuMuon' => $maChiTiet,
                            'loaiPhat' => 'quaHan',
                            'soTien' => $soTienQuaHan,
                            'trangThai' => 'chuaThanhToan',
                            'ngayLap' => now(),
                        ]);
                        $phi += $soTienQuaHan;
                    }

                    // Hu hong
                    $soTienHuHong = (float)($tinhTrangKhiTra === 'huHongNhe' ? ($mucPhat->tienPhatHuHongNhe ?? 0) : ($tinhTrangKhiTra === 'huHongNang' ? ($mucPhat->tienPhatHuHongNang ?? 0) : 0));
                    if ($soTienHuHong > 0) {
                        DB::table('phieuphat')->insert([
                            'maDocGia' => $muc->maDocGia,
                            'maChiTietPhieuMuon' => $maChiTiet,
                            'loaiPhat' => $tinhTrangKhiTra,
                            'soTien' => $soTienHuHong,
                            'trangThai' => 'chuaThanhToan',
                            'ngayLap' => now(),
                        ]);
                        $phi += $soTienHuHong;
                    }
                }

                // Cập nhật trạng thái phiếu mượn nếu tất cả chi tiết đã trả
                foreach (array_unique($phieuMuonIds) as $pmId) {
                    $chuaTra = DB::table('chitietphieumuon')
                        ->where('maPhieuMuon', $pmId)
                        ->whereNull('ngayTra')
                        ->exists();

                    if (!$chuaTra) {
                        DB::table('phieumuon')->where('maPhieuMuon', $pmId)->update(['trangThai' => 'daTra']);
                    }
                }

                DB::commit();
                return response()->json(['thanhCong' => true, 'phi' => $phi]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => $e->getMessage() ?: 'Không thể xác nhận trả sách.'], 422);
            }
        }

        if ($hanhDong === 'xuLyGiaHan') {
            $maYeuCau = (int)$request->input('maYeuCauGiaHan', 0);
            $trangThai = $request->input('trangThai', '');

            if (!$maYeuCau || !in_array($trangThai, ['daDuyet', 'tuChoi'], true)) {
                return response()->json(['loi' => 'Yêu cầu gia hạn không hợp lệ.'], 422);
            }

            try {
                DB::beginTransaction();

                $yeuCau = DB::table('yeucaugiahan')
                    ->where('maYeuCauGiaHan', $maYeuCau)
                    ->where('trangThai', 'choDuyet')
                    ->lockForUpdate()
                    ->first();

                if (!$yeuCau) throw new \Exception('Yêu cầu đã được xử lý.');

                if ($trangThai === 'daDuyet') {
                    $updated = DB::table('chitietphieumuon')
                        ->where('maChiTietPhieuMuon', $yeuCau->maChiTietPhieuMuon)
                        ->whereNull('ngayTra')
                        ->update(['hanTra' => $yeuCau->hanTraMoi]);

                    if (!$updated) throw new \Exception('Tài liệu đã được trả.');
                }

                DB::table('yeucaugiahan')->where('maYeuCauGiaHan', $maYeuCau)->update(['trangThai' => $trangThai]);

                DB::commit();
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => $e->getMessage() ?: 'Không thể xử lý yêu cầu gia hạn.'], 422);
            }
        }

        if ($hanhDong === 'xuLyYeuCau') {
            $trangThai = $request->input('trangThai', '');
            $maYeuCau = (int)$request->input('maYeuCauMuon', 0);

            if (!in_array($trangThai, ['daDuyet', 'tuChoi'], true) || !$maYeuCau) {
                return response()->json(['loi' => 'Yêu cầu không hợp lệ.'], 422);
            }

            try {
                DB::beginTransaction();

                $yeuCau = DB::table('yeucaumuon as yc')
                    ->join('hosodocgia as dg', 'dg.maDocGia', '=', 'yc.maDocGia')
                    ->join('nguoidung as nd', 'nd.maNguoiDung', '=', 'dg.maNguoiDung')
                    ->select('yc.maDocGia', 'dg.loaiDocGia', 'dg.trangThaiThe', 'nd.trangThai')
                    ->where('yc.maYeuCauMuon', $maYeuCau)
                    ->where('yc.trangThai', 'choDuyet')
                    ->lockForUpdate()
                    ->first();

                if (!$yeuCau) throw new \Exception('Yêu cầu đã được xử lý.');

                if ($trangThai === 'tuChoi') {
                    DB::table('yeucaumuon')->where('maYeuCauMuon', $maYeuCau)->update([
                        'trangThai' => 'tuChoi',
                        'maNguoiXuLy' => $thuThu['id'],
                        'ngayXuLy' => now(),
                        'lyDoTuChoi' => trim($request->input('lyDoTuChoi', '')),
                    ]);
                    DB::commit();
                    return response()->json(['thanhCong' => true]);
                }

                if ($yeuCau->trangThai !== 'hoatDong' || $yeuCau->trangThaiThe !== 'hoatDong') {
                    throw new \Exception('Thẻ hoặc tài khoản độc giả đang không hoạt động.');
                }

                $chinhSach = DB::table('chinhsachmuon')
                    ->select('maChinhSach', 'soNgayMuon')
                    ->where('loaiDocGia', $yeuCau->loaiDocGia)
                    ->where('trangThai', 'dangApDung')
                    ->orderByDesc('ngayApDung')
                    ->first();

                if (!$chinhSach) throw new \Exception('Chưa có chính sách mượn phù hợp.');

                $maPhieuMuon = DB::table('phieumuon')->insertGetId([
                    'maDocGia' => $yeuCau->maDocGia,
                    'maThuThu' => $thuThu['id'],
                    'maChinhSach' => $chinhSach->maChinhSach,
                    'maYeuCauMuon' => $maYeuCau,
                    'ngayMuon' => now(),
                    'trangThai' => 'dangMuon',
                ]);

                $taiLieuIds = DB::table('chitietyeucaumuon')->where('maYeuCauMuon', $maYeuCau)->pluck('maTaiLieu');

                foreach ($taiLieuIds as $tlId) {
                    $updated = DB::table('tailieu')
                        ->where('maTaiLieu', $tlId)
                        ->where('soLuongCon', '>', 0)
                        ->decrement('soLuongCon');

                    if (!$updated) throw new \Exception('Có tài liệu không còn sẵn sàng.');

                    DB::table('chitietphieumuon')->insert([
                        'maPhieuMuon' => $maPhieuMuon,
                        'maTaiLieu' => $tlId,
                        'hanTra' => now()->addDays($chinhSach->soNgayMuon)->toDateString(),
                        'trangThai' => 'dangMuon',
                    ]);
                }

                DB::table('yeucaumuon')->where('maYeuCauMuon', $maYeuCau)->update([
                    'trangThai' => 'daDuyet',
                    'maNguoiXuLy' => $thuThu['id'],
                    'ngayXuLy' => now(),
                ]);

                DB::commit();
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => $e->getMessage() ?: 'Không thể duyệt yêu cầu.'], 422);
            }
        }

        if ($hanhDong === 'capNhatThe') {
            $maDocGia = (int)$request->input('maDocGia', 0);
            $trangThai = $request->input('trangThaiThe', '');

            if (!in_array($trangThai, ['hoatDong', 'tamKhoa'], true)) {
                return response()->json(['loi' => 'Trạng thái không hợp lệ.'], 422);
            }

            $updated = DB::table('hosodocgia')->where('maDocGia', $maDocGia)->update(['trangThaiThe' => $trangThai]);
            return response()->json(['thanhCong' => $updated > 0]);
        }

        if ($hanhDong === 'thuPhat') {
            $maPhieuPhat = (int)$request->input('maPhieuPhat', 0);
            $updated = DB::table('phieuphat')
                ->where('maPhieuPhat', $maPhieuPhat)
                ->where('trangThai', 'chuaThanhToan')
                ->update([
                    'trangThai' => 'daThanhToan',
                    'maNguoiThu' => $thuThu['id'],
                    'ngayThanhToan' => now(),
                ]);

            return response()->json(['thanhCong' => $updated > 0]);
        }

        return response()->json(['loi' => 'Thao tác không hợp lệ.'], 404);
    }
}