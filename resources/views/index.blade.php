<?php
$nguoiDungHienTai = $nguoiDungHienTai ?? session('library_user');
$vaiTroHienTai = $vaiTroHienTai ?? session('role');
$thuDienTuHoSo = $nguoiDungHienTai['email'] ?? 'thuan@example.com';
$soDienThoaiHoSo = $nguoiDungHienTai['phone'] ?? '0901 234 567';
$ngaySinhHoSo = $nguoiDungHienTai['birth_date'] ?? '2003-05-12';
$maHoSo = $nguoiDungHienTai['username'] ?? 'DG-2026-0145';
$danhSachTaiLieu = $danhSachTaiLieu ?? [];
?>
<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LIBRA | Quản lý thư viện</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/login.css">
    <link rel="stylesheet" href="assets/tuongTac.css">
    <link rel="stylesheet" href="assets/quanLySach.css?v=20261002-1">
    <link rel="stylesheet" href="assets/thuThu.css?v=20261002-1">
    <link rel="stylesheet" href="assets/yeuCauCho.css?v=20261010-1">
    <link rel="stylesheet" href="assets/chonTaiLieu.css">
    <link rel="stylesheet" href="assets/quanTri.css?v=20261004-3">
    <link rel="stylesheet" href="assets/yeuCauDocGia.css?v=20260921-1">
</head>

<body data-role="<?= htmlspecialchars($vaiTroHienTai) ?>">
    <aside class="sidebar">
        <a class="brand" href="#"><span class="brand-mark">L</span><span>LIBRA<small>THƯ VIỆN
                    SỐ</small></span></a>
        <?php if ($vaiTroHienTai === 'reader'): ?><nav id="reader-nav" class="nav-group">
            <p>KHÔNG GIAN ĐỘC GIẢ</p><a class="nav-link active" href="#dashboard" data-page="dashboard"><i>⌂</i> Tổng
                quan</a><a class="nav-link" href="#catalog" data-page="catalog"><i>⌕</i> Tìm kiếm sách</a><a
                class="nav-link" href="#loans" data-page="loans"><i>▣</i> Mượn & trả sách</a><a class="nav-link"
                href="#history" data-page="history"><i>◷</i> Lịch sử hoạt động</a><a class="nav-link"
                href="#reader-fines" data-page="reader-fines"><i>₫</i> Khoản phạt</a><a class="nav-link"
                href="#reader-pending" data-page="reader-pending"><i>◌</i> Yêu cầu chờ duyệt</a><a class="nav-link"
                href="#profile" data-page="profile"><i>♙</i> Hồ sơ cá nhân</a>
        </nav><?php endif; ?>
        <?php if ($vaiTroHienTai === 'librarian'): ?><nav id="staff-nav" class="nav-group">
            <p>QUẢN LÝ NGHIỆP VỤ</p><a class="nav-link active" href="#staff-dashboard"
                data-page="staff-dashboard"><i>⌂</i> Tổng quan</a><a class="nav-link" href="#book-admin"
                data-page="book-admin"><i>▤</i> Quản lý sách</a><a class="nav-link" href="#publishers"
                data-page="publishers"><i>▦</i> Nhà xuất bản</a><a class="nav-link" href="#borrow-admin"
                data-page="borrow-admin"><i>↔</i> Quản lý mượn sách</a><a class="nav-link" href="#return-admin"
                data-page="return-admin"><i>↵</i> Quản lý trả sách</a><a class="nav-link" href="#reader-admin"
                data-page="reader-admin"><i>♙</i> Hồ sơ độc giả</a><a class="nav-link" href="#renewal-admin"
                data-page="renewal-admin"><i>◷</i> Yêu cầu gia hạn</a><a class="nav-link" href="#fines"
                data-page="fines"><i>₫</i> Quản lý phạt</a>
        </nav><?php endif; ?>
        <?php if ($vaiTroHienTai === 'admin'): ?><nav id="admin-nav" class="nav-group">
            <p>QUẢN TRỊ HỆ THỐNG</p><a class="nav-link active" href="#admin-dashboard"
                data-page="admin-dashboard"><i>⌂</i> Tổng quan</a><a class="nav-link" href="#accounts"
                data-page="accounts"><i>♙</i> Quản lý tài khoản</a><a class="nav-link" href="#categories"
                data-page="categories"><i>▦</i> Quản lý khoa</a><a class="nav-link" href="#policies"
                data-page="policies"><i>◈</i> Chính sách thư viện</a><a class="nav-link" href="#admin-password"
                data-page="admin-password"><i>◉</i> Đổi mật khẩu</a>
        </nav><?php endif; ?>
        <div class="sidebar-user">
            <div class="avatar"><?= htmlspecialchars($nguoiDungHienTai['initials']) ?></div>
            <div>
                <b><?= htmlspecialchars($nguoiDungHienTai['name']) ?></b><span><?= htmlspecialchars($nguoiDungHienTai['role_name']) ?></span>
            </div>
            <form method="post" action="<?= route('logout') ?>" class="logout-form">@csrf<button class="logout"
                    type="submit" title="Đăng xuất" aria-label="Đăng xuất">⇥</button></form>
        </div>
    </aside>
    <main>
        <?php if ($vaiTroHienTai === 'reader'): ?>
        <section id="dashboard" class="page active">
            <div class="welcome">
                <div>
                    <p class="eyebrow">XIN CHÀO, <?= htmlspecialchars($nguoiDungHienTai['name'] ?? 'Độc giả') ?></p>
                    <h1>Thư viện luôn<br><em>rộng mở.</em></h1>
                    <p class="sub">Khám phá tri thức mới và quản lý hành trình đọc sách của bạn.</p><button
                        class="primary" data-go="catalog">Khám phá sách <span>→</span></button>
                </div>
                <div class="welcome-art">
                    <div class="sun"></div>
                    <div class="book-shape b1"></div>
                    <div class="book-shape b2"></div>
                    <div class="book-shape b3"></div>
                    <div class="plant">⌇</div>
                </div>
            </div>
            <div class="stats">
                <article>
                    <div class="stat-icon peach">▣</div>
                    <div><b id="chi-so-dang-muon">—</b><span>Sách đang mượn</span></div><small
                        id="ghi-chu-dang-muon">Đang tải dữ liệu</small>
                </article>
                <article>
                    <div class="stat-icon lilac">◷</div>
                    <div><b id="chi-so-cho-duyet">—</b><span>Yêu cầu chờ duyệt</span></div><small
                        id="ghi-chu-cho-duyet">Đang tải dữ liệu</small>
                </article>
                <article>
                    <div class="stat-icon mint">♢</div>
                    <div><b id="chi-so-da-tra">—</b><span>Tài liệu đã trả</span></div><small id="ghi-chu-da-tra">Đang
                        tải dữ liệu</small>
                </article>
            </div>
            <div class="content-grid">
                <section class="panel loans-panel">
                    <div class="section-head">
                        <div>
                            <p class="eyebrow">HÀNH TRÌNH ĐỌC</p>
                            <h2>Sách đang mượn</h2>
                        </div><a href="#loans" data-go="loans">Xem tất cả →</a>
                    </div>
                    <div id="danh-sach-muon-tong-quan">
                        <p>Đang tải dữ liệu...</p>
                    </div>
                </section>
                <section class="panel">
                    <div class="section-head">
                        <div>
                            <p class="eyebrow">GỢI Ý CHO BẠN</p>
                            <h2>Có thể bạn thích</h2>
                        </div><a href="#catalog" data-go="catalog">Khám phá →</a>
                    </div>
                    <div id="goi-y-tong-quan">
                        <p>Đang tải gợi ý...</p>
                    </div>
                </section>
            </div>
        </section>
        <section id="catalog" class="page">
            <div class="page-heading">
                <p class="eyebrow">KHO SÁCH LIBRA</p>
                <h1>Tìm cuốn sách dành cho bạn</h1>
                <p>Tra cứu theo tên sách, khoa, nhà xuất bản hoặc mã sách.</p>
            </div>
            <div class="search-box"><span>⌕</span><input id="book-search"
                    placeholder="Nhập tên sách, nhà xuất bản hoặc từ khóa..."><button class="primary">Tìm
                    kiếm</button></div>
            <div class="filter-row"><button class="chip active" data-filter="">Tất cả</button><button class="chip"
                    data-filter="công nghệ thông tin">Công nghệ thông tin</button><button class="chip"
                    data-filter="kinh tế">Kinh tế</button><button class="chip"
                    data-filter="luật">Luật</button><button class="chip" data-filter="y dược">Y
                    dược</button><button class="chip" data-filter="ngoại ngữ">Ngoại ngữ</button><span
                    id="book-count">11 đầu sách</span></div>
            <div id="book-grid" class="book-grid"><?php foreach ($danhSachTaiLieu as $taiLieu): ?><article class="book-card" tabindex="0"
                    role="link" data-search="<?= strtolower($taiLieu['title'] . ' ' . $taiLieu['category']) ?>"
                    data-detail-url="chiTietTaiLieu.php?id=<?= urlencode($taiLieu['code']) ?>">
                    <div class="cover large <?= $taiLieu['color'] ?>">
                        <?= $taiLieu['icon'] ?><span><?= htmlspecialchars($taiLieu['code']) ?></span></div>
                    <div class="book-card-body">
                        <p><?= htmlspecialchars($taiLieu['category']) ?></p>
                        <h3><?= htmlspecialchars($taiLieu['title']) ?></h3>
                        <footer><b class="<?= $taiLieu['available'] ? 'available' : 'unavailable' ?>">●
                                <?= $taiLieu['available'] ? 'Còn ' . $taiLieu['available'] . ' cuốn' : 'Đã hết sách' ?></b><button
                                class="detail-btn" type="button"
                                disabled><?= $taiLieu['available'] ? 'Mượn →' : 'Hết sách' ?></button></footer>
                    </div>
                </article><?php endforeach; ?></div>
        </section>
        <section id="loans" class="page">
            <div class="page-heading">
                <p class="eyebrow">QUẢN LÝ MƯỢN</p>
                <h1>Sách của tôi</h1>
                <p>Đăng ký mượn, theo dõi thời hạn và gia hạn sách trực tuyến.</p>
            </div>
            <div class="tom-tat-muon-doc-gia"><span id="so-tai-lieu-dang-muon">Đang tải...</span><span
                    id="so-tai-lieu-sap-den-han">Đang tải...</span></div>
            <section class="panel table-panel">
                <table>
                    <thead>
                        <tr>
                            <th>SÁCH</th>
                            <th>NGÀY MƯỢN</th>
                            <th>HẠN TRẢ</th>
                            <th>TRẠNG THÁI</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="du-lieu-dang-muon">
                        <tr>
                            <td><b>Cơ Sở Dữ Liệu</b><span>CNTT-101 · Abraham Silberschatz</span></td>
                            <td>07/09/2026</td>
                            <td><b class="warning">20/09/2026</b></td>
                            <td><mark class="yellow">Sắp đến hạn</mark></td>
                            <td><button class="outline renew-btn">Gia hạn</button></td>
                        </tr>
                        <tr>
                            <td><b>Kinh Tế Vi Mô</b><span>KT-044 · N. Gregory Mankiw</span></td>
                            <td>14/09/2026</td>
                            <td>28/09/2026</td>
                            <td><mark class="green-mark">Đang mượn</mark></td>
                            <td><button class="outline renew-btn">Gia hạn</button></td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </section>
        <section id="history" class="page">
            <div class="page-heading">
                <p class="eyebrow">HÀNH TRÌNH ĐỌC</p>
                <h1>Lịch sử hoạt động mượn</h1>
            </div>
            <section class="panel table-panel">
                <table>
                    <thead>
                        <tr>
                            <th>SÁCH</th>
                            <th>NGÀY MƯỢN / GỬI YÊU CẦU</th>
                            <th>NGÀY TRẢ</th>
                            <th>TRẠNG THÁI</th>
                            <th>GHI CHÚ</th>
                        </tr>
                    </thead>
                    <tbody id="du-lieu-lich-su">
                        <tr>
                            <td><b>Muôn Kiếp Nhân Sinh</b><span>Nguyên Phong</span></td>
                            <td>12/08/2026</td>
                            <td>25/08/2026</td>
                            <td><mark class="green-mark">Đã trả</mark></td>
                            <td>Nguyên vẹn</td>
                        </tr>
                        <tr>
                            <td><b>Tuổi Trẻ Đáng Giá Bao Nhiêu</b><span>Rosie Nguyễn</span></td>
                            <td>02/07/2026</td>
                            <td>16/07/2026</td>
                            <td><mark class="red-mark">Từ chối</mark></td>
                            <td>Không đủ số bản sẵn sàng.</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </section>
        <section id="reader-fines" class="page">
            <div class="page-heading">
                <h1>Khoản phạt</h1>
                <p>Các khoản chưa thanh toán sẽ tạm thời hạn chế việc đăng ký mượn tài liệu.</p>
            </div>
            <section class="panel table-panel">
                <table>
                    <thead>
                        <tr>
                            <th>TÀI LIỆU</th>
                            <th>LÝ DO</th>
                            <th>SỐ TIỀN</th>
                            <th>NGÀY LẬP</th>
                            <th>TRẠNG THÁI</th>
                            <th>NGÀY THANH TOÁN</th>
                        </tr>
                    </thead>
                    <tbody id="du-lieu-phieu-phat">
                        <tr>
                            <td colspan="6">Đang tải khoản phạt...</td>
                        </tr>
                    </tbody>
                </table>
            </section>
        </section>
        <section id="reader-pending" class="page">
            <div class="page-heading">
                <p class="eyebrow">ĐĂNG KÝ TRỰC TUYẾN</p>
                <h1>Yêu cầu chờ duyệt</h1>
                <p>Các yêu cầu sẽ được thủ thư kiểm tra trước khi bạn đến thư viện nhận sách.</p>
            </div>
            <div class="luu-y-doc-gia"><i>i</i>
                <p>Bạn có thể hủy yêu cầu khi thủ thư chưa xử lý. Khi yêu cầu được duyệt, hãy đến quầy lưu thông để nhận
                    tài liệu.</p>
            </div>
            <div id="du-lieu-yeu-cau-muon" class="danh-sach-yeu-cau-doc-gia">
                <article data-yeu-cau-doc-gia>
                    <div class="bia-yeu-cau-doc-gia blue">◉</div>
                    <div class="thong-tin-yeu-cau-doc-gia">
                        <div><b>CNTT-145</b><mark class="yellow">Chờ duyệt</mark></div>
                        <h2>Nhập Môn Trí Tuệ Nhân Tạo</h2>
                        <p>Stuart Russell & Peter Norvig</p><small>Gửi lúc 09:15 · 21/09/2026 · Mong muốn nhận trước
                            24/09/2026</small>
                    </div>
                    <div class="hanh-dong-yeu-cau-doc-gia"><span>Đang chờ thủ thư phản hồi</span><button
                            class="outline huy-yeu-cau-doc-gia">Hủy yêu cầu</button></div>
                </article>
                <article data-yeu-cau-doc-gia>
                    <div class="bia-yeu-cau-doc-gia purple">§</div>
                    <div class="thong-tin-yeu-cau-doc-gia">
                        <div><b>LUAT-032</b><mark class="yellow">Chờ duyệt</mark></div>
                        <h2>Giáo Trình Luật Dân Sự Việt Nam</h2>
                        <p>Đại học Luật Hà Nội</p><small>Gửi lúc 14:30 · 20/09/2026 · Mong muốn nhận trước
                            23/09/2026</small>
                    </div>
                    <div class="hanh-dong-yeu-cau-doc-gia"><span>Đang chờ thủ thư phản hồi</span><button
                            class="outline huy-yeu-cau-doc-gia">Hủy yêu cầu</button></div>
                </article>
            </div>
            <section class="trang-thai-rong-doc-gia" hidden><i>✓</i>
                <h2>Không có yêu cầu chờ duyệt</h2>
                <p>Bạn có thể tìm tài liệu và gửi đăng ký mượn trực tuyến.</p><button class="primary"
                    data-go="catalog">Tìm tài liệu <span>→</span></button>
            </section>
        </section>
        <section id="profile" class="page">
            <div class="page-heading">
                <p class="eyebrow">TÀI KHOẢN CỦA BẠN</p>
                <h1>Hồ sơ cá nhân</h1>
            </div>
            <div class="profile-layout">
                <section class="panel profile-card">
                    <div id="anh-dai-dien-ho-so" class="avatar xl">
                        <?= htmlspecialchars($nguoiDungHienTai['initials']) ?></div>
                    <h2 id="ten-ho-so"><?= htmlspecialchars($nguoiDungHienTai['name']) ?></h2>
                    <p id="ma-ho-so"><?= htmlspecialchars($maHoSo) ?> ·
                        <?= htmlspecialchars($nguoiDungHienTai['role_name']) ?></p>
                    <hr><span>Email</span><b id="email-ho-so"><?= htmlspecialchars($thuDienTuHoSo) ?></b><span>Số điện
                        thoại</span><b id="dien-thoai-ho-so"><?= htmlspecialchars($soDienThoaiHoSo) ?></b>
                </section>
                <section class="panel form-card">
                    <h2>Thông tin cá nhân</h2>
                    <form id="form-ho-so-doc-gia">
                        <div class="form-grid"><label>Họ và tên<input name="hoTen"
                                    value="<?= htmlspecialchars($nguoiDungHienTai['name']) ?>"></label><label>Email<input
                                    name="thuDienTu" type="email"
                                    value="<?= htmlspecialchars($thuDienTuHoSo) ?>"></label><label>Số điện thoại<input
                                    name="soDienThoai"
                                    value="<?= htmlspecialchars($soDienThoaiHoSo) ?>"></label><label>Ngày sinh<input
                                    name="ngaySinh" type="date"
                                    value="<?= htmlspecialchars($ngaySinhHoSo) ?>"></label></div><button
                            class="primary" type="submit">Lưu thay đổi</button>
                    </form>
                </section>
            </div>
        </section>
        <?php endif; ?>
        <?php
    $trangTheoVaiTro = [];
    if ($vaiTroHienTai === 'librarian') {
      $trangTheoVaiTro = ['staff-dashboard' => 'Bảng điều khiển thủ thư', 'book-admin' => 'Quản lý thông tin sách', 'publishers' => 'Quản lý nhà xuất bản', 'borrow-admin' => 'Quản lý mượn sách', 'return-admin' => 'Quản lý trả sách', 'reader-admin' => 'Quản lý hồ sơ độc giả', 'renewal-admin' => 'Yêu cầu gia hạn', 'fines' => 'Quản lý phạt'];
    } elseif ($vaiTroHienTai === 'admin') {
      $trangTheoVaiTro = ['admin-dashboard' => 'Bảng điều khiển quản trị', 'accounts' => 'Quản lý tài khoản', 'categories' => 'Quản lý khoa', 'policies' => 'Quản lý chính sách', 'admin-password' => 'Đổi mật khẩu'];
    }
    foreach ($trangTheoVaiTro as $maTrang => $tieuDeTrang):
      $duLieuMau = [
        'staff-dashboard' => ['code' => '#TT-093', 'title' => '12 phiếu mượn cần xác nhận', 'sub' => 'Ca trực sáng · Quầy lưu thông 01', 'date' => '16/09/2026', 'status' => 'Cần xử lý', 'class' => 'yellow'],
        'book-admin' => ['code' => '#TL-145', 'title' => 'Giáo trình Cơ sở dữ liệu', 'sub' => 'Tồn kho: 18 bản · Kệ CNTT-03', 'date' => '15/09/2026', 'status' => 'Đang lưu hành', 'class' => 'green-mark'],
        'publishers' => ['code' => '#NXB-021', 'title' => 'Nhà xuất bản Giáo dục Việt Nam', 'sub' => 'Đang phát hành 12 tài liệu', 'date' => '15/09/2026', 'status' => 'Đang sử dụng', 'class' => 'green-mark'],
        'borrow-admin' => ['code' => '#PM-2409', 'title' => 'Phiếu mượn của Nguyễn Minh Anh', 'sub' => 'MSSV 22110432 · 02 tài liệu', 'date' => '16/09/2026', 'status' => 'Chờ duyệt', 'class' => 'yellow'],
        'return-admin' => ['code' => '#PT-1128', 'title' => 'Trả sách: Nhà Giả Kim', 'sub' => 'Độc giả: Phạm Quốc Bảo · Không phát sinh phạt', 'date' => '16/09/2026', 'status' => 'Đã trả', 'class' => 'green-mark'],
        'reader-admin' => ['code' => '#DG-068', 'title' => 'Hồ sơ thẻ độc giả hết hạn', 'sub' => 'Lê Thị Hương · Khoa Kinh tế', 'date' => '14/09/2026', 'status' => 'Cần gia hạn', 'class' => 'yellow'],
        'renewal-admin' => ['code' => '#GH-018', 'title' => 'Yêu cầu gia hạn đang chờ duyệt', 'sub' => 'Kiểm tra và quyết định thời hạn mượn', 'date' => '', 'status' => '', 'class' => ''],
        'fines' => ['code' => '#VP-031', 'title' => 'Quá hạn 03 ngày', 'sub' => 'Trần Gia Huy · Phí tạm tính 15.000 đ', 'date' => '16/09/2026', 'status' => 'Chưa thu', 'class' => 'yellow'],
        'admin-dashboard' => ['code' => '#HT-047', 'title' => 'Nhật ký đăng nhập hệ thống', 'sub' => '42 phiên truy cập trong ngày', 'date' => '16/09/2026', 'status' => 'Bình thường', 'class' => 'green-mark'],
        'accounts' => ['code' => '#TK-019', 'title' => 'Tài khoản thủ thư mới', 'sub' => 'Phạm Khánh Linh · Chờ cấp quyền', 'date' => '15/09/2026', 'status' => 'Chờ duyệt', 'class' => 'yellow'],
        'categories' => ['code' => '#DM-072', 'title' => 'Danh mục Khoa học dữ liệu', 'sub' => 'Thuộc Khoa Công nghệ thông tin', 'date' => '12/09/2026', 'status' => 'Đang sử dụng', 'class' => 'green-mark'],
        'policies' => ['code' => '#CS-014', 'title' => 'Quy định mượn dành cho sinh viên', 'sub' => 'Tối đa 03 sách · 14 ngày · Gia hạn 01 lần', 'date' => '01/09/2026', 'status' => 'Đang áp dụng', 'class' => 'green-mark'],
        'admin-password' => ['code' => '#BM-001', 'title' => 'Cập nhật mật khẩu quản trị', 'sub' => 'Yêu cầu xác thực bằng mật khẩu hiện tại', 'date' => '', 'status' => '', 'class' => ''],
      ];
      $banGhi = $duLieuMau[$maTrang];
    ?><section id="<?= $maTrang ?>" class="page admin-page">
            <div class="page-heading">
                <p class="eyebrow">
                    <?= strpos($maTrang, 'admin') !== false || in_array($maTrang, ['accounts', 'categories', 'policies']) ? 'QUẢN TRỊ HỆ THỐNG' : 'NGHIỆP VỤ THƯ VIỆN' ?>
                </p>
                <h1><?= $tieuDeTrang ?></h1>
                <p>Quản lý dữ liệu và vận hành thư viện hiệu quả.</p>
            </div>
            <?php if ($maTrang === 'book-admin'): ?>
            <div class="inventory-stats">
                <article><span class="inventory-icon blue-soft">▤</span>
                    <div><b id="chi-so-tong-sach">—</b><small>Tổng đầu sách</small></div>
                </article>
                <article><span class="inventory-icon green-soft">✓</span>
                    <div><b id="chi-so-ban-san-sang">—</b><small>Bản sẵn sàng</small></div>
                </article>
                <article><span class="inventory-icon orange-soft">↔</span>
                    <div><b id="chi-so-dang-muon-sach">—</b><small>Đang được mượn</small></div>
                </article>
                <article><span class="inventory-icon red-soft">!</span>
                    <div><b id="chi-so-can-kiem-tra-sach">—</b><small>Đã hết sách</small></div>
                </article>
            </div>
            <section class="panel inventory-panel">
                <div class="inventory-toolbar">
                    <div class="inventory-search"><span>⌕</span><input id="inventory-search"
                            placeholder="Tìm theo mã, tên tài liệu hoặc nhà xuất bản..."></div>
                    <select id="loc-danh-muc-sach" class="inventory-filter">
                        <option value="">Tất cả khoa</option>
                    </select>
                    <button id="mo-form-sach" class="primary" type="button">+ Thêm sách</button>
                </div>
                <div class="inventory-caption"><b>Học liệu theo khoa</b><span id="tong-sach-hien-thi">Đang tải dữ
                        liệu...</span></div>
                <div class="table-panel inventory-table">
                    <table>
                        <thead>
                            <tr>
                                <th>TÀI LIỆU</th>
                                <th>MÃ / ISBN</th>
                                <th>KHOA</th>
                                <th>TỒN KHO</th>
                                <th>TRẠNG THÁI</th>
                                <th>THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody id="du-lieu-sach">
                            <tr data-book-row="cơ sở dữ liệu abraham silberschatz cntt-101">
                                <td>
                                    <div class="table-book"><i class="blue">◫</i>
                                        <div><b>Cơ Sở Dữ Liệu</b><span>Pearson Education</span></div>
                                    </div>
                                </td>
                                <td>CNTT-101<span>ISBN 978-604-0-12345-6</span></td>
                                <td>Tầng 3 · Kệ CNTT-03</td>
                                <td><b>08</b> bản</td>
                                <td><mark class="green-mark">Sẵn sàng</mark></td>
                                <td><button class="table-action book-action"
                                        data-message="Mở form chỉnh sửa Cơ Sở Dữ Liệu.">Sửa</button><button
                                        class="table-action book-action"
                                        data-message="Đã mở lịch sử bản sao của Cơ Sở Dữ Liệu.">⋮</button></td>
                            </tr>
                            <tr data-book-row="nhập môn trí tuệ nhân tạo stuart russell cntt-145">
                                <td>
                                    <div class="table-book"><i class="purple">◉</i>
                                        <div><b>Nhập Môn Trí Tuệ Nhân Tạo</b><span>Pearson Education</span></div>
                                    </div>
                                </td>
                                <td>CNTT-145<span>ISBN 978-013-604259-4</span></td>
                                <td>Tầng 3 · Kệ AI-01</td>
                                <td><b>05</b> bản</td>
                                <td><mark class="green-mark">Sẵn sàng</mark></td>
                                <td><button class="table-action book-action"
                                        data-message="Mở form chỉnh sửa Nhập Môn Trí Tuệ Nhân Tạo.">Sửa</button><button
                                        class="table-action book-action"
                                        data-message="Đã mở lịch sử bản sao của tài liệu.">⋮</button></td>
                            </tr>
                            <tr data-book-row="giáo trình luật dân sự việt nam đại học luật luat-032">
                                <td>
                                    <div class="table-book"><i class="purple">§</i>
                                        <div><b>Giáo Trình Luật Dân Sự Việt Nam</b><span>Nhà xuất bản Giáo dục Việt
                                                Nam</span></div>
                                    </div>
                                </td>
                                <td>LUAT-032<span>ISBN 978-604-72-9876-2</span></td>
                                <td>Tầng 2 · Kệ LUAT-05</td>
                                <td><b>04</b> bản</td>
                                <td><mark class="green-mark">Sẵn sàng</mark></td>
                                <td><button class="table-action book-action"
                                        data-message="Mở form chỉnh sửa Giáo Trình Luật Dân Sự Việt Nam.">Sửa</button><button
                                        class="table-action book-action"
                                        data-message="Đã mở lịch sử bản sao của tài liệu.">⋮</button></td>
                            </tr>
                            <tr data-book-row="sinh lý học y khoa guyton hall yd-027">
                                <td>
                                    <div class="table-book"><i class="green">✥</i>
                                        <div><b>Sinh Lý Học Y Khoa</b><span>Nhà xuất bản Giáo dục Việt Nam</span></div>
                                    </div>
                                </td>
                                <td>YD-027<span>ISBN 978-032-359712-8</span></td>
                                <td>Tầng 4 · Kệ YD-06</td>
                                <td><b>01</b> bản</td>
                                <td><mark class="yellow">Sắp hết</mark></td>
                                <td><button class="table-action book-action"
                                        data-message="Mở form chỉnh sửa Sinh Lý Học Y Khoa.">Sửa</button><button
                                        class="table-action book-action"
                                        data-message="Đã mở lịch sử bản sao của tài liệu.">⋮</button></td>
                            </tr>
                            <tr data-book-row="pháp luật đại cương nguyễn văn động luat-008">
                                <td>
                                    <div class="table-book"><i class="blue">⚖</i>
                                        <div><b>Pháp Luật Đại Cương</b><span>Nhà xuất bản Giáo dục Việt Nam</span></div>
                                    </div>
                                </td>
                                <td>LUAT-008<span>ISBN 978-604-73-6241-8</span></td>
                                <td>Tầng 2 · Kệ LUAT-01</td>
                                <td><b>00</b> bản</td>
                                <td><mark class="red-mark">Hết sách</mark></td>
                                <td><button class="table-action book-action"
                                        data-message="Mở form chỉnh sửa Pháp Luật Đại Cương.">Sửa</button><button
                                        class="table-action book-action"
                                        data-message="Đã mở lịch sử bản sao của tài liệu.">⋮</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="inventory-footer"><span id="phan-trang-sach">Đang tải dữ liệu...</span>
                    <div id="nut-phan-trang-sach" class="nut-phan-trang"></div><button id="mo-form-tom-tat"
                        class="outline" type="button">Cập nhật tóm tắt</button>
                </div>
            </section>
            <?php elseif ($maTrang === 'publishers'): ?>
            <section class="panel nxb-panel">
                <div class="inventory-toolbar">
                    <div class="inventory-search"><span>⌕</span><input id="tim-nxb"
                            placeholder="Tìm theo mã hoặc tên nhà xuất bản..."></div><button id="mo-form-nxb"
                        class="primary" type="button">+ Thêm nhà xuất bản</button>
                </div>
                <div class="inventory-caption"><b>Danh sách nhà xuất bản</b><span id="tong-nxb">Đang tải dữ
                        liệu...</span></div>
                <div class="table-panel inventory-table">
                    <table>
                        <thead>
                            <tr>
                                <th>MÃ</th>
                                <th>TÊN NHÀ XUẤT BẢN</th>
                                <th>ĐỊA CHỈ</th>
                                <th>SỐ TÀI LIỆU</th>
                                <th>THAO TÁC</th>
                            </tr>
                        </thead>
                        <tbody id="du-lieu-nxb"></tbody>
                    </table>
                </div>
                <div class="inventory-footer"><span id="phan-trang-nxb">Đang tải dữ liệu...</span>
                    <div id="nut-phan-trang-nxb" class="nut-phan-trang"></div>
                </div>
            </section>
            <?php elseif ($vaiTroHienTai === 'librarian'): ?>
            @include('partials.giaoDienThuThu')
            <?php elseif ($vaiTroHienTai === 'admin'): ?>
            @include('partials.giaoDienQuanTri')
            <?php endif; ?>
        </section><?php endforeach; ?>
    </main>
    <div id="hop-tu-choi-yeu-cau" class="hop-tu-choi-yeu-cau" aria-hidden="true" hidden>
        <section class="noi-dung-tu-choi-yeu-cau" role="dialog" aria-modal="true"
            aria-labelledby="tieu-de-tu-choi-yeu-cau">
            <div class="dau-tu-choi-yeu-cau">
                <div>
                    <p class="eyebrow">XỬ LÝ YÊU CẦU</p>
                    <h2 id="tieu-de-tu-choi-yeu-cau">Từ chối yêu cầu mượn</h2>
                </div>
                <button class="dong-tu-choi-yeu-cau" type="button" aria-label="Đóng">×</button>
            </div>
            <p>Hãy ghi rõ lý do để độc giả có thể xem và điều chỉnh yêu cầu của mình.</p>
            <form id="form-tu-choi-yeu-cau">
                <label for="ly-do-tu-choi-yeu-cau">Lý do từ chối</label>
                <textarea id="ly-do-tu-choi-yeu-cau" name="lyDoTuChoi" rows="4" maxlength="500" required
                    placeholder="Ví dụ: Tài liệu hiện đã hết bản có thể mượn."></textarea>
                <div class="chan-tu-choi-yeu-cau">
                    <button class="outline huy-tu-choi-yeu-cau" type="button">Hủy</button>
                    <button class="primary" type="submit">Xác nhận từ chối</button>
                </div>
            </form>
        </section>
    </div>
    <div id="hop-chon-tai-lieu" class="hop-chon-tai-lieu" aria-hidden="true">
        <div class="noi-dung-chon-tai-lieu">
            <div class="dau-chon-tai-lieu">
                <div>
                    <p class="eyebrow">KHO HỌC LIỆU</p>
                    <h2 id="tieu-de-chon-tai-lieu">Chọn tài liệu</h2>
                </div><button class="dong-chon-tai-lieu" aria-label="Đóng">×</button>
            </div>
            <div class="o-tim-tai-lieu"><span>⌕</span><input id="tim-tai-lieu-trong-hop"
                    placeholder="Tìm theo tên, mã tài liệu hoặc nhà xuất bản..."></div>
            <p class="ghi-chu-chon-tai-lieu">Chọn các tài liệu cần thêm vào phiếu. Hệ thống sẽ kiểm tra số lượng còn
                lại trước khi xác nhận.</p>
            <div id="danh-sach-chon-tai-lieu" class="danh-sach-chon-tai-lieu">
                <label data-muc-chon-tai-lieu="cơ sở dữ liệu abraham silberschatz cntt-101"><input type="checkbox"
                        checked><i class="blue">◫</i><span><b>Cơ Sở Dữ Liệu</b><small>CNTT-101 · Còn 08
                            bản</small></span></label>
                <label data-muc-chon-tai-lieu="nhập môn trí tuệ nhân tạo stuart russell cntt-145"><input
                        type="checkbox"><i class="purple">◉</i><span><b>Nhập Môn Trí Tuệ Nhân Tạo</b><small>CNTT-145 ·
                            Còn 05 bản</small></span></label>
                <label data-muc-chon-tai-lieu="nguyên lý kế toán weygandt kt-021"><input type="checkbox"><i
                        class="orange">₫</i><span><b>Nguyên Lý Kế Toán</b><small>KT-021 · Còn 06
                            bản</small></span></label>
                <label data-muc-chon-tai-lieu="pháp luật đại cương nguyễn văn động luat-008"><input type="checkbox"
                        disabled><i class="blue">⚖</i><span><b>Pháp Luật Đại Cương</b><small>LUAT-008 · Tạm hết
                            bản</small></span><em>Hết sách</em></label>
            </div>
            <div class="chan-chon-tai-lieu"><span id="so-luong-da-chon">01 tài liệu đã chọn</span>
                <div><button class="outline dong-chon-tai-lieu">Hủy</button><button
                        class="primary xac-nhan-chon-tai-lieu">Thêm vào phiếu <span>→</span></button></div>
            </div>
        </div>
    </div>
    <div id="hopThoai" class="modal">
        <div class="modal-card"><button class="close">×</button>
            <p class="eyebrow">THÔNG TIN SÁCH</p>
            <h2 id="hopThoai-title">Đắc Nhân Tâm</h2>
            <p>Cuốn sách hiện có trong kho. Bạn có thể gửi yêu cầu mượn và theo dõi trạng thái xử lý tại mục Sách của
                tôi.</p><button class="primary borrow-btn">Đăng ký mượn sách</button>
        </div>
    </div>
    <div id="hop-sach-thu-thu" class="hop-tai-khoan-quan-tri" aria-hidden="true">
        <section class="noi-dung-tai-khoan-quan-tri" role="dialog" aria-modal="true">
            <div class="dau-hop-tai-khoan">
                <div>
                    <p class="eyebrow">QUẢN LÝ SÁCH</p>
                    <h2 id="tieu-de-form-sach">Thêm tài liệu</h2>
                </div><button class="dong-hop-sach" type="button">×</button>
            </div>
            <form id="form-sach-thu-thu" enctype="multipart/form-data"><input name="maTaiLieu" type="hidden"><input
                    name="anhBiaHienTai" type="hidden">
                <div class="luoi-truong-tai-khoan">
                    <label>Mã tài liệu<input name="ma" placeholder="Ví dụ: CNTT-200" required></label>
                    <label>Khoa<select name="maDanhMuc" id="danh-muc-sach" required></select></label>
                    <label class="rong">Tên tài liệu<input name="tieuDe" required></label>
                    <label class="rong">Nhà xuất bản<div class="truong-nxb"><select name="maNhaXuatBan"
                                id="nha-xuat-ban-sach" required></select><button id="mo-form-nxb-nhanh"
                                class="outline" type="button">+ Thêm NXB</button></div></label>
                    <label>Năm xuất bản<input name="namXuatBan" type="number" min="1900"></label>
                    <label>Số lượng tổng<input name="soLuongTong" type="number" min="0" value="1"
                            required></label>
                    <label class="rong">ISBN<input name="maIsbn"></label>
                    <label>Vị trí kệ<select name="keSach" required>
                            <option value="">Chọn kệ</option>
                            <option value="A">Kệ A</option>
                            <option value="B">Kệ B</option>
                            <option value="C">Kệ C</option>
                            <option value="D">Kệ D</option>
                            <option value="E">Kệ E</option>
                            <option value="F">Kệ F</option>
                        </select></label>
                    <label>Tầng<select name="tangKe" required>
                            <option value="">Chọn tầng</option>
                            <option value="1">Tầng 1</option>
                            <option value="2">Tầng 2</option>
                            <option value="3">Tầng 3</option>
                            <option value="4">Tầng 4</option>
                            <option value="5">Tầng 5</option>
                        </select></label>
                    <label>Ngăn<select name="nganKe" required>
                            <option value="">Chọn ngăn</option>
                            <option value="1">Ngăn 01</option>
                            <option value="2">Ngăn 02</option>
                            <option value="3">Ngăn 03</option>
                            <option value="4">Ngăn 04</option>
                            <option value="5">Ngăn 05</option>
                            <option value="6">Ngăn 06</option>
                            <option value="7">Ngăn 07</option>
                            <option value="8">Ngăn 08</option>
                            <option value="9">Ngăn 09</option>
                            <option value="10">Ngăn 10</option>
                        </select></label>
                    <label class="rong">Ảnh bìa<input name="anhBia" type="file"
                            accept="image/jpeg,image/png,image/webp"><small>Chọn ảnh JPG, PNG hoặc WEBP, tối đa 5
                            MB.</small></label>
                </div>
                <p id="loi-form-sach" class="loi-tai-khoan-quan-tri" hidden></p>
                <div class="chan-hop-tai-khoan"><button class="outline dong-hop-sach"
                        type="button">Hủy</button><button class="primary" type="submit">Lưu tài liệu
                        <span>→</span></button></div>
            </form>
        </section>
    </div>
    <div id="hop-nxb-thu-thu" class="hop-tai-khoan-quan-tri" aria-hidden="true">
        <section class="noi-dung-tai-khoan-quan-tri" role="dialog" aria-modal="true">
            <div class="dau-hop-tai-khoan">
                <div>
                    <p class="eyebrow">NHÀ XUẤT BẢN</p>
                    <h2 id="tieu-de-form-nxb">Thêm nhà xuất bản</h2>
                </div><button class="dong-hop-nxb" type="button">×</button>
            </div>
            <form id="form-nxb-thu-thu"><input name="maNhaXuatBan" type="hidden">
                <div class="luoi-truong-tai-khoan"><label>Mã NXB<input name="ma" placeholder="Ví dụ: NXBGD"
                            required></label><label>Tên nhà xuất bản<input name="ten" required></label><label
                        class="rong">Địa chỉ<input name="diaChi"></label><label>Số điện thoại<input
                            name="soDienThoai"></label></div>
                <p id="loi-form-nxb" class="loi-tai-khoan-quan-tri" hidden></p>
                <div class="chan-hop-tai-khoan"><button class="outline dong-hop-nxb"
                        type="button">Hủy</button><button class="primary" type="submit">Lưu nhà xuất bản
                        <span>→</span></button></div>
            </form>
        </section>
    </div>
    <div id="hop-tom-tat-thu-thu" class="hop-tai-khoan-quan-tri" aria-hidden="true">
        <section class="noi-dung-tai-khoan-quan-tri" role="dialog" aria-modal="true">
            <div class="dau-hop-tai-khoan">
                <div>
                    <p class="eyebrow">NỘI DUNG TÀI LIỆU</p>
                    <h2>Cập nhật tóm tắt</h2>
                </div><button class="dong-hop-tai-khoan dong-hop-tom-tat" type="button" aria-label="Đóng">×</button>
            </div>
            <form id="form-tom-tat-thu-thu"><input name="maTaiLieu" type="hidden">
                <div class="o-loc-tom-tat"><input id="tim-sach-tom-tat"
                        placeholder="Tìm tên sách, mã sách hoặc nhà xuất bản..."><select id="loc-khoa-tom-tat">
                        <option value="">Tất cả khoa</option>
                    </select></div>
                <div id="danh-sach-sach-tom-tat" class="danh-sach-sach-tom-tat"></div><label
                    class="truong-tom-tat">Tóm tắt
                    <textarea name="tomTat" rows="8" maxlength="3000"
                        placeholder="Nhập nội dung tóm tắt cho tài liệu đã chọn..."></textarea>
                </label>
                <p id="loi-form-tom-tat" class="loi-tai-khoan-quan-tri" hidden></p>
                <div class="chan-hop-tai-khoan"><button class="outline dong-hop-tom-tat"
                        type="button">Hủy</button><button class="primary" type="submit">Lưu tóm tắt
                        <span>→</span></button></div>
            </form>
        </section>
    </div>
    <div id="hienThongBao" class="toast">Đã cập nhật thành công</div>
    <script src="assets/app.js?v=20261022-1"></script>
    <script src="assets/docGiaApi.js?v=20261028-2"></script>
    <script src="assets/quanTriApi.js?v=20261004-3"></script>
    <script src="assets/quanTriHeThong.js?v=20261003-5"></script>
    <script src="assets/thuThuApi.js?v=20261010-2"></script>
    <script src="assets/thuThuSach.js?v=20261028-1"></script>
    <script src="assets/thuThuSachThongKe.js?v=20261002-2"></script>
</body>

</html>
