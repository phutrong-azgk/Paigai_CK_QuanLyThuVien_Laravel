<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đăng nhập | LIBRA</title>
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
                <p class="eyebrow">TRUNG TÂM HỌC LIỆU</p>
                <h1>Tri thức mở ra<br><em>mọi hành trình.</em></h1>
                <p>Tra cứu học liệu, quản lý mượn trả và kết nối với kho tàng tri thức của trường đại học.</p>
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
            <form id="form-dang-nhap" class="login-card">
                <p class="eyebrow">CHÀO MỪNG BẠN TRỞ LẠI</p>
                <h2>Đăng nhập hệ thống</h2>
                <p class="form-intro">Sử dụng MSSV, MSGV hoặc tài khoản quản trị để tiếp tục.</p>
                <div id="loi-dang-nhap" class="login-error" hidden></div>
                <div id="tao-tai-khoan-thanh-cong" class="account-created" hidden>Tạo tài khoản thành công. Hãy đăng nhập bằng mã số và mật khẩu bạn vừa đặt.</div><label>Mã đăng nhập<input name="username" autocomplete="username" placeholder="Ví dụ: 22110456" required autofocus></label><label>Mật khẩu<div class="password-wrap"><input name="password" type="password" autocomplete="current-password" placeholder="Nhập mật khẩu" required><button type="button" id="toggle-password">◉</button></div></label>
                <div class="login-options"><label class="remember"><input type="checkbox"> Ghi nhớ đăng nhập</label><a href="#">Quên mật khẩu?</a></div><button class="primary login-submit" type="submit">Đăng nhập <span>→</span></button>
                <p class="login-footer">Chưa có tài khoản? <a href="dangKy.php">Đăng ký độc giả</a></p>
                @if(app()->environment('local'))
                    <a href="dangNhapNhanh.php" class="login-quick">Đăng nhập nhanh</a>
                @endif
            </form>
        </section>
    </main>
    <script>
        if (new URLSearchParams(location.search).has('registered')) document.querySelector('#tao-tai-khoan-thanh-cong').hidden = false;
        document.querySelector('#form-dang-nhap').onsubmit = async e => {
            e.preventDefault();
            const f = e.currentTarget,
                loi = document.querySelector('#loi-dang-nhap');
            const r = await fetch('api/dangNhap.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    username: f.username.value,
                    password: f.password.value
                })
            });
            const d = await r.json();
            if (!r.ok) {
                loi.textContent = d.loi;
                loi.hidden = false;
                return;
            }
            location.href = d.chuyenTrang;
        };
        document.querySelector('#toggle-password').onclick = () => {
            const i = document.querySelector('[name=password]');
            i.type = i.type === 'password' ? 'text' : 'password'
        }
    </script>
</body>

</html>
