# Hướng Dẫn Bàn Giao Hệ Thống Bán Hàng

## Tài khoản demo

- Admin: `admin@example.com` / `password`
- Khách hàng: `customer@example.com` / `password`

## Cấu hình môi trường

`.env` cần có database hợp lệ và các biến AI:

```env
GROQ_API_KEY=...
GROQ_MODEL=llama-3.1-8b-instant
AI_CHAT_ENABLED=true
```

Không lưu API key trong mã nguồn. Cấu hình Groq được đọc qua `config/services.php`.

## Lệnh chạy

```bash
composer install
npm install
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Khi phát triển giao diện có thể dùng:

```bash
composer run dev
```

## Chức năng đã triển khai

- Khách hàng: đăng ký/đăng nhập, xem sản phẩm, tìm kiếm, lọc danh mục/khoảng giá, xem chi tiết, thêm giỏ hàng, checkout COD, quản lý địa chỉ, xem lịch sử đơn và timeline trạng thái.
- Quản trị: dashboard, CRUD danh mục, CRUD sản phẩm, quản lý ảnh, cập nhật kho, quản lý đơn hàng, cập nhật trạng thái, ghi lịch sử trạng thái, quản lý người dùng, khóa/mở khóa tài khoản, gán vai trò.
- Chatbox AI: bật/tắt, prompt, FAQ, lịch sử hội thoại, tư vấn theo sản phẩm và tra cứu đơn hàng của user đang đăng nhập.

## Kiểm thử thủ công đề xuất

- Đăng nhập admin, vào `/admin`, kiểm tra dashboard và các trang quản trị.
- Đăng nhập khách hàng, thêm sản phẩm vào giỏ, đặt hàng COD, xem chi tiết đơn.
- Từ admin cập nhật trạng thái đơn; quay lại khách hàng kiểm tra timeline.
- Bật/tắt AI trong `/admin/ai`, hỏi thử về sản phẩm và đơn hàng.
