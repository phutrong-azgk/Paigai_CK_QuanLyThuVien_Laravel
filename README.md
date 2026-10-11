# LIBRA — Hệ thống quản lý thư viện

LIBRA là ứng dụng web quản lý thư viện trường học, xây dựng bằng Laravel. Hệ thống hỗ trợ độc giả tra cứu tài liệu và gửi yêu cầu mượn; thủ thư xử lý mượn, trả, gia hạn và yêu cầu trực tuyến; quản trị viên quản lý tài khoản, danh mục và chính sách mượn.

## Chức năng chính

- Đăng ký, đăng nhập và phân quyền: độc giả, thủ thư, quản trị viên.
- Tra cứu tài liệu theo tên, mã, khoa hoặc nhà xuất bản.
- Hiển thị ảnh bìa, thông tin chi tiết và tình trạng còn sách.
- Gửi, hủy, duyệt hoặc từ chối yêu cầu mượn; lý do từ chối được lưu lại để độc giả theo dõi.
- Lập phiếu mượn, xác nhận trả sách, tính phí phạt và xử lý yêu cầu gia hạn.
- Quản lý tài liệu, nhà xuất bản, độc giả và chính sách mượn.

## Yêu cầu

- PHP 8.3 trở lên
- Composer
- MySQL 8 trở lên
- Node.js và npm (chỉ cần khi xây dựng lại tài nguyên giao diện)

## Cài đặt

1. Clone project và cài package PHP:

   ```powershell
   git clone <repository-url>
   cd Paigai_CK_QuanLyThuVien_Laravel
   composer install
   ```

2. Tạo file cấu hình môi trường và khóa ứng dụng:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Mở `.env` và cấu hình kết nối MySQL:

   ```env
   APP_NAME=LIBRA
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=thuvienlibra
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Import cấu trúc và dữ liệu database thư viện vào MySQL.

   > Lưu ý: các bảng nghiệp vụ như `nguoidung`, `hosodocgia`, `tailieu`, `yeucaumuon` và `phieumuon` chưa có migration đầy đủ trong repository. Vì vậy cần dùng file backup/database dump của dự án trước khi chạy ứng dụng.

5. Chạy ứng dụng:

   ```powershell
   php artisan serve
   ```

   Mở [http://127.0.0.1:8000](http://127.0.0.1:8000). Trang gốc sẽ chuyển đến màn hình đăng nhập.

## Chạy giao diện trong lúc phát triển

Các tài nguyên giao diện hiện nằm trong `public/assets`. Nếu cần cài hoặc xây dựng lại tài nguyên Vite, chạy:

```powershell
npm install
npm run build
```

## Cấu trúc đáng chú ý

```text
app/Http/Controllers/    Xử lý đăng nhập, độc giả, thủ thư và quản trị
app/Models/              Model các thực thể thư viện
public/assets/           JavaScript, CSS và ảnh bìa tài liệu
resources/views/         Giao diện Blade
routes/web.php           Route web và API nội bộ
```

## Kiểm tra

```powershell
php artisan test
```

## Bảo mật

Không commit file `.env`, dữ liệu đăng nhập, hoặc bản sao database có chứa dữ liệu thật.
