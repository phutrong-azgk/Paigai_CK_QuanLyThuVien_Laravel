(() => {
    if (document.body.dataset.role !== 'librarian') return;
    const a = x => String(x ?? '').replace(/[&<>]/g, c => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;'
    } [c]));
    const g = async (u, o = {}) => {
        const r = await fetch(u, o),
            d = await r.json();
        if (!r.ok) throw Error(d.loi);
        return d
    }, p = (h, d) => g('api/thuThu.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            hanhDong: h,
            ...d
        })
    });
    const kiemTraYeuCau = yeuCau => {
        const theHopLe = yeuCau.trangThaiTaiKhoan === 'hoatDong' && yeuCau.trangThaiThe === 'hoatDong';
        const duSach = Number(yeuCau.soTaiLieuSanSang) === Number(yeuCau.soTaiLieu);
        if (!theHopLe) return '<mark class="red-mark">Thẻ không hợp lệ</mark>';
        if (!duSach) return '<mark class="red-mark">Có tài liệu hết sách</mark>';
        return '<mark class="green-mark">Đủ điều kiện</mark>';
    };
    const coTheDuyetYeuCau = yeuCau => yeuCau.trangThaiTaiKhoan === 'hoatDong' && yeuCau.trangThaiThe === 'hoatDong' && Number(yeuCau.soTaiLieuSanSang) === Number(yeuCau.soTaiLieu);
    const hienThiTongQuan = () => {
        const chiSoMuon = document.querySelector('#chi-so-muon-hom-nay');
        if (!chiSoMuon) return;
        g('api/thuThu.php?hanhDong=tongQuan').then(phanHoi => {
            const duLieu = phanHoi.duLieu || {}, soTien = Number(duLieu.chuaThu || 0).toLocaleString('vi-VN');
            chiSoMuon.textContent = duLieu.muonHomNay || 0;
            document.querySelector('#ghi-chu-muon-hom-nay').textContent = `Đang chờ duyệt: ${duLieu.choDuyet || 0}`;
            document.querySelector('#chi-so-tra-hom-nay').textContent = duLieu.traHomNay || 0;
            document.querySelector('#ghi-chu-tra-hom-nay').textContent = duLieu.traHomNay ? 'Đã tiếp nhận trong hôm nay' : 'Chưa có sách trả hôm nay';
            document.querySelector('#chi-so-qua-han').textContent = duLieu.quaHan || 0;
            document.querySelector('#ghi-chu-qua-han').textContent = duLieu.quaHan ? 'Cần gửi nhắc nhở' : 'Không có tài liệu quá hạn';
            document.querySelector('#chi-so-chua-thu').textContent = `${soTien}đ`;
            document.querySelector('#ghi-chu-chua-thu').textContent = `${duLieu.docGiaPhat || 0} độc giả chưa thanh toán`;
            document.querySelector('#ghi-chu-xu-ly-qua-han').textContent = `${duLieu.quaHan || 0} tài liệu cần nhắc`;

            const danhSach = document.querySelector('#danh-sach-den-han'), homNay = new Date();
            homNay.setHours(0, 0, 0, 0);
            if (!phanHoi.denHan?.length) {
                danhSach.innerHTML = '<p>Không có tài liệu đến hạn trong 3 ngày tới.</p>';
                return;
            }
            danhSach.innerHTML = phanHoi.denHan.map(muc => {
                const ngayHan = new Date(`${muc.hanTra}T00:00:00`), chenLech = Math.round((ngayHan - homNay) / 86400000), ngay = String(ngayHan.getDate()).padStart(2, '0'), thang = String(ngayHan.getMonth() + 1).padStart(2, '0');
                const nhan = chenLech < 0 ? 'Quá hạn' : chenLech === 0 ? 'Hôm nay' : `Còn ${chenLech} ngày`;
                return `<div class="due-item"><div class="due-date"><b>${ngay}</b><small>Th${thang}</small></div><div><b>${a(muc.tieuDe)}</b><small>${a(muc.hoTen)} · ${a(muc.maSo)}</small></div><em>${nhan}</em></div>`;
            }).join('');
        }).catch(() => {
            chiSoMuon.textContent = '—';
            document.querySelector('#ghi-chu-muon-hom-nay').textContent = 'Không tải được dữ liệu';
        });
    };
    hienThiTongQuan();
    const hopChon = document.querySelector('#hop-chon-tai-lieu');
    const danhSachChon = document.querySelector('#danh-sach-chon-tai-lieu');
    let docGiaMuon = null, taiLieuDaChon = [], taiLieuTraDaChon = [], loaiChon = 'muon', duLieuChon = [];
    const dinhDangNgay = ngay => ngay ? new Date(ngay).toLocaleDateString('vi-VN') : '—';
    const vePhieuMuonTam = () => {
        const khung = document.querySelector('#danh-sach-tai-lieu-muon-tam');
        if (!khung) return;
        const soToiDa = docGiaMuon?.soSachToiDa || '—';
        document.querySelector('#so-luong-muon-tam').textContent = `${String(taiLieuDaChon.length).padStart(2, '0')} / ${soToiDa}`;
        document.querySelector('#thoi-han-muon-tam').textContent = docGiaMuon ? `${docGiaMuon.soNgayMuon} ngày` : '— ngày';
        document.querySelector('#xac-nhan-cho-muon').disabled = !docGiaMuon || !taiLieuDaChon.length;
        khung.innerHTML = taiLieuDaChon.map(sach => `<div class="ticket-book"><i class="blue">◫</i><div><b>${a(sach.tieuDe)}</b><span>${a(sach.ma)} · Còn ${sach.soLuongCon} bản</span></div><button class="bo-tai-lieu-muon" type="button" data-id="${sach.maTaiLieu}">×</button></div>`).join('') || '<p>Chưa chọn tài liệu.</p>';
    };
    const capNhatPhiTraTam = async () => {
        const hienThiPhi = document.querySelector('#phi-tra-tam');
        if (!hienThiPhi) return;
        if (!taiLieuTraDaChon.length) {
            hienThiPhi.textContent = '0đ';
            return;
        }
        hienThiPhi.textContent = 'Đang tính...';
        try {
            const maChiTiet = taiLieuTraDaChon.map(sach => sach.maChiTietPhieuMuon).join(',');
            const tinhTrang = document.querySelector('#tinh-trang-khi-tra')?.value || 'nguyenVen';
            const phanHoi = await g(`api/thuThu.php?hanhDong=uocTinhPhatTra&maChiTietPhieuMuon=${encodeURIComponent(maChiTiet)}&tinhTrangKhiTra=${encodeURIComponent(tinhTrang)}`);
            hienThiPhi.textContent = `${Number(phanHoi.duLieu?.phi || 0).toLocaleString('vi-VN')}đ`;
        } catch (loi) {
            hienThiPhi.textContent = 'Không tính được';
        }
    };
    const veTaiLieuTraTam = () => {
        const khung = document.querySelector('#danh-sach-tai-lieu-tra-tam');
        if (!khung) return;
        const homNay = new Date(); homNay.setHours(0, 0, 0, 0);
        const quaHan = taiLieuTraDaChon.some(sach => new Date(`${sach.hanTra}T00:00:00`) < homNay);
        khung.innerHTML = taiLieuTraDaChon.map(sach => `<div class="return-book"><i class="green">↗</i><div><b>${a(sach.tieuDe)}</b><span>${a(sach.ma)} · Hạn trả ${dinhDangNgay(sach.hanTra)}</span><p>Độc giả: ${a(sach.hoTen)} · ${a(sach.maSo)}</p></div><button class="bo-tai-lieu-tra" type="button" data-id="${sach.maChiTietPhieuMuon}">×</button></div>`).join('') || '<p>Chưa chọn tài liệu cần trả.</p>';
        document.querySelector('#han-tra-tra-tam').textContent = taiLieuTraDaChon.length ? taiLieuTraDaChon.map(sach => dinhDangNgay(sach.hanTra)).join(', ') : '—';
        document.querySelector('#ngay-tra-tra-tam').textContent = taiLieuTraDaChon.length ? dinhDangNgay(new Date()) : '—';
        document.querySelector('#qua-han-tra-tam').textContent = taiLieuTraDaChon.length ? (quaHan ? 'Có' : 'Không') : '—';
        document.querySelector('#xac-nhan-tra-sach').disabled = !taiLieuTraDaChon.length;
        capNhatPhiTraTam();
    };
    const taiDuLieuChon = async () => {
        const tuKhoa = document.querySelector('#tim-tai-lieu-trong-hop').value;
        const d = await g(`api/thuThu.php?hanhDong=taiLieuChon&loai=${loaiChon}&tuKhoa=${encodeURIComponent(tuKhoa)}`);
        duLieuChon = d.duLieu || [];
        danhSachChon.innerHTML = duLieuChon.map((sach, chiSo) => {
            const ma = loaiChon === 'muon' ? sach.maTaiLieu : sach.maChiTietPhieuMuon;
            const daChon = (loaiChon === 'muon' ? taiLieuDaChon : taiLieuTraDaChon).some(muc => String((loaiChon === 'muon' ? muc.maTaiLieu : muc.maChiTietPhieuMuon)) === String(ma));
            const phu = loaiChon === 'muon' ? `${sach.ma} · Còn ${sach.soLuongCon} bản` : `${sach.ma} · ${sach.hoTen} · Hạn ${dinhDangNgay(sach.hanTra)}`;
            return `<label><input type="checkbox" value="${ma}" ${daChon ? 'checked' : ''}><i class="${['blue','purple','green','orange'][chiSo % 4]}">${loaiChon === 'muon' ? '◫' : '↗'}</i><span><b>${a(sach.tieuDe)}</b><small>${a(phu)}</small></span></label>`;
        }).join('') || '<p align="center">Không tìm thấy tài liệu phù hợp.</p>';
        document.querySelector('#so-luong-da-chon').textContent = `${String(danhSachChon.querySelectorAll('input:checked').length).padStart(2, '0')} tài liệu đã chọn`;
    };
    const moChonTaiLieu = async loai => {
        loaiChon = loai;
        document.querySelector('#tieu-de-chon-tai-lieu').textContent = loai === 'muon' ? 'Chọn tài liệu cho mượn' : 'Chọn tài liệu cần trả';
        document.querySelector('.xac-nhan-chon-tai-lieu').innerHTML = `${loai === 'muon' ? 'Thêm vào phiếu' : 'Tiếp nhận trả'} <span>→</span>`;
        document.querySelector('#tim-tai-lieu-trong-hop').value = '';
        hopChon.classList.add('mo'); hopChon.setAttribute('aria-hidden', 'false');
        await taiDuLieuChon();
    };
    document.querySelector('#mo-chon-tai-lieu-muon')?.addEventListener('click', () => moChonTaiLieu('muon'));
    document.querySelector('#mo-chon-tai-lieu-tra')?.addEventListener('click', () => moChonTaiLieu('tra'));
    document.querySelectorAll('.dong-chon-tai-lieu').forEach(nut => nut.addEventListener('click', () => {
        hopChon.classList.remove('mo');
        hopChon.setAttribute('aria-hidden', 'true');
    }));
    document.querySelector('#tim-tai-lieu-trong-hop')?.addEventListener('input', () => taiDuLieuChon());
    danhSachChon?.addEventListener('change', () => document.querySelector('#so-luong-da-chon').textContent = `${String(danhSachChon.querySelectorAll('input:checked').length).padStart(2, '0')} tài liệu đã chọn`);
    document.querySelector('.xac-nhan-chon-tai-lieu')?.addEventListener('click', () => {
        const maDaChon = [...danhSachChon.querySelectorAll('input:checked')].map(o => o.value);
        if (loaiChon === 'muon') { taiLieuDaChon = duLieuChon.filter(sach => maDaChon.includes(String(sach.maTaiLieu))); vePhieuMuonTam(); }
        else { taiLieuTraDaChon = duLieuChon.filter(sach => maDaChon.includes(String(sach.maChiTietPhieuMuon))); veTaiLieuTraTam(); }
        hopChon.classList.remove('mo'); hopChon.setAttribute('aria-hidden', 'true');
    });
    document.querySelector('#danh-sach-tai-lieu-muon-tam')?.addEventListener('click', e => { const nut = e.target.closest('.bo-tai-lieu-muon'); if (!nut) return; taiLieuDaChon = taiLieuDaChon.filter(sach => String(sach.maTaiLieu) !== nut.dataset.id); vePhieuMuonTam(); });
    document.querySelector('#danh-sach-tai-lieu-tra-tam')?.addEventListener('click', e => { const nut = e.target.closest('.bo-tai-lieu-tra'); if (!nut) return; taiLieuTraDaChon = taiLieuTraDaChon.filter(sach => String(sach.maChiTietPhieuMuon) !== nut.dataset.id); veTaiLieuTraTam(); });
    document.querySelector('#tinh-trang-khi-tra')?.addEventListener('change', capNhatPhiTraTam);
    const traCuuDocGia = async () => {
        const tuKhoa = document.querySelector('#tra-cuu-doc-gia-muon').value.trim(), khung = document.querySelector('#ket-qua-doc-gia-muon');
        try {
            const d = await g(`api/thuThu.php?hanhDong=traCuuDocGia&tuKhoa=${encodeURIComponent(tuKhoa)}`); docGiaMuon = d.duLieu;
            const hopLe = docGiaMuon.trangThaiThe === 'hoatDong' && docGiaMuon.trangThai === 'hoatDong' && docGiaMuon.soSachToiDa;
            khung.innerHTML = `<div class="avatar xl">${a(docGiaMuon.hoTen.split(/\s+/).map(x => x[0]).slice(-2).join(''))}</div><div><b>${a(docGiaMuon.hoTen)}</b><span>${a(docGiaMuon.maSo)} · ${a(docGiaMuon.khoa || 'Chưa cập nhật khoa')}</span><p><mark class="${hopLe ? 'green-mark' : 'red-mark'}">${hopLe ? 'Thẻ hoạt động' : 'Không thể mượn'}</mark> Đang mượn ${docGiaMuon.dangMuon} / ${docGiaMuon.soSachToiDa || '—'} sách</p></div>`;
            document.querySelector('#ma-phieu-muon-tam').textContent = `Phiếu mượn · ${docGiaMuon.maSo}`; document.querySelector('#trang-thai-phieu-muon-tam').textContent = hopLe ? 'Hợp lệ' : 'Không hợp lệ'; document.querySelector('#trang-thai-phieu-muon-tam').className = hopLe ? 'green-mark' : 'red-mark'; document.querySelector('#mo-chon-tai-lieu-muon').disabled = !hopLe; if (!hopLe) taiLieuDaChon = []; vePhieuMuonTam();
        } catch (e) { docGiaMuon = null; taiLieuDaChon = []; khung.innerHTML = `<p>${a(e.message)}</p>`; document.querySelector('#mo-chon-tai-lieu-muon').disabled = true; vePhieuMuonTam(); }
    };
    document.querySelector('#nut-tra-cuu-doc-gia-muon')?.addEventListener('click', traCuuDocGia);
    document.querySelector('#tra-cuu-doc-gia-muon')?.addEventListener('keydown', e => { if (e.key === 'Enter') traCuuDocGia(); });
    document.querySelector('#xac-nhan-cho-muon')?.addEventListener('click', async () => { try { const d = await p('lapPhieuMuon', {maDocGia: docGiaMuon.maDocGia, maTaiLieu: taiLieuDaChon.map(sach => sach.maTaiLieu)}); window.hienThongBao?.(`Đã lập phiếu mượn PM-${d.maPhieuMuon}.`); taiLieuDaChon = []; await traCuuDocGia(); } catch (e) { alert(e.message); } });
    document.querySelector('#xac-nhan-tra-sach')?.addEventListener('click', async () => { try { const d = await p('xacNhanTra', {maChiTietPhieuMuon: taiLieuTraDaChon.map(sach => sach.maChiTietPhieuMuon), tinhTrangKhiTra: document.querySelector('#tinh-trang-khi-tra')?.value}); window.hienThongBao?.(`Đã xác nhận trả sách${d.phi ? `, phát sinh ${Number(d.phi).toLocaleString('vi-VN')}đ` : ''}.`); taiLieuTraDaChon = []; veTaiLieuTraTam(); } catch (e) { alert(e.message); } });
    const b = document.querySelector('#du-lieu-yeu-cau-thu-thu');
    if (b) {
        const t = async () => {
            try {
                const d = await g('api/thuThu.php?hanhDong=yeuCau');
                b.innerHTML = d.duLieu.map(x => '<tr><td><b>' + a(x.hoTen) + '</b><span>' + a(x.maSo) + '</span></td><td>' + a(x.taiLieu.replaceAll('||', ', ')) + '</td><td>' + a(x.ngayYeuCau) + '</td><td>' + kiemTraYeuCau(x) + '</td><td><button class="nut-duyet-yeu-cau duyet-thu-thu" data-id="' + x.maYeuCauMuon + '" ' + (coTheDuyetYeuCau(x) ? '' : 'disabled') + '>Duyệt</button><button class="nut-tu-choi-yeu-cau tu-choi-thu-thu" data-id="' + x.maYeuCauMuon + '">Từ chối</button></td></tr>').join('') || '<tr><td colspan="5">Không có yêu cầu.</td></tr>'
            } catch (e) {
                b.innerHTML = '<tr><td colspan=4>' + a(e.message) + '</td></tr>'
            }
        };
        b.onclick = async e => {
            const n = e.target.closest('.duyet-thu-thu,.tu-choi-thu-thu');
            if (!n) return;
            const s = n.classList.contains('duyet-thu-thu') ? 'daDuyet' : 'tuChoi',
                l = s === 'tuChoi' ? prompt('Lý do từ chối:', '') : '';
            if (l === null) return;
            try {
                await p('xuLyYeuCau', {
                    maYeuCauMuon: n.dataset.id,
                    trangThai: s,
                    lyDoTuChoi: l
                });
                await t();
                window.hienThongBao?.('Đã xử lý yêu cầu.')
            } catch (e) {
                alert(e.message)
            }
        };
        t()
    }
    const bangYeuCauDayDu = document.querySelector('#du-lieu-yeu-cau-day-du');
    if (bangYeuCauDayDu) {
        const taiYeuCauDayDu = () => g('api/thuThu.php?hanhDong=yeuCau').then(d => {
            bangYeuCauDayDu.innerHTML = d.duLieu.map(x => {
                const hopLe = coTheDuyetYeuCau(x);
                return `<tr data-muc-yeu-cau data-trang-thai-yeu-cau="${hopLe ? 'hop-le' : 'tam-khoa'}" data-tim-yeu-cau="${a(`${x.hoTen} ${x.maSo} ${x.taiLieu}`).toLowerCase()}"><td><b>YC-${x.maYeuCauMuon}</b></td><td><b>${a(x.hoTen)}</b><span>${a(x.maSo)}</span></td><td>${a(x.taiLieu.replaceAll('||', ', '))}</td><td>${a(x.ngayYeuCau)}</td><td>${kiemTraYeuCau(x)}</td><td><button class="nut-duyet-yeu-cau duyet-yeu-cau-day-du" data-id="${x.maYeuCauMuon}" ${hopLe ? '' : 'disabled'}>Duyệt</button><button class="nut-tu-choi-yeu-cau tu-choi-yeu-cau-day-du" data-id="${x.maYeuCauMuon}">Từ chối</button></td></tr>`;
            }).join('') || '<tr><td colspan="6">Không có yêu cầu chờ duyệt.</td></tr>';
        });
        document.querySelectorAll('.mo-danh-sach-yeu-cau').forEach(nut => nut.addEventListener('click', () => taiYeuCauDayDu()));
        bangYeuCauDayDu.onclick = async e => { const nut = e.target.closest('.duyet-yeu-cau-day-du,.tu-choi-yeu-cau-day-du'); if (!nut || nut.disabled) return; const tuChoi = nut.classList.contains('tu-choi-yeu-cau-day-du'); const lyDo = tuChoi ? prompt('Lý do từ chối:', '') : ''; if (lyDo === null) return; try { await p('xuLyYeuCau', {maYeuCauMuon: nut.dataset.id, trangThai: tuChoi ? 'tuChoi' : 'daDuyet', lyDoTuChoi: lyDo}); await taiYeuCauDayDu(); window.hienThongBao?.('Đã xử lý yêu cầu.'); } catch (loi) { alert(loi.message); } };
        taiYeuCauDayDu();
    }
    const r = document.querySelector('#du-lieu-doc-gia-thu-thu');
    if (r) {
        const taiDocGia = () => g(`api/thuThu.php?hanhDong=docGia&thoiGian=${Date.now()}`, {cache: 'no-store'}).then(d => r.innerHTML = d.duLieu.map(x => '<tr><td><b>' + a(x.hoTen) + '</b><span>' + a(x.thuDienTu) + '</span></td><td>' + a(x.maSo) + '</td><td>' + a(x.khoa || '') + '</td><td>' + x.dangMuon + '</td><td>' + Number(x.tienPhat).toLocaleString('vi-VN') + 'đ</td><td><mark class="' + (x.trangThaiThe === 'hoatDong' ? 'green-mark' : 'red-mark') + '">' + (x.trangThaiThe === 'hoatDong' ? 'Hoạt động' : 'Tạm khóa') + '</mark></td><td><button class="table-action cap-nhat-the" data-id="' + x.maDocGia + '" data-trang-thai="' + x.trangThaiThe + '">' + (x.trangThaiThe === 'hoatDong' ? 'Tạm khóa thẻ' : 'Mở khóa thẻ') + '</button></td></tr>').join('') || '<tr><td colspan="7">Chưa có độc giả.</td></tr>');
        r.onclick = async e => { const nut = e.target.closest('.cap-nhat-the'); if (!nut) return; try { await p('capNhatThe', {maDocGia: nut.dataset.id, trangThaiThe: nut.dataset.trangThai === 'hoatDong' ? 'tamKhoa' : 'hoatDong'}); await taiDocGia(); window.hienThongBao?.('Đã cập nhật trạng thái thẻ.'); } catch (loi) { alert(loi.message); } };
        document.querySelectorAll('[data-page="reader-admin"]').forEach(nut => nut.addEventListener('click', taiDocGia));
        taiDocGia();
    }
    const bangGiaHan = document.querySelector('#du-lieu-gia-han-thu-thu');
    if (bangGiaHan) {
        const taiGiaHan = () => g('api/thuThu.php?hanhDong=yeuCauGiaHan').then(d => bangGiaHan.innerHTML = d.duLieu.map(x => `<tr><td><b>${a(x.hoTen)}</b><span>${a(x.maSo)}</span></td><td><b>${a(x.tieuDe)}</b><span>${a(x.ma)}</span></td><td>${dinhDangNgay(x.hanTra)}</td><td>${dinhDangNgay(x.hanTraMoi)}</td><td><button class="table-action duyet-gia-han" data-id="${x.maYeuCauGiaHan}">Duyệt</button><button class="table-action tu-choi-gia-han" data-id="${x.maYeuCauGiaHan}">Từ chối</button></td></tr>`).join('') || '<tr><td colspan="5">Không có yêu cầu gia hạn chờ duyệt.</td></tr>');
        bangGiaHan.onclick = async e => { const nut = e.target.closest('.duyet-gia-han,.tu-choi-gia-han'); if (!nut) return; try { await p('xuLyGiaHan', {maYeuCauGiaHan: nut.dataset.id, trangThai: nut.classList.contains('duyet-gia-han') ? 'daDuyet' : 'tuChoi'}); await taiGiaHan(); window.hienThongBao?.('Đã xử lý yêu cầu gia hạn.'); } catch (loi) { alert(loi.message); } };
        taiGiaHan();
    }
})();

/* Tách riêng để trang Quản lý phạt luôn tải được, kể cả khi một phần nghiệp vụ khác không có trên trang. */
(() => {
    if (document.body.dataset.role !== 'librarian') return;
    const bang = document.querySelector('#du-lieu-phat-thu-thu');
    if (!bang) return;
    const anToan = giaTri => String(giaTri ?? '').replace(/[&<>]/g, kyTu => ({'&': '&amp;', '<': '&lt;', '>': '&gt;'}[kyTu]));
    const nhanLoaiPhat = { quaHan: 'Quá hạn', huHongNhe: 'Hư hỏng nhẹ', huHongNang: 'Hư hỏng nặng' };
    const nhanTrangThai = { chuaThanhToan: 'Chưa thanh toán', daThanhToan: 'Đã thanh toán' };
    const layPhat = async () => {
        try {
            const phanHoi = await fetch(`api/thuThu.php?hanhDong=phat&thoiGian=${Date.now()}`, {cache: 'no-store'});
            const duLieu = await phanHoi.json();
            if (!phanHoi.ok) throw Error(duLieu.loi || 'Không thể tải danh sách phạt.');
            const danhSach = duLieu.duLieu || [];
            bang.innerHTML = danhSach.map(phat => `<tr><td>VP-${anToan(phat.maPhieuPhat)}</td><td><b>${anToan(phat.hoTen)}</b><span>${anToan(phat.maSo)}</span></td><td>${anToan(nhanLoaiPhat[phat.loaiPhat] || phat.loaiPhat)}</td><td>${Number(phat.soTien || 0).toLocaleString('vi-VN')}đ</td><td>${anToan(phat.ngayLap)}</td><td>${anToan(nhanTrangThai[phat.trangThai] || phat.trangThai)}</td><td>${phat.trangThai === 'chuaThanhToan' ? `<button class="table-action thu-phat" data-id="${anToan(phat.maPhieuPhat)}">Thu tiền</button>` : ''}</td></tr>`).join('') || '<tr><td colspan="7">Chưa có phiếu phạt.</td></tr>';
        } catch (loi) {
            bang.innerHTML = `<tr><td colspan="7">${anToan(loi.message)}</td></tr>`;
        }
    };
    bang.addEventListener('click', async suKien => {
        const nut = suKien.target.closest('.thu-phat');
        if (!nut) return;
        try {
            const phanHoi = await fetch('api/thuThu.php', { method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({hanhDong: 'thuPhat', maPhieuPhat: nut.dataset.id}) });
            const duLieu = await phanHoi.json();
            if (!phanHoi.ok) throw Error(duLieu.loi || 'Không thể thu tiền phạt.');
            await layPhat();
            window.hienThongBao?.('Đã xác nhận thu tiền phạt.');
        } catch (loi) {
            alert(loi.message);
        }
    });
    document.querySelectorAll('[data-page="fines"]').forEach(nut => nut.addEventListener('click', layPhat));
    layPhat();
})();
