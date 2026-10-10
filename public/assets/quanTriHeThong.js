(() => {
  if (document.body.dataset.role !== 'admin') return;
  const anToan = giaTri => String(giaTri ?? '').replace(/[&<>'"]/g, kyTu => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    "'": '&#39;',
    '"': '&quot;'
  })[kyTu]);
  async function goiApi(duongDan, tuyChon = {}) {
    const phanHoi = await fetch(duongDan, tuyChon);
    const duLieu = await phanHoi.json().catch(() => ({}));
    if (!phanHoi.ok) throw new Error(duLieu.loi || 'Không thể thực hiện thao tác.');
    return duLieu;
  }
  const thongBao = noiDung => window.hienThongBao?.(noiDung);

  const chiSoTaiKhoan = document.querySelector('#chi-so-tai-khoan');
  if (chiSoTaiKhoan) {
    const nhatKy = document.querySelector('#nhat-ky-quan-tri');
    const canhBao = document.querySelector('#danh-sach-canh-bao-quan-tri');
    goiApi('api/quanTriHeThong.php?loai=tongQuan').then(ketQua => {
      const duLieu = ketQua.duLieu;
      chiSoTaiKhoan.textContent = duLieu.taiKhoan;
      document.querySelector('#ghi-chu-tai-khoan').textContent = `${duLieu.taiKhoanHoatDong} đang hoạt động`;
      document.querySelector('#chi-so-tai-lieu').textContent = duLieu.taiLieu;
      document.querySelector('#chi-so-yeu-cau').textContent = duLieu.yeuCauChoDuyet;
      document.querySelector('#ghi-chu-yeu-cau').textContent = duLieu.yeuCauChoDuyet ? 'Thủ thư cần xử lý' : 'Không có yêu cầu mới';
      document.querySelector('#chi-so-can-xu-ly').textContent = duLieu.taiKhoanTamKhoa;
      document.querySelector('#ghi-chu-can-xu-ly').textContent = duLieu.taiKhoanTamKhoa ? 'Cần kiểm tra hoặc mở khóa' : 'Không có tài khoản bị khóa';
      document.querySelector('#chi-so-danh-muc').textContent = `${duLieu.danhMuc} khoa`;
      nhatKy.innerHTML = ketQua.nhatKy.length ? ketQua.nhatKy.map(muc => `<div><i>✓</i><p><b>${anToan(muc.hoTen || 'Hệ thống')}</b> đã ${anToan(muc.hanhDong)}.<small>${anToan(muc.ngayTao)}</small></p></div>`).join('') : '<p>Chưa có nhật ký hoạt động.</p>';
      canhBao.innerHTML = ketQua.canhBao.length ? ketQua.canhBao.map(muc => `<div><span>${anToan(muc.soLuong)}</span><p>${anToan(muc.noiDung)}</p></div>`).join('') : '<p>Không có việc nào cần lưu ý.</p>';
    }).catch(loi => {
      nhatKy.textContent = loi.message;
      canhBao.textContent = loi.message;
    });
  }

  const formDanhMuc = document.querySelector('#form-danh-muc');
  if (formDanhMuc) {
    const danhSach = document.querySelector('#danh-sach-danh-muc');
    const nutHuy = document.querySelector('#huy-sua-danh-muc');
    const tieuDe = document.querySelector('#tieu-de-form-danh-muc');
    async function taiDanhMuc() {
      try {
        const ketQua = await goiApi('api/quanTriHeThong.php?loai=danhMuc');
      danhSach.innerHTML = ketQua.duLieu.map((danhMuc, chiSo) => `<article><i class="${['xanh','cam','tim','do'][chiSo % 4]}">⌘</i><div><b>${anToan(danhMuc.ten)}</b><span>${anToan(danhMuc.ma)} · ${danhMuc.soTaiLieu} tài liệu</span></div><button class="sua-danh-muc" type="button" data-id="${danhMuc.maDanhMuc}" data-ma="${anToan(danhMuc.ma)}" data-ten="${anToan(danhMuc.ten)}">Sửa</button><button class="xoa-danh-muc" type="button" data-id="${danhMuc.maDanhMuc}" data-ten="${anToan(danhMuc.ten)}">Xóa</button></article>`).join('') || '<p>Chưa có khoa.</p>';
      } catch (loi) {
        danhSach.innerHTML = `<p>${anToan(loi.message)}</p>`;
      }
    }
    formDanhMuc.addEventListener('submit', async suKien => {
      suKien.preventDefault();
      try {
        await goiApi('api/quanTriHeThong.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            hanhDong: 'luuDanhMuc',
            ...Object.fromEntries(new FormData(formDanhMuc))
          })
        });
        formDanhMuc.reset();
        tieuDe.textContent = 'Thêm khoa';
        nutHuy.hidden = true;
        await taiDanhMuc();
        thongBao('Đã lưu khoa.');
      } catch (loi) {
        alert(loi.message);
      }
    });
    nutHuy.addEventListener('click', () => {
      formDanhMuc.reset();
      tieuDe.textContent = 'Thêm khoa';
      nutHuy.hidden = true;
    });
    danhSach.addEventListener('click', async suKien => {
      const nutSua = suKien.target.closest('.sua-danh-muc'),
        nutXoa = suKien.target.closest('.xoa-danh-muc');
      if (nutSua) {
        formDanhMuc.maDanhMuc.value = nutSua.dataset.id;
        formDanhMuc.ma.value = nutSua.dataset.ma;
        formDanhMuc.ten.value = nutSua.dataset.ten;
        tieuDe.textContent = 'Sửa khoa';
        nutHuy.hidden = false;
        formDanhMuc.ma.focus();
      }
      if (nutXoa && confirm(`Xóa khoa ${nutXoa.dataset.ten}?`)) {
        try {
          await goiApi('api/quanTriHeThong.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify({
              hanhDong: 'xoaDanhMuc',
              maDanhMuc: nutXoa.dataset.id
            })
          });
          await taiDanhMuc();
          thongBao('Đã xóa khoa.');
        } catch (loi) {
          alert(loi.message);
        }
      }
    });
    taiDanhMuc();
  }

  const formChinhSach = document.querySelector('#form-chinh-sach');
  if (formChinhSach) {
    const danhSach = document.querySelector('#danh-sach-chinh-sach');
    let chinhSach = [];
    const dinhDangTien = giaTri => {
      const chuoi = String(giaTri ?? '').trim();
      const laSoThapPhanTuCSDL = /^\d+\.\d{1,2}$/.test(chuoi);
      const so = laSoThapPhanTuCSDL ? Math.round(Number(chuoi)) : Number(chuoi.replace(/\D/g, ''));
      return Number.isFinite(so) && so > 0 ? so.toLocaleString('vi-VN') : '';
    };

    formChinhSach.querySelectorAll('[data-tien]').forEach(oNhap => oNhap.addEventListener('input', () => {
      oNhap.value = dinhDangTien(oNhap.value);
    }));

    function apDungChinhSach() {
      const hienTai = chinhSach.find(muc => muc.loaiDocGia === formChinhSach.loaiDocGia.value && muc.trangThai === 'dangApDung');
      if (!hienTai) return formChinhSach.reset();
      ['soSachToiDa', 'soNgayMuon', 'soLanGiaHan', 'soNgayGiaHan'].forEach(ten => formChinhSach[ten].value = hienTai[ten]);
      ['tienPhatMoiNgay', 'tienPhatHuHongNhe', 'tienPhatHuHongNang'].forEach(ten => formChinhSach[ten].value = dinhDangTien(hienTai[ten] ?? 0));
    }
    async function taiChinhSach() {
      try {
        const ketQua = await goiApi('api/quanTriHeThong.php?loai=chinhSach');
        chinhSach = ketQua.duLieu;
        apDungChinhSach();
        danhSach.innerHTML = chinhSach.map(muc => `<div class="the-lich-su-chinh-sach"><i>◈</i><p><b>${muc.loaiDocGia === 'sinhVien' ? 'Sinh viên' : 'Giảng viên'} · ${muc.soSachToiDa} sách / ${muc.soNgayMuon} ngày</b><span>${muc.soLanGiaHan} lần gia hạn · ${muc.soNgayGiaHan} ngày/lần · Hư nhẹ: ${Number(muc.tienPhatHuHongNhe || 0).toLocaleString('vi-VN')}đ · Hư nặng: ${Number(muc.tienPhatHuHongNang || 0).toLocaleString('vi-VN')}đ</span></p><small>${muc.trangThai === 'dangApDung' ? 'Đang áp dụng' : 'Ngừng áp dụng'}<br>${muc.ngayApDung}</small></div>`).join('');
      } catch (loi) {
        danhSach.textContent = loi.message;
      }
    }
    formChinhSach.loaiDocGia.addEventListener('change', apDungChinhSach);
    formChinhSach.addEventListener('submit', async suKien => {
      suKien.preventDefault();
      try {
        const duLieu = Object.fromEntries(new FormData(formChinhSach));
        ['tienPhatMoiNgay', 'tienPhatHuHongNhe', 'tienPhatHuHongNang'].forEach(ten => duLieu[ten] = String(duLieu[ten] ?? '').replace(/\./g, ''));
        await goiApi('api/quanTriHeThong.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            hanhDong: 'luuChinhSach',
            ...duLieu
          })
        });
        await taiChinhSach();
        thongBao('Đã lưu phiên bản chính sách mới.');
      } catch (loi) {
        alert(loi.message);
      }
    });
    taiChinhSach();
  }
})();
