<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\NguoiDung;
use App\Models\HoSoDocGia;
use App\Models\VaiTro;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('library_user')) {
            return redirect()->route('home');
        }
        return view('login');
    }

    public function showRegister()
    {
        return view('dangKy');
    }

    public function showQuickLogin()
    {
        return view('dangNhapNhanh');
    }

    public function login(Request $request)
    {
        $tenDangNhap = strtoupper(trim($request->input('username', '')));
        $matKhau = (string)$request->input('password', '');

        if (!$tenDangNhap || !$matKhau) {
            return response()->json(['loi' => 'Vui lòng nhập mã đăng nhập và mật khẩu.'], 422);
        }

        $nguoiDung = DB::table('nguoidung as nd')
            ->join('vaitro as vt', 'vt.maVaiTro', '=', 'nd.maVaiTro')
            ->select('nd.maNguoiDung', 'nd.tenDangNhap', 'nd.matKhau', 'nd.hoTen', 'nd.thuDienTu', 'nd.soDienThoai', 'nd.ngaySinh', 'nd.trangThai', 'vt.ma', 'vt.ten')
            ->where('nd.tenDangNhap', $tenDangNhap)
            ->first();

        if (!$nguoiDung || !$nguoiDung->matKhau || !Hash::check($matKhau, $nguoiDung->matKhau)) {
            return response()->json(['loi' => 'Mã đăng nhập hoặc mật khẩu chưa đúng.'], 401);
        }

        if ($nguoiDung->trangThai === 'tamKhoa') {
            return response()->json(['loi' => 'Tài khoản của bạn đang bị tạm khóa. Vui lòng liên hệ thủ thư để được hỗ trợ.'], 403);
        }

        if ($nguoiDung->trangThai !== 'hoatDong') {
            return response()->json(['loi' => 'Tài khoản của bạn chưa được cấp quyền đăng nhập. Vui lòng liên hệ quản trị viên.'], 403);
        }

        $request->session()->regenerate();

        $vaiTroMap = ['DOCGIA' => 'reader', 'THUTHU' => 'librarian', 'ADMIN' => 'admin'];
        $vaiTro = $vaiTroMap[$nguoiDung->ma] ?? '';

        $initials = mb_strtoupper(mb_substr($nguoiDung->hoTen, 0, 1));

        session([
            'library_user' => [
                'id' => (int)$nguoiDung->maNguoiDung,
                'username' => $nguoiDung->tenDangNhap,
                'name' => $nguoiDung->hoTen,
                'initials' => $initials,
                'role_name' => $nguoiDung->ten,
                'email' => $nguoiDung->thuDienTu,
                'phone' => $nguoiDung->soDienThoai,
                'birth_date' => $nguoiDung->ngaySinh,
            ],
            'role' => $vaiTro
        ]);

        return response()->json(['thanhCong' => true, 'chuyenTrang' => 'index.php']);
    }

    public function register(Request $request)
    {
        $maSo = strtoupper(trim($request->input('code', '')));
        $hoTen = trim($request->input('name', ''));
        $email = trim($request->input('email', ''));
        $dienThoai = trim($request->input('phone', ''));
        $ngaySinh = $request->input('birth_date');
        $matKhau = (string)$request->input('password', '');

        if (!preg_match('/^[A-Z0-9]{5,16}$/', $maSo) || mb_strlen($hoTen) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($matKhau) < 8) {
            return response()->json(['loi' => 'Thông tin đăng ký chưa hợp lệ; mật khẩu cần ít nhất 8 ký tự.'], 422);
        }

        try {
            DB::beginTransaction();
            $vaiTro = DB::table('vaitro')->where('ma', 'DOCGIA')->value('maVaiTro');

            $maNguoiDung = DB::table('nguoidung')->insertGetId([
                'maVaiTro' => $vaiTro,
                'tenDangNhap' => $maSo,
                'matKhau' => Hash::make($matKhau),
                'hoTen' => $hoTen,
                'thuDienTu' => $email,
                'soDienThoai' => $dienThoai,
                'ngaySinh' => $ngaySinh ?: null,
                'trangThai' => 'hoatDong',
                'ngayTao' => now(),
            ]);

            DB::table('hosodocgia')->insert([
                'maNguoiDung' => $maNguoiDung,
                'maSo' => $maSo,
                'loaiDocGia' => 'sinhVien',
                'trangThaiThe' => 'hoatDong',
            ]);

            DB::commit();
            return response()->json(['thanhCong' => true, 'maSo' => $maSo]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['loi' => 'Mã số hoặc email đã tồn tại.'], 409);
        }
    }

    public function quickLogin(Request $request)
    {
        $vaiTroChon = $request->input('vaiTro', '');
        $maVaiTroMap = ['docGia' => 'DOCGIA', 'thuThu' => 'THUTHU', 'quanTri' => 'ADMIN'];
        $maVaiTro = $maVaiTroMap[$vaiTroChon] ?? '';

        if (!$maVaiTro) {
            return redirect()->back()->with('loi', 'Vai trò không hợp lệ.');
        }

        $query = DB::table('nguoidung as nd')
            ->join('vaitro as vt', 'vt.maVaiTro', '=', 'nd.maVaiTro')
            ->select('nd.maNguoiDung', 'nd.tenDangNhap', 'nd.hoTen', 'nd.thuDienTu', 'nd.soDienThoai', 'nd.ngaySinh', 'vt.ten')
            ->where('vt.ma', $maVaiTro)
            ->where('nd.trangThai', 'hoatDong')
            ->orderBy('nd.maNguoiDung');

        if ($maVaiTro === 'DOCGIA') {
            $query->join('hosodocgia as hs', 'hs.maNguoiDung', '=', 'nd.maNguoiDung');
        }

        $nguoiDung = $query->first();

        if ($nguoiDung) {
            $initials = mb_strtoupper(mb_substr($nguoiDung->hoTen, 0, 1));
            $roleCode = ['DOCGIA' => 'reader', 'THUTHU' => 'librarian', 'ADMIN' => 'admin'][$maVaiTro];

            session([
                'library_user' => [
                    'id' => (int)$nguoiDung->maNguoiDung,
                    'username' => $nguoiDung->tenDangNhap,
                    'name' => $nguoiDung->hoTen,
                    'initials' => $initials,
                    'role_name' => $nguoiDung->ten,
                    'email' => $nguoiDung->thuDienTu,
                    'phone' => $nguoiDung->soDienThoai,
                    'birth_date' => $nguoiDung->ngaySinh,
                ],
                'role' => $roleCode
            ]);
            return redirect()->route('home');
        }

        return redirect()->back()->with('loi', 'Chưa có tài khoản hoạt động phù hợp trong CSDL.');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}