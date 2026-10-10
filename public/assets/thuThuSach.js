(() => {
  if (document.body.dataset.role !== 'librarian') return;
  const bangSach = document.querySelector('#du-lieu-sach');
  const bangNxb = document.querySelector('#du-lieu-nxb');
  const hopSach = document.querySelector('#hop-sach-thu-thu');
  const hopNxb = document.querySelector('#hop-nxb-thu-thu');
  const formSach = document.querySelector('#form-sach-thu-thu');
  const formNxb = document.querySelector('#form-nxb-thu-thu');
  const hopTomTat = document.querySelector('#hop-tom-tat-thu-thu');
  const formTomTat = document.querySelector('#form-tom-tat-thu-thu');
  const chonKhoa = document.querySelector('#danh-muc-sach');
  const chonNxb = document.querySelector('#nha-xuat-ban-sach');
  const timSach = document.querySelector('#inventory-search');
  const locKhoaSach = document.querySelector('#loc-danh-muc-sach');
  const anToan = value => String(value ?? '').replace(/[&<>"']/g, kyTu => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[kyTu]));
  let danhSachSach = [];
  let danhSachNxb = [];
  let danhSachTomTat = [];
  let trangSach = 1;
  let trangNxb = 1;
  let chonNxbSauKhiLuu = false;

  const goi = async (url, tuyChon = {}) => {
    const phanHoi = await fetch(url, tuyChon);
    const duLieu = await phanHoi.json();
    if (!phanHoi.ok) throw Error(duLieu.loi || 'Không thể xử lý yêu cầu.');
    return duLieu;
  };
  const gui = (hanhDong, duLieu) => {
    if (duLieu instanceof FormData) {
      duLieu.set('hanhDong', hanhDong);
      return goi('api/thuThuSach.php', { method: 'POST', body: duLieu });
    }
    return goi('api/thuThuSach.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ hanhDong, ...duLieu }) });
  };

  const vePhanTrang = (idThongBao, idNut, trang, tong, gioiHan, tai) => {
    const tongTrang = Math.max(1, Math.ceil(tong / gioiHan));
    const batDau = tong ? (trang - 1) * gioiHan + 1 : 0;
    const ketThuc = Math.min(trang * gioiHan, tong);
    document.querySelector(idThongBao).textContent = `Hiển thị ${batDau}–${ketThuc} trên ${tong} kết quả`;
    document.querySelector(idNut).innerHTML = `<button class="outline" type="button" ${trang === 1 ? 'disabled' : ''}>← Trước</button><span>Trang ${trang}/${tongTrang}</span><button class="outline" type="button" ${trang === tongTrang ? 'disabled' : ''}>Sau →</button>`;
    const nut = document.querySelector(idNut).querySelectorAll('button');
    nut[0].onclick = () => tai(trang - 1);
    nut[1].onclick = () => tai(trang + 1);
  };
  const taiNxb = async (trang = 1, maCanChon = null) => {
    trangNxb = trang;
    const tuKhoa = document.querySelector('#tim-nxb')?.value || '';
    const ketQua = await goi(`api/thuThuSach.php?hanhDong=nhaXuatBan&trang=${trang}&gioiHan=6&tuKhoa=${encodeURIComponent(tuKhoa)}`);
    danhSachNxb = ketQua.duLieu;
    veNxb();
    if (bangNxb) vePhanTrang('#phan-trang-nxb', '#nut-phan-trang-nxb', ketQua.trang, ketQua.tong, ketQua.gioiHan, taiNxb);
  };
  const taiLuaChonNxb = async maCanChon => {
    if (!chonNxb) return;
    const ketQua = await goi('api/thuThuSach.php?hanhDong=nhaXuatBan&layTatCa=1');
    chonNxb.innerHTML = '<option value="">Chọn nhà xuất bản</option>' + ketQua.duLieu.map(nxb => `<option value="${nxb.maNhaXuatBan}">${anToan(nxb.ten)}</option>`).join('');
    if (maCanChon) chonNxb.value = maCanChon;
  };
  const veNxb = () => {
    if (!bangNxb) return;
    bangNxb.innerHTML = danhSachNxb.map(nxb => `<tr><td><b>${anToan(nxb.ma)}</b></td><td>${anToan(nxb.ten)}</td><td>${anToan(nxb.diaChi || '—')}</td><td>${nxb.soTaiLieu}</td><td><button class="table-action sua-nxb" data-id="${nxb.maNhaXuatBan}">Sửa</button><button class="table-action xoa-nxb" data-id="${nxb.maNhaXuatBan}">Xóa</button></td></tr>`).join('') || '<tr><td colspan="5">Chưa có nhà xuất bản.</td></tr>';
  };
  const veSach = () => {
    if (!bangSach) return;
    bangSach.innerHTML = danhSachSach.map(sach => `<tr><td><div class="table-book">${sach.anhBia ? `<img src="${anToan(sach.anhBia)}" alt="">` : '<i class="blue">▣</i>'}<div><b>${anToan(sach.tieuDe)}</b><span>${anToan(sach.nhaXuatBan || 'Chưa có nhà xuất bản')}</span></div></div></td><td>${anToan(sach.ma)}</td><td>${anToan(sach.khoa || '')}</td><td>${sach.soBanSanSang} / ${sach.tongBan} bản</td><td><mark class="${sach.soBanSanSang ? 'green-mark' : 'red-mark'}">${sach.soBanSanSang ? 'Sẵn sàng' : 'Hết sách'}</mark></td><td><button class="table-action edit-sach" data-id="${sach.maTaiLieu}">Sửa</button><button class="table-action xoa-sach" data-id="${sach.maTaiLieu}">Xóa</button></td></tr>`).join('') || '<tr><td colspan="6">Chưa có tài liệu.</td></tr>';
  };
  const veDanhSachTomTat = () => {
    if (!formTomTat) return;
    const tuKhoa = document.querySelector('#tim-sach-tom-tat').value.toLocaleLowerCase('vi').trim();
    const khoa = document.querySelector('#loc-khoa-tom-tat').value;
    const danhSach = danhSachTomTat.filter(sach => (`${sach.ma} ${sach.tieuDe} ${sach.nhaXuatBan || ''}`.toLocaleLowerCase('vi').includes(tuKhoa)) && (!khoa || sach.khoa === khoa));
    const maDaChon = String(formTomTat.maTaiLieu.value || '');
    document.querySelector('#danh-sach-sach-tom-tat').innerHTML = danhSach.map(sach => `<button class="muc-sach-tom-tat${String(sach.maTaiLieu) === maDaChon ? ' duoc-chon' : ''}" type="button" data-id="${sach.maTaiLieu}"><b>${anToan(sach.tieuDe)}</b><small>${anToan(sach.ma)} · ${anToan(sach.khoa || '')}</small></button>`).join('') || '<p>Không tìm thấy tài liệu phù hợp.</p>';
  };
  const taiSach = async (trang = 1) => {
    if (!bangSach) return;
    trangSach = trang;
    const ketQua = await goi(`api/thuThuSach.php?trang=${trang}&gioiHan=6&tuKhoa=${encodeURIComponent(timSach.value)}&maKhoa=${locKhoaSach.value}`);
    danhSachSach = ketQua.duLieu;
    veSach();
    vePhanTrang('#phan-trang-sach', '#nut-phan-trang-sach', ketQua.trang, ketQua.tong, ketQua.gioiHan, taiSach);
    window.dispatchEvent(new CustomEvent('thu-thu-sach-da-cap-nhat'));
  };
  const moFormNxb = (nxb = null, chonSauKhiLuu = false) => {
    formNxb.reset();
    formNxb.maNhaXuatBan.value = nxb?.maNhaXuatBan || '';
    formNxb.ma.value = nxb?.ma || '';
    formNxb.ten.value = nxb?.ten || '';
    formNxb.diaChi.value = nxb?.diaChi || '';
    formNxb.soDienThoai.value = nxb?.soDienThoai || '';
    document.querySelector('#tieu-de-form-nxb').textContent = nxb ? 'Sửa nhà xuất bản' : 'Thêm nhà xuất bản';
    document.querySelector('#loi-form-nxb').hidden = true;
    chonNxbSauKhiLuu = chonSauKhiLuu;
    hopNxb.classList.add('mo');
    hopNxb.setAttribute('aria-hidden', 'false');
  };

  document.querySelector('#mo-form-sach')?.addEventListener('click', () => { formSach.reset(); document.querySelector('#loi-form-sach').hidden = true; hopSach.classList.add('mo'); });
  document.querySelectorAll('.dong-hop-sach').forEach(nut => nut.onclick = () => hopSach.classList.remove('mo'));
  document.querySelector('#mo-form-nxb')?.addEventListener('click', () => moFormNxb());
  document.querySelector('#mo-form-nxb-nhanh')?.addEventListener('click', () => moFormNxb(null, true));
  document.querySelector('#mo-form-tom-tat')?.addEventListener('click', async () => {
    formTomTat.reset();
    danhSachTomTat = (await goi('api/thuThuSach.php?layTatCa=1')).duLieu;
    const loc = document.querySelector('#loc-khoa-tom-tat');
    loc.innerHTML = '<option value="">Tất cả khoa</option>' + [...new Set(danhSachTomTat.map(sach => sach.khoa).filter(Boolean))].map(khoa => `<option value="${anToan(khoa)}">${anToan(khoa)}</option>`).join('');
    veDanhSachTomTat();
    document.querySelector('#loi-form-tom-tat').hidden = true;
    hopTomTat.classList.add('mo');
  });
  document.querySelectorAll('.dong-hop-tom-tat').forEach(nut => nut.onclick = () => hopTomTat.classList.remove('mo'));
  document.querySelector('#tim-sach-tom-tat')?.addEventListener('input', veDanhSachTomTat);
  document.querySelector('#loc-khoa-tom-tat')?.addEventListener('change', veDanhSachTomTat);
  document.querySelector('#danh-sach-sach-tom-tat')?.addEventListener('click', suKien => {
    const nut = suKien.target.closest('.muc-sach-tom-tat');
    if (!nut) return;
    const sach = danhSachTomTat.find(item => item.maTaiLieu == nut.dataset.id);
    if (!sach) return;
    formTomTat.maTaiLieu.value = sach.maTaiLieu;
    formTomTat.tomTat.value = sach.tomTat || '';
    veDanhSachTomTat();
  });
  document.querySelectorAll('.dong-hop-nxb').forEach(nut => nut.onclick = () => hopNxb.classList.remove('mo'));
  document.querySelector('#tim-nxb')?.addEventListener('input', () => taiNxb(1));
  timSach?.addEventListener('input', () => taiSach(1));
  locKhoaSach?.addEventListener('change', () => taiSach(1));

  bangSach?.addEventListener('click', async suKien => {
    const nut = suKien.target.closest('.edit-sach,.xoa-sach');
    if (!nut) return;
    const sach = danhSachSach.find(item => item.maTaiLieu == nut.dataset.id);
    if (nut.classList.contains('xoa-sach')) {
      if (!confirm('Xóa tài liệu này?')) return;
      try { await gui('xoa', { maTaiLieu: sach.maTaiLieu }); await taiSach(); } catch (loi) { alert(loi.message); }
      return;
    }
    formSach.reset();
    formSach.maTaiLieu.value = sach.maTaiLieu;
    formSach.ma.value = sach.ma;
    formSach.tieuDe.value = sach.tieuDe;
    formSach.maDanhMuc.value = Array.from(chonKhoa.options).find(option => option.textContent === sach.khoa)?.value || '';
    formSach.maNhaXuatBan.value = sach.maNhaXuatBan || '';
    formSach.namXuatBan.value = sach.namXuatBan || '';
    formSach.maIsbn.value = sach.maIsbn || '';
    formSach.anhBiaHienTai.value = sach.anhBia || '';
    formSach.soLuongTong.value = sach.tongBan || 0;
    document.querySelector('#loi-form-sach').hidden = true;
    hopSach.classList.add('mo');
  });
  bangNxb?.addEventListener('click', async suKien => {
    const nut = suKien.target.closest('.sua-nxb,.xoa-nxb');
    if (!nut) return;
    const nxb = danhSachNxb.find(item => item.maNhaXuatBan == nut.dataset.id);
    if (nut.classList.contains('sua-nxb')) return moFormNxb(nxb);
    if (!confirm(`Xóa nhà xuất bản ${nxb.ten}?`)) return;
    try { await gui('xoaNhaXuatBan', { maNhaXuatBan: nxb.maNhaXuatBan }); await taiNxb(1); } catch (loi) { alert(loi.message); }
  });
  formNxb.onsubmit = async suKien => {
    suKien.preventDefault();
    try {
      const ketQua = await gui('luuNhaXuatBan', Object.fromEntries(new FormData(formNxb)));
      hopNxb.classList.remove('mo');
      await taiLuaChonNxb(chonNxbSauKhiLuu ? ketQua.maNhaXuatBan : null);
      await taiNxb(1);
      window.hienThongBao?.('Đã lưu nhà xuất bản.');
    } catch (loi) { const o = document.querySelector('#loi-form-nxb'); o.textContent = loi.message; o.hidden = false; }
  };
  formSach.onsubmit = async suKien => {
    suKien.preventDefault();
    try { await gui('luu', new FormData(formSach)); hopSach.classList.remove('mo'); await taiSach(); window.hienThongBao?.('Đã lưu tài liệu.'); }
    catch (loi) { const o = document.querySelector('#loi-form-sach'); o.textContent = loi.message; o.hidden = false; }
  };
  formTomTat.onsubmit = async suKien => {
    suKien.preventDefault();
    try {
      await gui('luuTomTat', Object.fromEntries(new FormData(formTomTat)));
      const sach = danhSachTomTat.find(item => item.maTaiLieu == formTomTat.maTaiLieu.value);
      if (sach) sach.tomTat = formTomTat.tomTat.value;
      hopTomTat.classList.remove('mo');
      window.hienThongBao?.('Đã lưu tóm tắt.');
    } catch (loi) { const o = document.querySelector('#loi-form-tom-tat'); o.textContent = loi.message; o.hidden = false; }
  };

  Promise.all([goi('api/thuThuSach.php?hanhDong=danhMuc'), goi('api/thuThuSach.php?hanhDong=nhaXuatBan&layTatCa=1')]).then(([khoa, nxb]) => {
    chonKhoa.innerHTML = khoa.duLieu.map(item => `<option value="${item.maDanhMuc}">${anToan(item.ten)}</option>`).join('');
    locKhoaSach.innerHTML = '<option value="">Tất cả khoa</option>' + khoa.duLieu.map(item => `<option value="${item.maDanhMuc}">${anToan(item.ten)}</option>`).join('');
    danhSachNxb = nxb.duLieu;
    if (chonNxb) chonNxb.innerHTML = '<option value="">Chọn nhà xuất bản</option>' + danhSachNxb.map(item => `<option value="${item.maNhaXuatBan}">${anToan(item.ten)}</option>`).join('');
    taiNxb();
    taiSach();
  }).catch(loi => { if (bangSach) bangSach.innerHTML = `<tr><td colspan="6">${anToan(loi.message)}</td></tr>`; });
})();
