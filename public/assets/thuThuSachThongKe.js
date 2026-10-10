(() => {
  if (document.body.dataset.role !== 'librarian') return;
  const taiThongKe = () => fetch('api/thuThuSach.php?layTatCa=1').then(phanHoi => phanHoi.json()).then(phanHoi => {
    const danhSach = phanHoi.duLieu || [];
    document.querySelector('#chi-so-tong-sach').textContent = danhSach.length;
    document.querySelector('#chi-so-ban-san-sang').textContent = danhSach.reduce((tong, sach) => tong + Number(sach.soBanSanSang), 0);
    document.querySelector('#chi-so-dang-muon-sach').textContent = danhSach.reduce((tong, sach) => tong + Number(sach.tongBan - sach.soBanSanSang), 0);
    document.querySelector('#chi-so-can-kiem-tra-sach').textContent = danhSach.filter(sach => Number(sach.soBanSanSang) === 0).length;
  });
  taiThongKe();
  window.addEventListener('thu-thu-sach-da-cap-nhat', taiThongKe);
})();
