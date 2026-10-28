<?php
$maTaiLieu = trim(request('id', ''));
$laDocGia = (session('role') === 'reader');
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Chi tiết tài liệu | LIBRA</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/chiTietTaiLieu.css?v=20261002-1">
</head>
<body>
  <header class="detail-header"><a class="brand" href="index.php"><span class="brand-mark">L</span><span>LIBRA<small>THƯ VIỆN SỐ</small></span></a><a class="back-link" href="index.php">← Quay lại thư viện</a></header>
  <main class="detail-main">
    <p id="duong-dan-tai-lieu" class="breadcrumb">Trang chủ / Tìm kiếm sách / Đang tải...</p>
    <section class="book-hero">
      <div id="bia-tai-lieu" class="cover feature blue">◫<span id="ma-tai-lieu">—</span></div>
      <div class="book-overview">
        <p id="khoa-tai-lieu" class="eyebrow">ĐANG TẢI</p>
        <h1 id="tieu-de-tai-lieu">Đang tải tài liệu...</h1>
        <h2 id="nha-xuat-ban-tai-lieu">&nbsp;</h2>
        <div id="trang-thai-tai-lieu" class="availability">Đang tải tình trạng</div>
        <p id="tom-tat-tai-lieu" class="description">Đang tải nội dung tóm tắt...</p>
        <div class="book-actions"><button id="dang-ky-muon-chi-tiet" class="primary" type="button" disabled>Đang tải <span>→</span></button></div>
      </div>
    </section>
    <section class="detail-grid">
      <article class="detail-panel">
        <h2>Thông tin thư mục</h2>
        <dl>
          <div><dt>Mã tài liệu</dt><dd id="thong-tin-ma">—</dd></div>
          <div><dt>Nhà xuất bản</dt><dd id="thong-tin-nxb">—</dd></div>
          <div><dt>Năm xuất bản</dt><dd id="thong-tin-nam">—</dd></div>
          <div><dt>Vị trí kệ</dt><dd id="thong-tin-vi-tri-ke">—</dd></div>
        </dl>
      </article>
      <article class="detail-panel">
        <h2>Hướng dẫn mượn tài liệu</h2>
        <p>Độc giả có thể gửi yêu cầu mượn trực tuyến. Thủ thư sẽ kiểm tra tình trạng thẻ và số lượng tài liệu trước khi xác nhận.</p>
      </article>
    </section>
  </main>
  <div id="toast" class="toast">Đã cập nhật</div>
  <script>
    const maTaiLieu = <?= json_encode($maTaiLieu, JSON_UNESCAPED_UNICODE) ?>;
    const laDocGia = <?= $laDocGia ? 'true' : 'false' ?>;
    const anToan = giaTri => String(giaTri ?? '').replace(/[&<>"']/g, kyTu => ({'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'}[kyTu]));
    const hienThongBao = noiDung => {
      const t = document.querySelector('#toast');
      t.textContent = noiDung;
      t.classList.add('show');
      setTimeout(() => t.classList.remove('show'), 2800);
    };
    const hienLoi = noiDung => {
      document.querySelector('#tieu-de-tai-lieu').textContent = 'Không thể tải tài liệu';
      document.querySelector('#tom-tat-tai-lieu').textContent = noiDung;
      document.querySelector('#trang-thai-tai-lieu').textContent = 'Không có dữ liệu';
    };
    const napChiTiet = async () => {
      if (!maTaiLieu) return hienLoi('Thiếu mã tài liệu. Hãy quay lại kho sách và chọn lại tài liệu.');
      try {
        const phanHoi = await fetch(`api/docGia.php?hanhDong=chiTietTaiLieu&ma=${encodeURIComponent(maTaiLieu)}`);
        const ketQua = await phanHoi.json();
        if (!phanHoi.ok) throw new Error(ketQua.loi || 'Không tìm thấy tài liệu.');
        const taiLieu = ketQua.duLieu;
        document.title = `${taiLieu.tieuDe} | LIBRA`;
        document.querySelector('#duong-dan-tai-lieu').textContent = `Trang chủ / Tìm kiếm sách / ${taiLieu.khoa || 'Kho học liệu'}`;
        document.querySelector('#khoa-tai-lieu').textContent = taiLieu.khoa || 'KHO HỌC LIỆU';
        document.querySelector('#tieu-de-tai-lieu').textContent = taiLieu.tieuDe;
        document.querySelector('#nha-xuat-ban-tai-lieu').textContent = taiLieu.nhaXuatBan || 'Chưa có nhà xuất bản';
        document.querySelector('#tom-tat-tai-lieu').textContent = taiLieu.tomTat || 'Tài liệu này chưa có nội dung tóm tắt.';
        document.querySelector('#ma-tai-lieu').textContent = taiLieu.ma;
        document.querySelector('#thong-tin-ma').textContent = taiLieu.ma;
        document.querySelector('#thong-tin-nxb').textContent = taiLieu.nhaXuatBan || '—';
        document.querySelector('#thong-tin-nam').textContent = taiLieu.namXuatBan || '—';
        document.querySelector('#thong-tin-vi-tri-ke').textContent = taiLieu.viTriKe || 'Chưa cập nhật';
        const bia = document.querySelector('#bia-tai-lieu');
        if (taiLieu.anhBia) {
          bia.classList.add('co-anh');
          bia.innerHTML = `<img src="${anToan(taiLieu.anhBia)}" alt="Bìa ${anToan(taiLieu.tieuDe)}"><span>${anToan(taiLieu.ma)}</span>`;
        }
        const conSach = Number(taiLieu.soBan) > 0, coTheMuon = laDocGia && conSach && taiLieu.duocMuon === 'co';
        const trangThai = document.querySelector('#trang-thai-tai-lieu');
        trangThai.className = `availability ${conSach ? 'available-box' : 'unavailable-box'}`;
        trangThai.textContent = conSach ? `● Hiện còn ${taiLieu.soBan} bản có thể mượn` : '● Tạm hết bản có thể mượn';
        const nut = document.querySelector('#dang-ky-muon-chi-tiet');
        nut.disabled = !coTheMuon;
        nut.dataset.id = taiLieu.maTaiLieu;
        nut.textContent = coTheMuon ? 'Đăng ký mượn →' : (!laDocGia ? 'Chỉ độc giả có thể đăng ký' : (conSach ? 'Tài liệu không được mượn' : 'Tạm hết sách'));
      } catch (loi) {
        hienLoi(loi.message);
      }
    };
    document.querySelector('#dang-ky-muon-chi-tiet').onclick = async suKien => {
      const nut = suKien.currentTarget;
      if (nut.disabled || !nut.dataset.id) return;
      nut.disabled = true;
      try {
        const phanHoi = await fetch('api/docGia.php?hanhDong=yeuCauMuon', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({maTaiLieu: [nut.dataset.id]})});
        const ketQua = await phanHoi.json();
        if (!phanHoi.ok || !ketQua.thanhCong) throw new Error(ketQua.loi || 'Không thể gửi yêu cầu mượn.');
        nut.textContent = 'Đã gửi yêu cầu';
        hienThongBao('Yêu cầu mượn đã được gửi đến thủ thư.');
      } catch (loi) {
        nut.disabled = false;
        hienThongBao(loi.message);
      }
    };
    napChiTiet();
  </script>
</body>
</html>
