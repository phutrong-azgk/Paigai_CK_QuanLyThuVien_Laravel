(() => {
  if (document.body.dataset.role !== 'reader') return;
  const goiApi = async url => {
    const r = await fetch(url);
    const d = await r.json();
    if (!r.ok) throw new Error(d.loi);
    return d.duLieu || [];
  };
  const anToan = v => String(v ?? '').replace(/[&<>"]/g, c => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;'
  } [c]));
  const dinhDangNgay = v => v ? new Date(v).toLocaleDateString('vi-VN') : '';
  const napHoSo = async () => {
    const form = document.querySelector('#form-ho-so-doc-gia');
    if (!form) return;
    const nap = async () => {
      const phanHoi = await fetch('api/docGia.php?hanhDong=hoSo');
      const ketQua = await phanHoi.json();
      if (!phanHoi.ok) throw new Error(ketQua.loi);
      const hoSo = ketQua.duLieu;
      Object.entries({hoTen: hoSo.hoTen, thuDienTu: hoSo.thuDienTu, soDienThoai: hoSo.soDienThoai || '', ngaySinh: hoSo.ngaySinh || ''}).forEach(([ten, giaTri]) => form[ten].value = giaTri);
      document.querySelector('#ten-ho-so').textContent = hoSo.hoTen;
      document.querySelector('#ma-ho-so').textContent = `${hoSo.maSo} · Độc giả`;
      document.querySelector('#email-ho-so').textContent = hoSo.thuDienTu;
      document.querySelector('#dien-thoai-ho-so').textContent = hoSo.soDienThoai || 'Chưa cập nhật';
      document.querySelector('#anh-dai-dien-ho-so').textContent = hoSo.hoTen.split(/\s+/).map(ten => ten[0]).slice(-2).join('').toUpperCase();
    };
    form.onsubmit = async suKien => {
      suKien.preventDefault();
      const nut = form.querySelector('button[type="submit"]');
      nut.disabled = true;
      try {
        const phanHoi = await fetch('api/docGia.php?hanhDong=hoSo', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(Object.fromEntries(new FormData(form)))});
        const ketQua = await phanHoi.json();
        if (!phanHoi.ok) throw new Error(ketQua.loi || 'Không thể lưu hồ sơ.');
        await nap();
        window.hienThongBao?.('Đã lưu thông tin cá nhân.');
      } catch (loi) { window.hienThongBao?.(loi.message); }
      finally { nut.disabled = false; }
    };
    await nap();
  };
  const napTongQuan = async () => {
    const chiSo = document.querySelector('#chi-so-dang-muon');
    if (!chiSo) return;
    const phanHoi = await fetch('api/docGia.php?hanhDong=tongQuan');
    const ketQua = await phanHoi.json();
    if (!phanHoi.ok) throw new Error(ketQua.loi);
    const duLieu = ketQua.duLieu || {};
    chiSo.textContent = duLieu.dangMuon || 0;
    document.querySelector('#ghi-chu-dang-muon').textContent = duLieu.sapDenHan ? `${duLieu.sapDenHan} sắp đến hạn` : 'Chưa có sách sắp đến hạn';
    document.querySelector('#chi-so-cho-duyet').textContent = duLieu.choDuyet || 0;
    document.querySelector('#ghi-chu-cho-duyet').textContent = duLieu.choDuyet ? 'Thủ thư đang xử lý' : 'Không có yêu cầu mới';
    document.querySelector('#chi-so-da-tra').textContent = duLieu.daTra || 0;
    document.querySelector('#ghi-chu-da-tra').textContent = duLieu.daTra ? 'Tổng số tài liệu đã trả' : 'Chưa có lịch sử trả sách';

    const khungMuon = document.querySelector('#danh-sach-muon-tong-quan');
    const homNay = new Date();
    homNay.setHours(0, 0, 0, 0);
    const danhSachMuon = ketQua.dangMuonGanDay || [];
    khungMuon.innerHTML = danhSachMuon.map((sach, chiSo) => {
      const hanTra = new Date(`${sach.hanTra}T00:00:00`);
      const soNgay = Math.round((hanTra - homNay) / 86400000);
      const thongBao = soNgay < 0 ? `Quá hạn ${Math.abs(soNgay)} ngày` : soNgay === 0 ? 'Hạn trả hôm nay' : `Còn ${soNgay} ngày để trả sách`;
      const tienDo = Math.max(8, Math.min(100, 100 - soNgay * 7));
      return `<div class="loan-row"><div class="cover ${['blue', 'green'][chiSo % 2]}">◫</div><div class="loan-info"><b>${anToan(sach.tieuDe)}</b><span>${anToan(sach.ma)}</span><div class="progress"><i style="width:${tienDo}%"></i></div><small>${thongBao}</small></div><button class="outline xem-muon-tong-quan" type="button">Xem</button></div>`;
    }).join('') || '<p>Bạn chưa có tài liệu đang mượn.</p>';
    khungMuon.querySelectorAll('.xem-muon-tong-quan').forEach(nut => nut.onclick = () => window.hienThiTrang?.('loans'));

    const khungGoiY = document.querySelector('#goi-y-tong-quan');
    const goiY = ketQua.goiY;
    khungGoiY.innerHTML = goiY ? `<div class="recommend"><div class="cover purple">◉</div><div><b>${anToan(goiY.tieuDe)}</b><span>${anToan(goiY.nhaXuatBan || 'Chưa có nhà xuất bản')}</span><p>Tài liệu sẵn sàng thuộc ${anToan(goiY.khoa || 'kho học liệu')}.</p><a class="text-btn" href="chiTietTaiLieu.php?id=${encodeURIComponent(goiY.ma)}">Xem chi tiết →</a></div></div>` : '<p>Chưa có tài liệu phù hợp để gợi ý.</p>';
  };
  const napTaiLieu = async () => {
    const khung = document.querySelector('#book-grid');
    if (!khung) return;
    const ds = await goiApi('api/docGia.php?hanhDong=taiLieu');
    khung.innerHTML = ds.map((x, i) => `<article class="book-card" tabindex="0" role="link" data-search="${anToan(`${x.tieuDe} ${x.nhaXuatBan || ''} ${x.danhMuc}`).toLowerCase()}" data-detail-url="chiTietTaiLieu.php?id=${encodeURIComponent(x.ma)}"><div class="cover large ${['blue','purple','green','orange'][i%4]}">◫<span>${anToan(x.ma)}</span></div><div class="book-card-body"><p>${anToan(x.danhMuc)}</p><h3>${anToan(x.tieuDe)}</h3><span>${anToan(x.nhaXuatBan || 'Chưa có nhà xuất bản')}</span><footer><b class="${x.soBan>0?'available':'unavailable'}">● ${x.soBan>0?`Còn ${x.soBan} cuốn`:'Đã hết sách'}</b><button class="detail-btn muon-ngay-api" type="button" data-id="${x.maTaiLieu}" ${x.soBan>0?'':'disabled'}>${x.soBan>0?'Mượn →':'Hết sách'}</button></footer></div></article>`).join('');
    khung.onclick = async suKien => {
      const nutMuon = suKien.target.closest('.muon-ngay-api');
      if (nutMuon) {
        nutMuon.disabled = true;
        try {
          const phanHoi = await fetch('api/docGia.php?hanhDong=yeuCauMuon', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({maTaiLieu: [nutMuon.dataset.id]})});
          const ketQua = await phanHoi.json();
          if (!phanHoi.ok || !ketQua.thanhCong) throw new Error(ketQua.loi || 'Không thể gửi yêu cầu mượn.');
          nutMuon.textContent = 'Đã gửi yêu cầu';
          window.hienThongBao?.('Yêu cầu mượn đã được gửi đến thủ thư.');
          napYeuCau();
        } catch (loi) { nutMuon.disabled = false; window.hienThongBao?.(loi.message); }
        return;
      }
      const sach = suKien.target.closest('.book-card');
      if (sach) window.location.href = sach.dataset.detailUrl;
    };
    khung.onkeydown = suKien => {
      const sach = suKien.target.closest('.book-card');
      if (sach && (suKien.key === 'Enter' || suKien.key === ' ')) { suKien.preventDefault(); window.location.href = sach.dataset.detailUrl; }
    };
  };
  const napMuon = async () => {
    const tbody = document.querySelector('#du-lieu-dang-muon');
    if (!tbody) return;
    const ds = await goiApi('api/docGia.php?hanhDong=phieuMuon');
    const homNay = new Date(); homNay.setHours(0, 0, 0, 0);
    const sapDenHan = ds.filter(x => new Date(`${x.hanTra}T00:00:00`) <= new Date(homNay.getTime() + 3 * 86400000)).length;
    document.querySelector('#so-tai-lieu-dang-muon').textContent = `${ds.length} tài liệu đang mượn`;
    document.querySelector('#so-tai-lieu-sap-den-han').textContent = sapDenHan ? `${sapDenHan} tài liệu sắp đến hạn` : 'Chưa có tài liệu sắp đến hạn';
    tbody.innerHTML = ds.map(x => {
      const quaHan = new Date(`${x.hanTra}T00:00:00`) < homNay;
      const daGuiGiaHan = x.trangThaiGiaHan === 'choDuyet';
      return `<tr><td><b>${anToan(x.tieuDe)}</b><span>${anToan(x.ma)}</span></td><td>${dinhDangNgay(x.ngayMuon)}</td><td><b class="${quaHan ? 'warning' : ''}">${dinhDangNgay(x.hanTra)}</b></td><td><mark class="${quaHan ? 'red-mark' : 'green-mark'}">${quaHan ? 'Quá hạn' : 'Đang mượn'}</mark></td><td><button class="outline gia-han-api" data-id="${x.maChiTietPhieuMuon}" ${daGuiGiaHan ? 'disabled' : ''}>${daGuiGiaHan ? 'Đã gửi gia hạn' : 'Gia hạn'}</button></td></tr>`;
    }).join('') || '<tr><td colspan="5">Bạn chưa mượn tài liệu nào.</td></tr>';
    document.querySelectorAll('.gia-han-api').forEach(b => b.onclick = async () => {
      const r = await fetch('api/docGia.php?hanhDong=giaHan', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          maChiTietPhieuMuon: b.dataset.id
        })
      });
      const d = await r.json();
      if (d.thanhCong) { window.hienThongBao?.('Đã gửi yêu cầu gia hạn.'); await napMuon(); }
      else window.hienThongBao?.(d.loi || 'Không thể gửi yêu cầu gia hạn.');
    });
  };
  const napLichSu = async () => {
    const tbody = document.querySelector('#du-lieu-lich-su');
    if (!tbody) return;
    const ds = await goiApi('api/docGia.php?hanhDong=lichSuHoatDong');
    const tinhTrang = {
      nguyenven: 'Nguyên vẹn',
      huhongnhe: 'Hư hỏng nhẹ',
      huhongnang: 'Hư hỏng nặng',
      'nguyên vẹn': 'Nguyên vẹn',
      'hư hỏng nhẹ': 'Hư hỏng nhẹ',
      'hư hỏng nặng': 'Hư hỏng nặng'
    };
    tbody.innerHTML = ds.map(x => {
      const biTuChoi = x.trangThaiHoatDong === 'tuChoi';
      const maTinhTrang = String(x.ghiChu || '').trim().toLowerCase();
      const ghiChu = biTuChoi ? (x.ghiChu || 'Không nêu lý do.') : (tinhTrang[maTinhTrang] || 'Chưa ghi nhận');
      return `<tr><td><b>${anToan(x.tieuDe)}</b><span>${anToan(x.ma)}</span></td><td>${dinhDangNgay(x.ngayHoatDong)}</td><td>${dinhDangNgay(x.ngayTra) || '—'}</td><td><mark class="${biTuChoi ? 'red-mark' : 'green-mark'}">${biTuChoi ? 'Từ chối' : 'Đã trả'}</mark></td><td>${anToan(ghiChu)}</td></tr>`;
    }).join('') || '<tr><td colspan="5">Chưa có lịch sử hoạt động mượn.</td></tr>';
  };
  const napYeuCau = async () => {
    const khung = document.querySelector('#du-lieu-yeu-cau-muon');
    if (!khung) return;
    const ds = await goiApi('api/docGia.php?hanhDong=yeuCauMuon');
    khung.innerHTML = ds.map(x => `<article data-id="${x.maYeuCauMuon}"><div class="bia-yeu-cau-doc-gia blue">◉</div><div class="thong-tin-yeu-cau-doc-gia"><div><b>${anToan(x.taiLieu.split('|')[0])}</b><mark class="yellow">Chờ duyệt</mark></div><h2>${anToan(x.taiLieu.split('|')[1] || 'Yêu cầu mượn')}</h2><small>Gửi lúc ${dinhDangNgay(x.ngayYeuCau)}</small></div><div class="hanh-dong-yeu-cau-doc-gia"><button class="outline huy-yeu-cau-api">Hủy yêu cầu</button></div></article>`).join('');
    document.querySelector('.trang-thai-rong-doc-gia').hidden = ds.length > 0;
    document.querySelectorAll('.huy-yeu-cau-api').forEach(b => b.onclick = async () => {
      const id = b.closest('article').dataset.id;
      await fetch('api/docGia.php?hanhDong=huyYeuCau', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          maYeuCauMuon: id
        })
      });
      napYeuCau();
    });
  };
  Promise.all([napTongQuan(), napTaiLieu(), napMuon(), napLichSu(), napYeuCau(), napHoSo()]).catch(loi => console.warn(loi.message));
})();
