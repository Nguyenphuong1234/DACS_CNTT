# Kế Hoạch Triển Khai Toàn Bộ Dự Án Bàn Giao

## Tóm Tắt

Triển khai đầy đủ hệ thống bán hàng trực tuyến theo `docs/requirements.md` trên Laravel + Livewire starter kit hiện có. Giữ Fortify authentication, Flux UI, Tailwind, Vite; mở rộng thành 2 phân hệ rõ ràng: Khách hàng và Quản trị. Không triển khai unit test/PHPUnit theo yêu cầu mới; thay bằng checklist kiểm thử thủ công để bàn giao.

## Kiến Trúc Và Dữ Liệu

- Thêm model/migration cho `Role`, `UserAddress`, `Category`, `Product`, `ProductImage`, `Cart`, `CartItem`, `Order`, `OrderItem`, `OrderStatusHistory`, `AiSetting`, `AiConversation`.
- Mở rộng `users` với `role_id`, `phone`, `is_locked`; user đăng ký mặc định là khách hàng, admin được tạo bằng seeder.
- Quản lý kho trực tiếp trên `products` bằng `stock_quantity`, `low_stock_threshold`, `is_active`; trạng thái còn/hết hàng tính từ tồn kho.
- Đơn hàng dùng COD, có mã đơn duy nhất, thông tin người nhận, địa chỉ giao hàng, tổng tiền, ghi chú và trạng thái.
- Trạng thái đơn hàng cố định: `pending`, `confirmed`, `packing`, `shipping`, `completed`, `cancelled`; mọi lần đổi trạng thái ghi vào lịch sử.
- Dữ liệu mẫu gồm admin, customer, danh mục, sản phẩm, ảnh sản phẩm, tồn kho, đơn hàng mẫu, cấu hình AI mặc định.

## Phân Hệ Khách Hàng

- Trang chủ/cửa hàng: danh sách sản phẩm, phân trang, tìm kiếm theo tên, lọc danh mục, lọc khoảng giá, trạng thái còn/hết hàng.
- Chi tiết sản phẩm: ảnh, mô tả, giá, tồn kho, danh mục, thêm vào giỏ hàng.
- Tài khoản: cập nhật thông tin cá nhân, đổi mật khẩu qua phần settings hiện có, quản lý nhiều địa chỉ nhận hàng.
- Giỏ hàng: thêm sản phẩm, cập nhật số lượng, xóa sản phẩm, tính tạm tính, kiểm tra tồn kho trước checkout.
- Đặt hàng: chọn địa chỉ đã lưu hoặc nhập địa chỉ mới, nhập thông tin người nhận, xem lại sản phẩm/tổng tiền, xác nhận COD.
- Theo dõi đơn hàng: danh sách đơn, chi tiết đơn, timeline trạng thái, lịch sử thay đổi trạng thái.
- Chatbox AI: widget trên giao diện khách hàng, hỗ trợ tư vấn sản phẩm, FAQ, hướng dẫn website và tra cứu đơn hàng của chính người đang đăng nhập.

## Phân Hệ Quản Trị

- Route `/admin/*` bảo vệ bằng `auth`, `verified`, `admin`; user bị khóa không được truy cập hệ thống.
- Dashboard: tổng sản phẩm, tổng đơn, tổng user, doanh thu, đơn mới nhất, sản phẩm sắp hết hàng, doanh thu theo ngày/tháng, đơn theo trạng thái.
- Quản lý sản phẩm: CRUD, tìm kiếm, lọc danh mục/trạng thái, upload/quản lý ảnh, giá bán, mô tả, bật/tắt hiển thị.
- Quản lý danh mục: CRUD, slug tự động, không cho xóa danh mục đang có sản phẩm trừ khi chuyển sản phẩm sang danh mục khác hoặc vô hiệu hóa.
- Quản lý kho: cập nhật tồn kho, danh sách hết hàng/sắp hết hàng, cảnh báo theo `low_stock_threshold`.
- Quản lý đơn hàng: danh sách, chi tiết, tìm theo mã đơn/khách hàng, lọc trạng thái, cập nhật trạng thái, hủy đơn, ghi chú xử lý.
- Quản lý người dùng: danh sách, tìm kiếm, cập nhật thông tin, khóa/mở khóa, gán vai trò admin/customer.
- Quản lý AI: cấu hình Groq API qua `.env`, bật/tắt chatbox, chỉnh system prompt, quản lý FAQ, xem lịch sử hội thoại.

## Tích Hợp Và Cấu Hình

- Cấu hình MySQL trong `.env.example`; giữ SQLite chỉ cho dev nhanh nếu cần, nhưng hướng dẫn bàn giao dùng MySQL.
- Thêm `GROQ_API_KEY`, `GROQ_MODEL`, `AI_CHAT_ENABLED` vào cấu hình; không hard-code API key.
- Tạo service gọi Groq API, có timeout, xử lý lỗi thân thiện, không lộ key/log nhạy cảm.
- Dùng Livewire page components theo pattern repo hiện có; ưu tiên Blade/Flux/Tailwind, không đổi framework.
- Upload ảnh sản phẩm qua storage public; thêm hướng dẫn chạy `php artisan storage:link`.

## Kiểm Thử Bàn Giao

- Không viết unit test/PHPUnit.
- Chạy kiểm tra kỹ thuật: `composer install`, `npm install`, `php artisan migrate:fresh --seed`, `npm run build`.
- Kiểm thử thủ công các luồng: đăng ký/đăng nhập, customer mua hàng, admin quản lý sản phẩm/danh mục/kho, admin cập nhật đơn, customer xem timeline, AI chat khi bật/tắt.
- Bàn giao kèm tài khoản mẫu, hướng dẫn cài đặt, cấu hình `.env`, lệnh chạy dự án và checklist chức năng đã hoàn thành.

## Giả Định

- Phạm vi “toàn bộ” là toàn bộ chức năng có trong `requirements.md`, không bao gồm các phần tài liệu đã loại trừ: thanh toán online, đánh giá, bình luận, wishlist, mã giảm giá.
- Không unit test nghĩa là không thêm/chạy test bắt buộc cho phần mới; vẫn có thể giữ nguyên test starter kit đang có.
- Giao diện cần đủ hoàn chỉnh để demo và bàn giao, ưu tiên rõ ràng, responsive và ổn định hơn hiệu ứng phức tạp.
- tôi đã cấu hình sẵn env database, và GROQ_API_KEY trong env rồi