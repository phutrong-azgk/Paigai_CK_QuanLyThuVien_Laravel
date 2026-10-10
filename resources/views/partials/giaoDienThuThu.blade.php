<?php
$maTrang = $maTrang ?? '';
if ($maTrang === 'staff-dashboard'):
?>
  <div class="librarian-metrics">
    <article><span class="metric-icon navy">▣</span>
      <div><b id="chi-so-muon-hom-nay">—</b><small>Phiếu mượn hôm nay</small></div><em id="ghi-chu-muon-hom-nay">Đang tải dữ liệu</em>
    </article>
    <article><span class="metric-icon coral">↵</span>
      <div><b id="chi-so-tra-hom-nay">—</b><small>Sách trả hôm nay</small></div><em id="ghi-chu-tra-hom-nay">Đang tải dữ liệu</em>
    </article>
    <article><span class="metric-icon amber">!</span>
      <div><b id="chi-so-qua-han">—</b><small>Tài liệu quá hạn</small></div><em id="ghi-chu-qua-han">Đang tải dữ liệu</em>
    </article>
    <article><span class="metric-icon mint">₫</span>
      <div><b id="chi-so-chua-thu">—</b><small>Phí phạt chưa thu</small></div><em id="ghi-chu-chua-thu">Đang tải dữ liệu</em>
    </article>
  </div>
  <div class="librarian-dashboard-grid">
    <section class="panel">
      <div class="section-head">
        <div>
          <p class="eyebrow">XỬ LÝ NHANH</p>
          <h2>Quầy lưu thông</h2>
        </div>
      </div>
      <div class="quick-actions"><button data-go="borrow-admin"><i>+</i><b>Lập phiếu mượn</b><span>Nhập mã độc giả và chọn tài liệu</span></button><button data-go="return-admin"><i>↵</i><b>Nhận trả sách</b><span>Kiểm tra tình trạng sách</span></button><button data-go="reader-admin"><i>!</i><b>Xử lý quá hạn</b><span id="ghi-chu-xu-ly-qua-han">Đang tải dữ liệu</span></button></div>
    </section>
    <section class="panel">
      <div class="section-head">
        <div>
          <p class="eyebrow">LỊCH TRẢ SÁCH</p>
          <h2>Đến hạn trong 3 ngày</h2>
        </div>
      </div>
      <div id="danh-sach-den-han"><p>Đang tải dữ liệu...</p></div>
    </section>
  </div>
<?php elseif ($maTrang === 'borrow-admin'): ?>
  <div class="circulation-layout">
    <section class="panel circulation-form">
      <p class="eyebrow"></p>
      <h2>Xác thực độc giả</h2>
      <div class="o-nhap-lieu"><span>⌁</span><input id="tra-cuu-doc-gia-muon" placeholder="Nhập MSSV, MSGV hoặc mã độc giả"><button id="nut-tra-cuu-doc-gia-muon" type="button">Tra cứu</button></div>
      <div id="ket-qua-doc-gia-muon" class="reader-preview"><p>Nhập mã độc giả để lập phiếu mượn.</p></div>
      <p class="eyebrow"></p>
      <h2>Thêm tài liệu mượn</h2>
      <p class="form-hint">Tìm theo tên, mã tài liệu hoặc nhà xuất bản; có thể chọn nhiều tài liệu cho một phiếu mượn.</p><button id="mo-chon-tai-lieu-muon" class="outline" type="button" disabled>⌕ Tìm và chọn tài liệu</button>
    </section>
    <section class="panel loan-ticket">
      <div class="section-head">
        <div>
          <p class="eyebrow">PHIẾU MƯỢN TẠM</p>
          <h2 id="ma-phieu-muon-tam">Chưa chọn độc giả</h2>
        </div><mark id="trang-thai-phieu-muon-tam" class="yellow">Chờ thông tin</mark>
      </div>
      <div id="danh-sach-tai-lieu-muon-tam"><p>Chưa chọn tài liệu.</p></div>
      <div class="ticket-summary"><span>Số lượng<b id="so-luong-muon-tam">00 / —</b></span><span>Thời hạn<b id="thoi-han-muon-tam">— ngày</b></span><span>Phí tạm tính<b>0đ</b></span></div><button id="xac-nhan-cho-muon" class="primary full" type="button" disabled>Xác nhận cho mượn <span>→</span></button>
    </section>
  </div>
  <section class="panel pending-panel">
    <div class="section-head">
      <div>
        <p class="eyebrow">YÊU CẦU TỪ CỔNG ĐỘC GIẢ</p>
        <h2>Đăng ký mượn đang chờ duyệt</h2>
      </div><button class="text-btn mo-danh-sach-yeu-cau">Xem tất cả →</button>
    </div>
    <div class="table-panel pending-table">
      <table>
        <thead>
          <tr>
            <th>ĐỘC GIẢ</th>
            <th>TÀI LIỆU ĐĂNG KÝ</th>
            <th>THỜI GIAN GỬI</th>
            <th>KIỂM TRA</th>
            <th>THAO TÁC</th>
          </tr>
        </thead>
        <tbody id="du-lieu-yeu-cau-thu-thu">
          <tr data-yeu-cau-cho>
            <td><b>Lê Thị Hương</b><span>22110820 · Khoa Kinh tế</span></td>
            <td><b>Nguyên Lý Kế Toán</b><span>KT-021 · Còn 06 bản</span></td>
            <td>18/09/2026 · 08:42</td>
            <td><mark class="green-mark">Thẻ hợp lệ</mark></td>
            <td><button class="duyet-yeu-cau" data-message="Đã duyệt yêu cầu mượn của Lê Thị Hương.">Duyệt</button><button class="tu-choi-yeu-cau" data-message="Đã từ chối yêu cầu mượn của Lê Thị Hương.">Từ chối</button></td>
          </tr>
          <tr data-yeu-cau-cho>
            <td><b>Phạm Quốc Bảo</b><span>22110615 · Khoa Luật</span></td>
            <td><b>Giáo Trình Luật Dân Sự Việt Nam</b><span>LUAT-032 · Còn 04 bản</span></td>
            <td>18/09/2026 · 09:10</td>
            <td><mark class="green-mark">Thẻ hợp lệ</mark></td>
            <td><button class="duyet-yeu-cau" data-message="Đã duyệt yêu cầu mượn của Phạm Quốc Bảo.">Duyệt</button><button class="tu-choi-yeu-cau" data-message="Đã từ chối yêu cầu mượn của Phạm Quốc Bảo.">Từ chối</button></td>
          </tr>
          <tr data-yeu-cau-cho>
            <td><b>Trần Gia Huy</b><span>22110218 · Khoa Luật</span></td>
            <td><b>Pháp Luật Đại Cương</b><span>LUAT-008 · Hiện hết bản</span></td>
            <td>18/09/2026 · 09:26</td>
            <td><mark class="red-mark">Thẻ tạm khóa</mark></td>
            <td><button class="duyet-yeu-cau vo-hieu" disabled>Không thể duyệt</button><button class="tu-choi-yeu-cau" data-message="Đã từ chối yêu cầu do thẻ độc giả tạm khóa.">Từ chối</button></td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
  <div id="hop-danh-sach-yeu-cau" class="hop-danh-sach-yeu-cau" aria-hidden="true" hidden>
    <section class="noi-dung-danh-sach-yeu-cau" role="dialog" aria-modal="true" aria-labelledby="tieu-de-danh-sach-yeu-cau">
      <div class="dau-danh-sach-yeu-cau">
        <div>
          <p class="eyebrow">CỔNG ĐỘC GIẢ</p>
          <h2 id="tieu-de-danh-sach-yeu-cau">Tất cả yêu cầu mượn</h2>
          <p>Danh sách đăng ký trực tuyến đang chờ thủ thư xử lý.</p>
        </div><button class="dong-danh-sach-yeu-cau" aria-label="Đóng">×</button>
      </div>
      <div class="bo-loc-danh-sach-yeu-cau"><label class="tim-danh-sach-yeu-cau"><span>⌕</span><input id="tim-danh-sach-yeu-cau" placeholder="Tìm độc giả, tài liệu hoặc mã..."></label><select id="loc-trang-thai-yeu-cau">
          <option value="tat-ca">Tất cả trạng thái</option>
          <option value="hop-le">Thẻ hợp lệ</option>
          <option value="tam-khoa">Thẻ tạm khóa</option>
        </select></div>
      <div class="table-panel bang-danh-sach-yeu-cau">
        <table>
          <thead>
            <tr>
              <th>MÃ PHIẾU</th>
              <th>ĐỘC GIẢ</th>
              <th>TÀI LIỆU</th>
              <th>GỬI LÚC</th>
              <th>KIỂM TRA</th>
              <th></th>
            </tr>
          </thead>
          <tbody id="du-lieu-yeu-cau-day-du">
            <tr data-muc-yeu-cau data-trang-thai-yeu-cau="hop-le" data-tim-yeu-cau="yc-240918-01 le thi huong 22110820 nguyen ly ke toan kt-021">
              <td><b>YC-240918-01</b></td>
              <td><b>Lê Thị Hương</b><span>22110820 · Kinh tế</span></td>
              <td><b>Nguyên Lý Kế Toán</b><span>KT-021 · 06 bản</span></td>
              <td>18/09 · 08:42</td>
              <td><mark class="green-mark">Thẻ hợp lệ</mark></td>
              <td class="thao-tac-yeu-cau-day-du"><button class="duyet-yeu-cau-day-du" data-message="Đã duyệt yêu cầu YC-240918-01.">Duyệt</button><button class="tu-choi-yeu-cau-day-du" data-message="Đã từ chối yêu cầu YC-240918-01.">Từ chối</button></td>
            </tr>
            <tr data-muc-yeu-cau data-trang-thai-yeu-cau="hop-le" data-tim-yeu-cau="yc-240918-02 pham quoc bao 22110615 giao trinh luat dan su viet nam luat-032">
              <td><b>YC-240918-02</b></td>
              <td><b>Phạm Quốc Bảo</b><span>22110615 · Luật</span></td>
              <td><b>Giáo Trình Luật Dân Sự Việt Nam</b><span>LUAT-032 · 04 bản</span></td>
              <td>18/09 · 09:10</td>
              <td><mark class="green-mark">Thẻ hợp lệ</mark></td>
              <td class="thao-tac-yeu-cau-day-du"><button class="duyet-yeu-cau-day-du" data-message="Đã duyệt yêu cầu YC-240918-02.">Duyệt</button><button class="tu-choi-yeu-cau-day-du" data-message="Đã từ chối yêu cầu YC-240918-02.">Từ chối</button></td>
            </tr>
            <tr data-muc-yeu-cau data-trang-thai-yeu-cau="tam-khoa" data-tim-yeu-cau="yc-240918-03 tran gia huy 22110218 phap luat dai cuong luat-008">
              <td><b>YC-240918-03</b></td>
              <td><b>Trần Gia Huy</b><span>22110218 · Luật</span></td>
              <td><b>Pháp Luật Đại Cương</b><span>LUAT-008 · 00 bản</span></td>
              <td>18/09 · 09:26</td>
              <td><mark class="red-mark">Thẻ tạm khóa</mark></td>
              <td class="thao-tac-yeu-cau-day-du"><button class="duyet-yeu-cau-day-du vo-hieu" disabled>Không thể duyệt</button><button class="tu-choi-yeu-cau-day-du" data-message="Đã từ chối YC-240918-03 do thẻ tạm khóa.">Từ chối</button></td>
            </tr>
            <tr data-muc-yeu-cau data-trang-thai-yeu-cau="hop-le" data-tim-yeu-cau="yc-240918-04 do khanh linh 22110914 nhap mon tri tue nhan tao cntt-145">
              <td><b>YC-240918-04</b></td>
              <td><b>Đỗ Khánh Linh</b><span>22110914 · CNTT</span></td>
              <td><b>Nhập Môn Trí Tuệ Nhân Tạo</b><span>CNTT-145 · 05 bản</span></td>
              <td>18/09 · 10:04</td>
              <td><mark class="green-mark">Thẻ hợp lệ</mark></td>
              <td class="thao-tac-yeu-cau-day-du"><button class="duyet-yeu-cau-day-du" data-message="Đã duyệt yêu cầu YC-240918-04.">Duyệt</button><button class="tu-choi-yeu-cau-day-du" data-message="Đã từ chối yêu cầu YC-240918-04.">Từ chối</button></td>
            </tr>
            <tr data-muc-yeu-cau data-trang-thai-yeu-cau="hop-le" data-tim-yeu-cau="yc-240918-05 nguyen thanh dat 22110376 english for academic purposes nna-019">
              <td><b>YC-240918-05</b></td>
              <td><b>Nguyễn Thành Đạt</b><span>22110376 · Ngôn ngữ Anh</span></td>
              <td><b>English for Academic Purposes</b><span>NNA-019 · 07 bản</span></td>
              <td>18/09 · 10:18</td>
              <td><mark class="green-mark">Thẻ hợp lệ</mark></td>
              <td class="thao-tac-yeu-cau-day-du"><button class="duyet-yeu-cau-day-du" data-message="Đã duyệt yêu cầu YC-240918-05.">Duyệt</button><button class="tu-choi-yeu-cau-day-du" data-message="Đã từ chối yêu cầu YC-240918-05.">Từ chối</button></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="chan-danh-sach-yeu-cau"><span id="tong-yeu-cau-hien-thi">05 yêu cầu</span><button class="outline dong-danh-sach-yeu-cau">Đóng</button></div>
    </section>
  </div>
<?php elseif ($maTrang === 'return-admin'): ?>
  <div class="circulation-layout return-layout">
    <section class="panel circulation-form">
      <p class="eyebrow">CHỌN TÀI LIỆU</p>
      <h2>Tiếp nhận trả sách</h2>
      <p class="form-hint">Tìm và chọn một hoặc nhiều tài liệu đang được độc giả mượn để tiếp nhận trả cùng lúc.</p><button id="mo-chon-tai-lieu-tra" class="outline" type="button">⌕ Tìm tài liệu cần trả</button>
      <div id="danh-sach-tai-lieu-tra-tam"><p>Chưa chọn tài liệu cần trả.</p></div><label class="condition-label">Tình trạng khi nhận<select id="tinh-trang-khi-tra">
          <option value="nguyenVen">Nguyên vẹn</option>
          <option value="huHongNhe">Hư hỏng nhẹ</option>
          <option value="huHongNang">Hư hỏng nặng</option>
        </select></label>
    </section>
    <section class="panel return-result">
      <p class="eyebrow">KẾT QUẢ KIỂM TRA</p>
      <h2>Thông tin hoàn trả</h2>
      <div class="return-line"><span>Hạn trả</span><b id="han-tra-tra-tam">—</b></div>
      <div class="return-line"><span>Ngày trả</span><b id="ngay-tra-tra-tam">—</b></div>
      <div class="return-line"><span>Quá hạn</span><b id="qua-han-tra-tam" class="ok-text">—</b></div>
      <div class="return-line total"><span>Phí cần thu</span><b id="phi-tra-tam">0đ</b></div><button id="xac-nhan-tra-sach" class="primary full" type="button" disabled>Xác nhận trả sách <span>→</span></button>
    </section>
  </div>
<?php elseif ($maTrang === 'reader-admin'): ?>
  <section class="panel reader-management">
    <div class="inventory-toolbar">
      <div class="inventory-search"><span>⌕</span><input id="tim-doc-gia-thu-thu" placeholder="Tìm MSSV, MSGV, họ tên hoặc email..."></div>
    </div>
    <div class="table-panel inventory-table">
      <table>
        <thead>
          <tr>
            <th>ĐỘC GIẢ</th>
            <th>MSSV / MSGV</th>
            <th>KHOA</th>
            <th>ĐANG MƯỢN</th>
            <th>PHÍ PHẠT</th>
            <th>TRẠNG THÁI</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="du-lieu-doc-gia-thu-thu">
          <tr>
            <td colspan="7">Đang tải dữ liệu...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
<?php elseif ($maTrang === 'renewal-admin'): ?>
  <section class="panel pending-panel">
    <div class="section-head"><div><p class="eyebrow">GIA HẠN</p><h2>Yêu cầu gia hạn chờ duyệt</h2></div></div>
    <div class="table-panel inventory-table"><table><thead><tr><th>ĐỘC GIẢ</th><th>TÀI LIỆU</th><th>HẠN HIỆN TẠI</th><th>HẠN ĐỀ NGHỊ</th><th></th></tr></thead><tbody id="du-lieu-gia-han-thu-thu"><tr><td colspan="5">Đang tải dữ liệu...</td></tr></tbody></table></div>
  </section>
<?php elseif ($maTrang === 'fines'): ?>
  <section class="panel fine-panel">
    <div class="inventory-toolbar">
      <div class="inventory-search"><span>⌕</span><input id="tim-phat-thu-thu" placeholder="Tìm độc giả, mã phiếu phạt..."></div>
    </div>
    <div class="table-panel inventory-table">
      <table>
        <thead>
          <tr>
            <th>PHIẾU PHẠT</th>
            <th>ĐỘC GIẢ</th>
            <th>LÝ DO</th>
            <th>SỐ TIỀN</th>
            <th>NGÀY LẬP</th>
            <th>TRẠNG THÁI</th>
            <th></th>
          </tr>
        </thead>
        <tbody id="du-lieu-phat-thu-thu">
          <tr>
            <td colspan="7">Đang tải dữ liệu...</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
<?php endif; ?>
