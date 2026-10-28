<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng ký | LIBRA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/login.css">
    <link rel="stylesheet" href="assets/login-layout.css">
</head>

<body class="login-body">
    <main class="login-wrap">
        <section class="login-story"><a class="brand" href="login.php"><span class="brand-mark">L</span><span>LIBRA<small>THƯ VIỆN SỐ</small></span></a>
            <div class="story-copy">
                <p class="eyebrow">ĐĂNG KÝ ĐỘC GIẢ</p>
                <h1>Bắt đầu hành trình<br><em>cùng tri thức.</em></h1>
                <p>MSSV hoặc MSGV của bạn sẽ là tên đăng nhập. Bạn tự thiết lập mật khẩu để bảo vệ tài khoản của mình.</p>
            </div>
            <div class="login-art">
                <div class="sun"></div>
                <div class="book-shape b1"></div>
                <div class="book-shape b2"></div>
                <div class="book-shape b3"></div>
                <div class="plant">⌇</div>
            </div>
        </section>
        <section class="login-form-side">
            <form id="form-dang-ky" class="login-card">
                <p class="eyebrow">TẠO TÀI KHOẢN</p>
                <h2>Đăng ký độc giả</h2>
                <p class="form-intro">Điền thông tin hồ sơ để tạo thẻ thư viện trực tuyến.</p>
                <div id="loi-dang-ky" class="login-error" hidden></div><label>MSSV hoặc MSGV<input name="code" placeholder="Ví dụ: 22110456" required></label><label>Họ và tên<input name="name" placeholder="Nguyễn Văn A" required></label><label>Email<input name="email" type="email" placeholder="sinhvien@university.edu.vn" required></label><label>Số điện thoại<input name="phone" placeholder="0901 234 567" required></label><label>Ngày sinh<input name="birth_date" type="date" required></label><label>Mật khẩu<input name="password" type="password" placeholder="Tối thiểu 8 ký tự" required></label><label>Xác nhận mật khẩu<input name="password_confirmation" type="password" placeholder="Nhập lại mật khẩu" required></label><button class="primary login-submit" type="submit">Tạo tài khoản <span>→</span></button>
                <p class="login-footer">Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
            </form>
        </section>
    </main>
</body>

<script>
    document.querySelector('#form-dang-ky').onsubmit = async e => {
        e.preventDefault();
        const f = e.currentTarget,
            loi = document.querySelector('#loi-dang-ky');
        if (f.password.value !== f.password_confirmation.value) {
            loi.textContent = 'Xác nhận mật khẩu chưa khớp.';
            loi.hidden = false;
            return
        }
        const r = await fetch('api/dangKy.php', {
            method: 'POST',
                headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(Object.fromEntries(new FormData(f)))
        });
        const d = await r.json();
        if (!r.ok) {
            loi.textContent = d.loi;
            loi.hidden = false;
            return
        }
        location.href = 'login.php?registered=1'
    }
</script>

</html>
