(() => {
  const bang = document.querySelector('#du-lieu-tai-khoan');
  if (!bang || document.body.dataset.role !== 'admin') return;

  const hop = document.querySelector('#hop-tai-khoan-quan-tri');
  const form = document.querySelector('#form-tai-khoan-quan-tri');
  const truongTao = document.querySelector('#truong-tao-tai-khoan');
  const truongLoaiDocGia = document.querySelector('#truong-loai-doc-gia');
  const tieuDe = document.querySelector('#tieu-de-hop-tai-khoan');
  const moTa = document.querySelector('#mo-ta-hop-tai-khoan');
  const nutLuu = document.querySelector('#nut-luu-tai-khoan');
  const loi = document.querySelector('#loi-tai-khoan-quan-tri');
  const tong = document.querySelector('#tong-tai-khoan');
  const oTim = document.querySelector('#tim-tai-khoan');
  const locVaiTro = document.querySelector('#loc-vai-tro');
  let danhSach = [];

  const anToan = giaTri => String(giaTri ?? '').replace(/[&<>'"]/g, kyTu => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    "'": '&#39;',
    '"': '&quot;'
  })[kyTu]);
  const nhanVaiTro = ma => ({
    ADMIN: 'Quản trị viên',
    THUTHU: 'Thủ thư',
    DOCGIA: 'Độc giả'
  })[ma] || ma;
  const lopVaiTro = ma => ({
    ADMIN: 'quan-tri',
    THUTHU: 'thu-thu',
    DOCGIA: 'doc-gia'
  })[ma] || '';
  const nhanTrangThai = ma => ma === 'hoatDong' ? 'Hoạt động' : 'Tạm khóa';

  async function goiApi(duongDan, tuyChon = {}) {
    const phanHoi = await fetch(duongDan, tuyChon);
    const duLieu = await phanHoi.json().catch(() => ({}));
    if (!phanHoi.ok) throw new Error(duLieu.loi || 'Không thể thực hiện thao tác.');
    return duLieu;
  }

  function veBang() {
    const tuKhoa = (oTim.value || '').trim().toLocaleLowerCase('vi');
    const vaiTro = locVaiTro.value;
    const hienThi = danhSach.filter(taiKhoan => {
      const noiDung = `${taiKhoan.tenDangNhap} ${taiKhoan.hoTen} ${taiKhoan.thuDienTu || ''}`.toLocaleLowerCase('vi');
      return (!tuKhoa || noiDung.includes(tuKhoa)) && (!vaiTro || taiKhoan.maVaiTro === vaiTro);
    });
    tong.textContent = `Hiển thị ${hienThi.length} trên ${danhSach.length} tài khoản`;
    bang.innerHTML = hienThi.length ? hienThi.map(taiKhoan => `
      <tr data-tai-khoan="${anToan(`${taiKhoan.tenDangNhap} ${taiKhoan.hoTen} ${taiKhoan.thuDienTu || ''}`)}">
        <td><b>${anToan(taiKhoan.hoTen)}</b><span>${anToan(taiKhoan.tenDangNhap)}</span></td>
        <td><mark class="vai-tro ${lopVaiTro(taiKhoan.maVaiTro)}">${nhanVaiTro(taiKhoan.maVaiTro)}</mark></td>
        <td>${anToan(taiKhoan.thuDienTu || 'Chưa cập nhật')}</td>
        <td><mark class="${taiKhoan.trangThai === 'hoatDong' ? 'green-mark' : 'red-mark'}">${nhanTrangThai(taiKhoan.trangThai)}</mark></td>
        <td><button class="nut-bang-quan-tri cap-lai-mat-khau" type="button" data-id="${taiKhoan.maNguoiDung}" data-ten="${anToan(taiKhoan.hoTen)}">Cấp lại mật khẩu</button><button class="nut-bang-quan-tri doi-trang-thai" type="button" data-id="${taiKhoan.maNguoiDung}" data-trang-thai="${taiKhoan.trangThai}">${taiKhoan.trangThai === 'hoatDong' ? 'Tạm khóa' : 'Mở khóa'}</button>${taiKhoan.maVaiTro === 'ADMIN' ? '' : `<button class="nut-bang-quan-tri xoa-tai-khoan" type="button" data-id="${taiKhoan.maNguoiDung}" data-ten="${anToan(taiKhoan.hoTen)}">Xóa</button>`}</td>
      </tr>`).join('') : '<tr><td colspan="5">Không tìm thấy tài khoản phù hợp.</td></tr>';
  }

  async function taiDanhSach() {
    try {
      const ketQua = await goiApi('api/quanTriTaiKhoan.php');
      danhSach = ketQua.duLieu || [];
      veBang();
    } catch (loi) {
      bang.innerHTML = `<tr><td colspan="5">${anToan(loi.message)}</td></tr>`;
      tong.textContent = 'Không tải được dữ liệu';
    }
  }

  function moHopTao() {
    form.reset();
    form.hanhDong.value = 'tao';
    form.maNguoiDung.value = '';
    truongTao.hidden = false;
    capNhatLoaiDocGia();
    tieuDe.textContent = 'Tạo tài khoản';
    moTa.textContent = 'Tạo tài khoản mới cho độc giả hoặc thủ thư. Mã đăng nhập là mã số do quản trị viên cấp.';
    nutLuu.innerHTML = 'Tạo tài khoản <span>→</span>';
    loi.hidden = true;
    hop.classList.add('mo');
    hop.setAttribute('aria-hidden', 'false');
    form.tenDangNhap.focus();
  }

  function capNhatLoaiDocGia() {
    truongLoaiDocGia.hidden = form.maVaiTro.value !== 'DOCGIA';
  }

  function moHopCapLai(nut) {
    form.reset();
    form.hanhDong.value = 'capLaiMatKhau';
    form.maNguoiDung.value = nut.dataset.id;
    truongTao.hidden = true;
    tieuDe.textContent = 'Cấp lại mật khẩu';
    moTa.textContent = `Đặt mật khẩu mới cho tài khoản ${nut.dataset.ten}. Người dùng sẽ dùng mật khẩu này ở lần đăng nhập tiếp theo.`;
    nutLuu.innerHTML = 'Lưu mật khẩu <span>→</span>';
    loi.hidden = true;
    hop.classList.add('mo');
    hop.setAttribute('aria-hidden', 'false');
    form.matKhau.focus();
  }

  function dongHop() {
    hop.classList.remove('mo');
    hop.setAttribute('aria-hidden', 'true');
  }

  document.querySelector('#mo-tao-tai-khoan').addEventListener('click', moHopTao);
  form.maVaiTro.addEventListener('change', capNhatLoaiDocGia);
  document.querySelectorAll('.dong-hop-tai-khoan').forEach(nut => nut.addEventListener('click', dongHop));
  hop.addEventListener('click', suKien => {
    if (suKien.target === hop) dongHop();
  });
  oTim.addEventListener('input', veBang);
  locVaiTro.addEventListener('change', veBang);
  bang.addEventListener('click', async suKien => {
    const nutCapLai = suKien.target.closest('.cap-lai-mat-khau');
    if (nutCapLai) return moHopCapLai(nutCapLai);
    const nutTrangThai = suKien.target.closest('.doi-trang-thai');
    const nutXoa = suKien.target.closest('.xoa-tai-khoan');
    if (nutXoa) {
      if (!confirm(`Xóa tài khoản ${nutXoa.dataset.ten}? Thao tác này không thể hoàn tác.`)) return;
      try {
        await goiApi('api/quanTriTaiKhoan.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            hanhDong: 'xoa',
            maNguoiDung: nutXoa.dataset.id
          })
        });
        await taiDanhSach();
        window.hienThongBao?.('Đã xóa tài khoản.');
      } catch (loi) {
        alert(loi.message);
      }
      return;
    }
    if (!nutTrangThai) return;
    const trangThaiMoi = nutTrangThai.dataset.trangThai === 'hoatDong' ? 'tamKhoa' : 'hoatDong';
    if (!confirm(`${trangThaiMoi === 'tamKhoa' ? 'Tạm khóa' : 'Mở khóa'} tài khoản này?`)) return;
    try {
      await goiApi('api/quanTriTaiKhoan.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          hanhDong: 'capNhatTrangThai',
          maNguoiDung: nutTrangThai.dataset.id,
          trangThai: trangThaiMoi
        })
      });
      await taiDanhSach();
    } catch (loi) {
      alert(loi.message);
    }
  });
  form.addEventListener('submit', async suKien => {
    suKien.preventDefault();
    loi.hidden = true;
    const duLieu = Object.fromEntries(new FormData(form));
    try {
      await goiApi('api/quanTriTaiKhoan.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(duLieu)
      });
      dongHop();
      await taiDanhSach();
      window.hienThongBao?.(duLieu.hanhDong === 'tao' ? 'Đã tạo tài khoản.' : 'Đã cập nhật mật khẩu.');
    } catch (loiApi) {
      loi.textContent = loiApi.message;
      loi.hidden = false;
    }
  });
  document.addEventListener('keydown', suKien => {
    if (suKien.key === 'Escape') dongHop();
  });
  taiDanhSach();
})();

(() => {
  if (document.body.dataset.role !== 'admin') return;
  const form = document.querySelector('#form-doi-mat-khau-admin');
  if (!form) return;
  const loi = document.querySelector('#loi-doi-mat-khau-admin');
  form.addEventListener('submit', async suKien => {
    suKien.preventDefault();
    loi.hidden = true;
    const duLieu = Object.fromEntries(new FormData(form));
    if (duLieu.matKhauMoi !== duLieu.xacNhanMatKhau) {
      loi.textContent = 'Xác nhận mật khẩu mới chưa khớp.';
      loi.hidden = false;
      return;
    }
    const nut = form.querySelector('button[type="submit"]');
    nut.disabled = true;
    try {
      const phanHoi = await fetch('api/quanTriTaiKhoan.php', {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({hanhDong: 'doiMatKhauCuaToi', ...duLieu})});
      const ketQua = await phanHoi.json();
      if (!phanHoi.ok) throw new Error(ketQua.loi || 'Không thể đổi mật khẩu.');
      form.reset();
      window.hienThongBao?.('Đã đổi mật khẩu.');
    } catch (loiApi) {
      loi.textContent = loiApi.message;
      loi.hidden = false;
    } finally {
      nut.disabled = false;
    }
  });
})();
