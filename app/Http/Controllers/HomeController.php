<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        if (!session('library_user')) {
            return redirect()->route('login');
        }

        $nguoiDungHienTai = session('library_user');
        $vaiTroHienTai = session('role');

        $taiLieuDB = DB::table('tailieu as tl')
            ->leftJoin('khoa as k', 'k.maKhoa', '=', 'tl.maKhoa')
            ->select('tl.ma as code', 'tl.tieuDe as title', 'k.ten as category', 'tl.soLuongCon as available')
            ->limit(12)
            ->get();

        $colors = ['blue', 'purple', 'green', 'orange'];
        $icons = ['◫', '◉', '⌘', '₫', '↗', '§', '⚖', '✚', '✥', 'A', '✎'];
        $danhSachTaiLieu = [];

        if ($taiLieuDB->isNotEmpty()) {
            foreach ($taiLieuDB as $i => $tl) {
                $danhSachTaiLieu[] = [
                    'title' => $tl->title,
                    'category' => $tl->category ?: 'Chung',
                    'code' => $tl->code,
                    'available' => (int)$tl->available,
                    'color' => $colors[$i % count($colors)],
                    'icon' => $icons[$i % count($icons)],
                ];
            }
        } else {
            $danhSachTaiLieu = [
                ['title' => 'Cơ Sở Dữ Liệu', 'category' => 'Công nghệ thông tin', 'code' => 'CNTT-101', 'available' => 8, 'color' => 'blue', 'icon' => '◫'],
                ['title' => 'Nhập Môn Trí Tuệ Nhân Tạo', 'category' => 'Công nghệ thông tin', 'code' => 'CNTT-145', 'available' => 5, 'color' => 'purple', 'icon' => '◉'],
                ['title' => 'Lập Trình Hướng Đối Tượng Với Java', 'category' => 'Công nghệ thông tin', 'code' => 'CNTT-128', 'available' => 3, 'color' => 'green', 'icon' => '⌘'],
                ['title' => 'Nguyên Lý Kế Toán', 'category' => 'Kinh tế', 'code' => 'KT-021', 'available' => 6, 'color' => 'orange', 'icon' => '₫'],
                ['title' => 'Kinh Tế Vi Mô', 'category' => 'Kinh tế', 'code' => 'KT-044', 'available' => 2, 'color' => 'green', 'icon' => '↗'],
                ['title' => 'Giáo Trình Luật Dân Sự Việt Nam', 'category' => 'Luật', 'code' => 'LUAT-032', 'available' => 4, 'color' => 'purple', 'icon' => '§'],
                ['title' => 'Pháp Luật Đại Cương', 'category' => 'Luật', 'code' => 'LUAT-008', 'available' => 0, 'color' => 'blue', 'icon' => '⚖'],
                ['title' => 'Giải Phẫu Người', 'category' => 'Y dược', 'code' => 'YD-015', 'available' => 3, 'color' => 'orange', 'icon' => '✚'],
                ['title' => 'Sinh Lý Học Y Khoa', 'category' => 'Y dược', 'code' => 'YD-027', 'available' => 1, 'color' => 'green', 'icon' => '✥'],
                ['title' => 'English for Academic Purposes', 'category' => 'Ngoại ngữ', 'code' => 'NNA-019', 'available' => 7, 'color' => 'blue', 'icon' => 'A'],
                ['title' => 'Academic Writing for Students', 'category' => 'Ngoại ngữ', 'code' => 'NNA-033', 'available' => 2, 'color' => 'purple', 'icon' => '✎'],
            ];
        }

        return view('index', compact('nguoiDungHienTai', 'vaiTroHienTai', 'danhSachTaiLieu'));
    }

    public function chiTietTaiLieu(Request $request)
    {
        $ma = $request->input('ma');
        $taiLieu = null;
        if ($ma) {
            $taiLieu = DB::table('tailieu as tl')
                ->leftJoin('khoa as k', 'k.maKhoa', '=', 'tl.maKhoa')
                ->leftJoin('nhaxuatban as nxb', 'nxb.maNhaXuatBan', '=', 'tl.maNhaXuatBan')
                ->select('tl.*', 'k.ten as khoa', 'nxb.ten as nhaXuatBan')
                ->where('tl.ma', $ma)
                ->first();
        }

        return view('chiTietTaiLieu', compact('taiLieu'));
    }
}