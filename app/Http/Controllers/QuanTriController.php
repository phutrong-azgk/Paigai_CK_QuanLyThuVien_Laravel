<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class QuanTriController extends Controller
{
    private function kiemTraAdmin()
    {
        $user = session('library_user');
        if (!$user || session('role') !== 'admin') {
            abort(response()->json(['loi' => 'Bạn không có quyền thực hiện thao tác này.'], 403));
        }
        return $user;
    }

    private function ghiNhatKy($maNguoiDung, $hanhDong, $doiTuong, $maDoiTuong)
    {
        DB::table('nhatkyhethong')->insert([
            'maNguoiDung' => $maNguoiDung,
            'hanhDong' => $hanhDong,
            'doiTuong' => $doiTuong,
            'maDoiTuong' => $maDoiTuong,
            'ngayTao' => now(),
        ]);
    }

    public function handleTaiKhoan(Request $request)
    {
        $admin = $this->kiemTraAdmin();

        if ($request->isMethod('GET')) {
            $duLieu = DB::table('nguoidung as nd')
                ->join('vaitro as vt', 'vt.maVaiTro', '=', 'nd.maVaiTro')
                ->select('nd.maNguoiDung', 'nd.tenDangNhap', 'nd.hoTen', 'nd.thuDienTu', 'nd.trangThai', 'vt.ma as maVaiTro', 'vt.ten as vaiTro')
                ->where('vt.ma', '<>', 'ADMIN')
                ->orderByDesc('nd.maNguoiDung')
                ->get();
            return response()->json(['duLieu' => $duLieu]);
        }

        $hanhDong = $request->input('hanhDong', '');

        if ($hanhDong === 'doiMatKhauCuaToi') {
            $matKhauHienTai = (string)$request->input('matKhauHienTai', '');
            $matKhauMoi = (string)$request->input('matKhauMoi', '');

            if (strlen($matKhauMoi) < 8) return response()->json(['loi' => 'Mật khẩu mới cần ít nhất 8 ký tự.'], 422);

            $userDB = DB::table('nguoidung')->where('maNguoiDung', $admin['id'])->first();
            if (!$userDB || !Hash::check($matKhauHienTai, $userDB->matKhau)) {
                return response()->json(['loi' => 'Mật khẩu hiện tại chưa đúng.'], 422);
            }

            DB::table('nguoidung')->where('maNguoiDung', $admin['id'])->update(['matKhau' => Hash::make($matKhauMoi)]);
            return response()->json(['thanhCong' => true]);
        }

        if ($hanhDong === 'tao') {
            $maVaiTro = strtoupper($request->input('maVaiTro', ''));
            $tenDangNhap = strtoupper(trim($request->input('tenDangNhap', '')));
            $hoTen = trim($request->input('hoTen', ''));
            $email = trim($request->input('thuDienTu', ''));
            $matKhau = (string)$request->input('matKhau', '');
            $loaiDocGia = $request->input('loaiDocGia', 'sinhVien');

            if (!in_array($maVaiTro, ['THUTHU', 'DOCGIA'], true) || !in_array($loaiDocGia, ['sinhVien', 'giangVien'], true) ||
                !preg_match('/^[A-Z0-9]{5,30}$/', $tenDangNhap) || mb_strlen($hoTen) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($matKhau) < 8) {
                return response()->json(['loi' => 'Thông tin tài khoản chưa hợp lệ.'], 422);
            }

            try {
                DB::beginTransaction();
                $vaiTroId = DB::table('vaitro')->where('ma', $maVaiTro)->value('maVaiTro');
                $maNguoiDung = DB::table('nguoidung')->insertGetId([
                    'maVaiTro' => $vaiTroId,
                    'tenDangNhap' => $tenDangNhap,
                    'matKhau' => Hash::make($matKhau),
                    'hoTen' => $hoTen,
                    'thuDienTu' => $email,
                    'trangThai' => 'hoatDong',
                    'ngayTao' => now(),
                ]);

                if ($maVaiTro === 'DOCGIA') {
                    DB::table('hosodocgia')->insert([
                        'maNguoiDung' => $maNguoiDung,
                        'maSo' => $tenDangNhap,
                        'loaiDocGia' => $loaiDocGia,
                        'trangThaiThe' => 'hoatDong',
                    ]);
                }

                DB::commit();
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => 'Mã đăng nhập hoặc email đã tồn tại.'], 409);
            }
        }

        if ($hanhDong === 'capLaiMatKhau') {
            $maNguoiDung = (int)$request->input('maNguoiDung', 0);
            $matKhau = (string)$request->input('matKhau', '');

            if (!$maNguoiDung || strlen($matKhau) < 8) return response()->json(['loi' => 'Mật khẩu cần ít nhất 8 ký tự.'], 422);

            $vaiTro = DB::table('nguoidung as nd')->join('vaitro as vt', 'vt.maVaiTro', '=', 'nd.maVaiTro')->where('nd.maNguoiDung', $maNguoiDung)->value('vt.ma');
            if ($vaiTro === 'ADMIN') return response()->json(['loi' => 'Quản trị viên phải tự đổi mật khẩu bằng mật khẩu hiện tại.'], 403);

            $updated = DB::table('nguoidung')->where('maNguoiDung', $maNguoiDung)->update(['matKhau' => Hash::make($matKhau)]);
            return response()->json(['thanhCong' => $updated > 0]);
        }

        if ($hanhDong === 'capNhatTrangThai') {
            $maNguoiDung = (int)$request->input('maNguoiDung', 0);
            $trangThai = $request->input('trangThai', '');

            if (!in_array($trangThai, ['hoatDong', 'tamKhoa'], true)) return response()->json(['loi' => 'Trạng thái không hợp lệ.'], 422);

            $updated = DB::table('nguoidung')->where('maNguoiDung', $maNguoiDung)->update(['trangThai' => $trangThai]);
            return response()->json(['thanhCong' => $updated > 0]);
        }

        return response()->json(['loi' => 'Thao tác không hợp lệ.'], 404);
    }

    public function handleHeThong(Request $request)
    {
        $admin = $this->kiemTraAdmin();

        if ($request->isMethod('GET')) {
            $loai = $request->input('loai', '');

            if ($loai === 'danhMuc') {
                $duLieu = DB::table('khoa as k')
                    ->leftJoin('tailieu as tl', 'tl.maKhoa', '=', 'k.maKhoa')
                    ->select('k.maKhoa as maDanhMuc', 'k.ma', 'k.ten', DB::raw('COUNT(tl.maTaiLieu) as soTaiLieu'))
                    ->groupBy('k.maKhoa')
                    ->orderBy('k.ten')
                    ->get();
                return response()->json(['duLieu' => $duLieu]);
            }

            if ($loai === 'chinhSach') {
                $duLieu = DB::table('chinhsachmuon')
                    ->orderBy('loaiDocGia')
                    ->orderByDesc('ngayApDung')
                    ->orderByDesc('maChinhSach')
                    ->get();
                return response()->json(['duLieu' => $duLieu]);
            }

            if ($loai === 'tongQuan') {
                $taiKhoan = DB::table('nguoidung')->count();
                $taiKhoanHoatDong = DB::table('nguoidung')->where('trangThai', 'hoatDong')->count();
                $taiLieu = DB::table('tailieu')->count();
                $danhMuc = DB::table('khoa')->count();
                $yeuCauChoDuyet = DB::table('yeucaumuon')->where('trangThai', 'choDuyet')->count();
                $taiKhoanTamKhoa = DB::table('nguoidung')->where('trangThai', 'tamKhoa')->count();
                $taiLieuHet = DB::table('tailieu')->where('soLuongCon', '<=', 0)->count();
                $chinhSachDangApDung = DB::table('chinhsachmuon')->where('trangThai', 'dangApDung')->pluck('loaiDocGia')->toArray();

                $canhBao = [];
                if ($yeuCauChoDuyet) $canhBao[] = ['soLuong' => $yeuCauChoDuyet, 'noiDung' => 'yêu cầu mượn đang chờ thủ thư duyệt.'];
                if ($taiKhoanTamKhoa) $canhBao[] = ['soLuong' => $taiKhoanTamKhoa, 'noiDung' => 'tài khoản đang tạm khóa và cần kiểm tra.'];
                if ($taiLieuHet) $canhBao[] = ['soLuong' => $taiLieuHet, 'noiDung' => 'đầu tài liệu hiện đã hết bản có thể mượn.'];

                foreach (['sinhVien' => 'sinh viên', 'giangVien' => 'giảng viên'] as $maLoai => $tenLoai) {
                    if (!in_array($maLoai, $chinhSachDangApDung, true)) {
                        $canhBao[] = ['soLuong' => '!', 'noiDung' => "chưa có chính sách mượn đang áp dụng cho $tenLoai."];
                    }
                }

                $canXuLy = $taiKhoanTamKhoa;
                $nhatKy = DB::table('nhatkyhethong as nk')
                    ->leftJoin('nguoidung as nd', 'nd.maNguoiDung', '=', 'nk.maNguoiDung')
                    ->select('nk.hanhDong', 'nk.doiTuong', 'nk.ngayTao', 'nd.hoTen')
                    ->orderByDesc('nk.ngayTao')
                    ->limit(5)
                    ->get();

                return response()->json([
                    'duLieu' => compact('taiKhoan', 'taiKhoanHoatDong', 'taiLieu', 'danhMuc', 'yeuCauChoDuyet', 'taiKhoanTamKhoa', 'canXuLy'),
                    'nhatKy' => $nhatKy,
                    'canhBao' => $canhBao,
                ]);
            }

            return response()->json(['loi' => 'Dữ liệu không hợp lệ.'], 404);
        }

        $hanhDong = $request->input('hanhDong', '');

        if ($hanhDong === 'luuDanhMuc') {
            $maDanhMuc = (int)$request->input('maDanhMuc', 0);
            $ma = strtoupper(trim($request->input('ma', '')));
            $ten = trim($request->input('ten', ''));

            if (!preg_match('/^[A-Z0-9-]{2,30}$/', $ma) || mb_strlen($ten) < 2) {
                return response()->json(['loi' => 'Mã và tên khoa chưa hợp lệ.'], 422);
            }

            try {
                if ($maDanhMuc) {
                    DB::table('khoa')->where('maKhoa', $maDanhMuc)->update(['ma' => $ma, 'ten' => $ten]);
                } else {
                    $maDanhMuc = DB::table('khoa')->insertGetId(['ma' => $ma, 'ten' => $ten]);
                }
                $this->ghiNhatKy($admin['id'], 'capNhatKhoa', 'khoa', $maDanhMuc);
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                return response()->json(['loi' => 'Mã khoa đã tồn tại.'], 409);
            }
        }

        if ($hanhDong === 'xoaDanhMuc') {
            try {
                $deleted = DB::table('khoa')->where('maKhoa', (int)$request->input('maDanhMuc', 0))->delete();
                if (!$deleted) return response()->json(['loi' => 'Không tìm thấy khoa.'], 404);
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                return response()->json(['loi' => 'Khoa đang có tài liệu hoặc hồ sơ độc giả nên không thể xóa.'], 409);
            }
        }

        if ($hanhDong === 'luuChinhSach') {
            $loaiDocGia = $request->input('loaiDocGia', '');
            $docTien = function ($giaTri) {
                $chuoi = trim((string)$giaTri);
                return preg_match('/^\d+\.\d{1,2}$/', $chuoi) ? (int)round((float)$chuoi) : (int)str_replace('.', '', $chuoi);
            };

            $soSach = (int)$request->input('soSachToiDa', 0);
            $soNgay = (int)$request->input('soNgayMuon', 0);
            $soLan = (int)$request->input('soLanGiaHan', 0);
            $soNgayGiaHan = (int)$request->input('soNgayGiaHan', 0);
            $tienPhat = $docTien($request->input('tienPhatMoiNgay', 0));
            $phatHuHongNhe = $docTien($request->input('tienPhatHuHongNhe', 0));
            $phatHuHongNang = $docTien($request->input('tienPhatHuHongNang', 0));

            if (!in_array($loaiDocGia, ['sinhVien', 'giangVien'], true) || $soSach < 1 || $soNgay < 1 || $soLan < 0 || $soNgayGiaHan < 1 || $tienPhat < 0 || $phatHuHongNhe < 0 || $phatHuHongNang < 0) {
                return response()->json(['loi' => 'Thông số chính sách chưa hợp lệ.'], 422);
            }

            try {
                DB::beginTransaction();
                DB::table('chinhsachmuon')->where('loaiDocGia', $loaiDocGia)->where('trangThai', 'dangApDung')->update(['trangThai' => 'ngungApDung']);

                $maChinhSach = DB::table('chinhsachmuon')->insertGetId([
                    'loaiDocGia' => $loaiDocGia,
                    'soSachToiDa' => $soSach,
                    'soNgayMuon' => $soNgay,
                    'soLanGiaHan' => $soLan,
                    'soNgayGiaHan' => $soNgayGiaHan,
                    'tienPhatMoiNgay' => $tienPhat,
                    'tienPhatHuHongNhe' => $phatHuHongNhe,
                    'tienPhatHuHongNang' => $phatHuHongNang,
                    'ngayApDung' => now()->toDateString(),
                    'trangThai' => 'dangApDung',
                ]);

                $this->ghiNhatKy($admin['id'], 'capNhatChinhSach', 'chinhSachMuon', $maChinhSach);
                DB::commit();
                return response()->json(['thanhCong' => true]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['loi' => 'Không thể lưu chính sách.'], 500);
            }
        }

        return response()->json(['loi' => 'Thao tác không hợp lệ.'], 404);
    }
}