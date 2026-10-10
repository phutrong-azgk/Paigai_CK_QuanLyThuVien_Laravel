<?php
$maTrang = $maTrang ?? '';
if ($maTrang === 'admin-dashboard'):
?>
  <div class="quan-tri-chi-so">
    <article><i class="xanh">♙</i>
      <div><b id="chi-so-tai-khoan">—</b><span>Tài khoản hệ thống</span><small id="ghi-chu-tai-khoan">Đang tải dữ liệu</small></div>
    </article>
    <article><i class="tim">◉</i>
      <div><b id="chi-so-tai-lieu">—</b><span>Đầu tài liệu</span><small id="chi-so-danh-muc">Đang tải</small></div>
    </article>
    <article><i class="cam">◷</i>
      <div><b id="chi-so-yeu-cau">—</b><span>Yêu cầu chờ duyệt</span><small id="ghi-chu-yeu-cau">Đang tải dữ liệu</small></div>
    </article>
    <article><i class="do">!</i>
      <div><b id="chi-so-can-xu-ly">—</b><span>Tài khoản tạm khóa</span><small id="ghi-chu-can-xu-ly">Đang tải dữ liệu</small></div>
    </article>
  </div>
  <div class="quan-tri-luoi">
    <section class="panel">
      <div class="section-head">
        <div>
          <p class="eyebrow">HOẠT ĐỘNG HỆ THỐNG</p>
          <h2>Nhật ký gần đây</h2>
        </div>
      </div>
      <div id="nhat-ky-quan-tri" class="nhat-ky-quan-tri">
        <p>Đang tải nhật ký...</p>
      </div>
    </section>
    <section class="panel canh-bao-quan-tri">
      <p class="eyebrow">CẦN LƯU Ý</p>
      <h2>Kiểm tra trước khi kết thúc ngày</h2>
      <div id="danh-sach-canh-bao-quan-tri"><p>Đang tải cảnh báo...</p></div>
    </section>
  </div>
<?php elseif ($maTrang === 'accounts'): ?>
  <section class="panel quan-tri-bang">
    <div class="thanh-cong-cu-quan-tri"><label><span>⌕</span><input id="tim-tai-khoan" placeholder="Tìm theo mã, họ tên hoặc email..."></label><select id="loc-vai-tro">
        <option value="">Tất cả vai trò</option>
        <option value="ADMIN">Quản trị viên</option>
        <option value="THUTHU">Thủ thư</option>
        <option value="DOCGIA">Độc giả</option>
      </select><button id="mo-tao-tai-khoan" class="primary" type="button">+ Tạo tài khoản</button></div>
    <div class="tieu-de-bang-quan-tri"><b>Danh sách tài khoản</b><span id="tong-tai-khoan">Đang tải dữ liệu...</span></div>
    <div class="table-panel">
      <table>
        <thead>
          <tr>
            <th>TÀI KHOẢN</th>
            <th>VAI TRÒ</th>
            <th>LIÊN HỆ</th>
            <th>TRẠNG THÁI</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="du-lieu-tai-khoan">
          <tr>
            <td colspan="5">Đang tải danh sách tài khoản...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <div id="hop-tai-khoan-quan-tri" class="hop-tai-khoan-quan-tri" aria-hidden="true">
    <section class="noi-dung-tai-khoan-quan-tri" role="dialog" aria-modal="true" aria-labelledby="tieu-de-hop-tai-khoan">
      <div class="dau-hop-tai-khoan">
        <div>
          <p class="eyebrow" id="nhan-hop-tai-khoan">QUẢN LÝ TÀI KHOẢN</p>
          <h2 id="tieu-de-hop-tai-khoan">Tạo tài khoản</h2>
        </div><button class="dong-hop-tai-khoan" type="button" aria-label="Đóng">×</button>
      </div>
      <p id="mo-ta-hop-tai-khoan">Tạo tài khoản mới cho độc giả hoặc thủ thư. Mã đăng nhập là mã số do quản trị viên cấp.</p>
      <form id="form-tai-khoan-quan-tri">
        <input type="hidden" name="hanhDong" value="tao"><input type="hidden" name="maNguoiDung" value="">
        <div class="luoi-truong-tai-khoan" id="truong-tao-tai-khoan">
          <label>Vai trò<select name="maVaiTro">
              <option value="DOCGIA">Độc giả</option>
              <option value="THUTHU">Thủ thư</option>
            </select></label>
          <label id="truong-loai-doc-gia">Loại độc giả<select name="loaiDocGia">
              <option value="sinhVien">Sinh viên</option>
              <option value="giangVien">Giảng viên</option>
            </select></label>
          <label>Mã đăng nhập<input name="tenDangNhap" placeholder="Ví dụ: 22110456 hoặc TT001" maxlength="30"></label>
          <label class="rong">Họ và tên<input name="hoTen" placeholder="Nhập họ và tên"></label>
          <label class="rong">Email<input name="thuDienTu" type="email" placeholder="ten@truong.edu.vn"></label>
        </div>
        <label id="truong-mat-khau">Mật khẩu <input name="matKhau" type="password" minlength="8" placeholder="Tối thiểu 8 ký tự" autocomplete="new-password"></label>
        <p id="loi-tai-khoan-quan-tri" class="loi-tai-khoan-quan-tri" hidden></p>
        <div class="chan-hop-tai-khoan"><button class="outline dong-hop-tai-khoan" type="button">Hủy</button><button id="nut-luu-tai-khoan" class="primary" type="submit">Tạo tài khoản <span>→</span></button></div>
      </form>
    </section>
  </div>
<?php elseif ($maTrang === 'categories'): ?>
  <section class="panel bieu-mau-chinh-sach">
    <div class="section-head">
      <div>
        <p class="eyebrow">KHOA</p>
        <h2 id="tieu-de-form-danh-muc">Thêm khoa</h2>
      </div>
    </div>
    <form id="form-danh-muc"><input name="maDanhMuc" type="hidden">
      <div class="luoi-truong-chinh-sach"><label>Mã khoa<input name="ma" placeholder="Ví dụ: CNTT"></label><label>Tên khoa<input name="ten" placeholder="Ví dụ: Công nghệ thông tin"></label></div><button class="primary" type="submit">Lưu khoa <span>→</span></button><button id="huy-sua-danh-muc" class="outline" type="button" hidden>Hủy sửa</button>
    </form>
  </section>
  <div id="danh-sach-danh-muc" class="luoi-danh-muc-quan-tri">
    <p>Đang tải khoa...</p>
  </div>
<?php elseif ($maTrang === 'policies'): ?>
  <div class="chinh-sach-quan-tri">
    <section class="panel bieu-mau-chinh-sach">
      <div class="section-head">
        <div>
          <p class="eyebrow">QUY ĐỊNH MƯỢN</p>
          <h2>Chính sách dành cho độc giả</h2>
        </div><mark class="green-mark">Lưu phiên bản mới</mark>
      </div>
      <form id="form-chinh-sach">
        <div class="luoi-truong-chinh-sach"><label>Nhóm độc giả<select name="loaiDocGia">
              <option value="sinhVien">Sinh viên</option>
              <option value="giangVien">Giảng viên</option>
            </select></label><label>Số sách tối đa<input name="soSachToiDa" type="number" min="1"></label><label>Thời hạn mượn (ngày)<input name="soNgayMuon" type="number" min="1"></label><label>Số lần gia hạn<input name="soLanGiaHan" type="number" min="0"></label><label>Số ngày mỗi lần gia hạn<input name="soNgayGiaHan" type="number" min="1"></label><label>Mức phạt quá hạn / ngày<input name="tienPhatMoiNgay" type="text" inputmode="numeric" data-tien placeholder="0"></label></div>
        <div class="khung-phat-hu-hong">
          <div><p class="eyebrow">PHẠT HƯ HỎNG TÀI LIỆU</p><h3>Mức phạt khi tiếp nhận trả</h3><p>Hệ thống lập phiếu phạt tự động khi thủ thư ghi nhận tài liệu hư hỏng.</p></div>
          <div class="luoi-truong-chinh-sach"><label>Hư hỏng nhẹ<input name="tienPhatHuHongNhe" type="text" inputmode="numeric" data-tien placeholder="0"></label><label>Hư hỏng nặng<input name="tienPhatHuHongNang" type="text" inputmode="numeric" data-tien placeholder="0"></label></div>
        </div><button class="primary" type="submit">Lưu chính sách <span>→</span></button>
      </form>
    </section>
    <section class="panel lich-su-chinh-sach">
      <p class="eyebrow">LỊCH SỬ CẬP NHẬT</p>
      <h2>Các phiên bản chính sách</h2>
      <div id="danh-sach-chinh-sach">Đang tải chính sách...</div>
    </section>
  </div>
<?php elseif ($maTrang === 'admin-password'): ?>
  <section class="panel bieu-mau-chinh-sach form-doi-mat-khau">
    <div class="section-head">
      <div>
        <p class="eyebrow">BẢO MẬT TÀI KHOẢN</p>
        <h2>Đổi mật khẩu của tôi</h2>
        <p>Nhập mật khẩu hiện tại trước khi đặt mật khẩu mới cho tài khoản quản trị.</p>
      </div>
    </div>
    <form id="form-doi-mat-khau-admin">
      <div class="luoi-truong-chinh-sach"><label>Mật khẩu hiện tại<input name="matKhauHienTai" type="password" minlength="8" required autocomplete="current-password"></label><label>Mật khẩu mới<input name="matKhauMoi" type="password" minlength="8" required autocomplete="new-password"></label><label>Xác nhận mật khẩu mới<input name="xacNhanMatKhau" type="password" minlength="8" required autocomplete="new-password"></label></div>
      <p id="loi-doi-mat-khau-admin" class="loi-tai-khoan-quan-tri" hidden></p><button class="primary" type="submit">Lưu mật khẩu mới <span>→</span></button>
    </form>
  </section>
<?php endif; ?>
