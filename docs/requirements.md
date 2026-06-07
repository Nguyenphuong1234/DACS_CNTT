# HỆ THỐNG QUẢN LÝ VÀ BÁN HÀNG TRỰC TUYẾN

## Sinh viên thực hiện

* Đồng Thị Ánh - 23013883
* Bùi Thị Hồng Tươi - 23015124
* Nguyễn Minh Phương - 23015738

---

## 1. Giới thiệu dự án

Dự án xây dựng một hệ thống quản lý và bán hàng trực tuyến nhằm cung cấp nền tảng mua sắm đơn giản, tiện lợi cho khách hàng, đồng thời hỗ trợ người quản trị quản lý sản phẩm, kho hàng, đơn hàng, người dùng và doanh thu.

Hệ thống được phát triển bằng Laravel Framework kết hợp Livewire để xây dựng giao diện động. Dự án bao gồm hai phân hệ chính: phân hệ Khách hàng và phân hệ Quản trị. Ngoài các chức năng bán hàng cơ bản, hệ thống tích hợp Chatbox AI sử dụng GroqAI API nhằm hỗ trợ tư vấn sản phẩm và trả lời câu hỏi tự động.

---

## 2. Công nghệ sử dụng

### 2.1. Backend

* Laravel Framework
* Laravel built-in authentication
* Eloquent ORM
* Migration và Seeder
* Middleware phân quyền
* Validation Request

### 2.2. Frontend

* Laravel Livewire
* Blade Template
* Tailwind CSS
* Alpine.js
* Vite

### 2.3. Cơ sở dữ liệu

* MySQL

### 2.4. Kiểm thử

* PHPUnit

### 2.5. Tích hợp bên ngoài

* GroqAI API cho Chatbox AI

---

## 3. Mục tiêu dự án

* Xây dựng website bán hàng trực tuyến có các chức năng cơ bản.
* Hỗ trợ khách hàng xem sản phẩm, tìm kiếm sản phẩm, thêm vào giỏ hàng và đặt hàng.
* Hỗ trợ khách hàng quản lý thông tin tài khoản, địa chỉ nhận hàng và lịch sử mua hàng.
* Hỗ trợ quản trị viên quản lý sản phẩm, danh mục, kho hàng, đơn hàng và người dùng.
* Hỗ trợ quản trị viên cập nhật trạng thái đơn hàng.
* Cung cấp báo cáo thống kê doanh thu cơ bản.
* Tích hợp Chatbox AI để hỗ trợ khách hàng trong quá trình mua sắm.

---

## 4. Phạm vi dự án

Dự án tập trung xây dựng các chức năng chính của hệ thống bán hàng trực tuyến, bao gồm:

* Quản lý tài khoản người dùng
* Quản lý sản phẩm
* Quản lý danh mục sản phẩm
* Quản lý kho hàng
* Quản lý giỏ hàng
* Quản lý đơn hàng
* Quản lý trạng thái đơn hàng
* Quản lý người dùng và phân quyền cơ bản
* Báo cáo, thống kê doanh thu cơ bản
* Chatbox AI hỗ trợ khách hàng

Các chức năng như đánh giá sản phẩm, bình luận, danh sách yêu thích, mã giảm giá và thanh toán trực tuyến chưa được triển khai trong phạm vi đồ án này.

---

## 5. Yêu cầu chức năng

## 5.1. Phân hệ Khách hàng

### 5.1.1. Đăng ký, đăng nhập và quản lý tài khoản

Khách hàng có thể:

* Đăng ký tài khoản mới
* Đăng nhập vào hệ thống
* Đăng xuất khỏi hệ thống
* Cập nhật thông tin cá nhân
* Đổi mật khẩu
* Quản lý địa chỉ nhận hàng
* Xem lịch sử mua hàng

### 5.1.2. Xem và tìm kiếm sản phẩm

Khách hàng có thể:

* Xem danh sách sản phẩm
* Xem chi tiết sản phẩm
* Tìm kiếm sản phẩm theo tên
* Lọc sản phẩm theo danh mục
* Lọc sản phẩm theo khoảng giá
* Xem trạng thái còn hàng hoặc hết hàng

### 5.1.3. Giỏ hàng

Khách hàng có thể:

* Thêm sản phẩm vào giỏ hàng
* Xem danh sách sản phẩm trong giỏ hàng
* Cập nhật số lượng sản phẩm
* Xóa sản phẩm khỏi giỏ hàng
* Xem tổng tiền tạm tính
* Tiến hành đặt hàng

### 5.1.4. Đặt hàng

Khách hàng có thể:

* Chọn hoặc nhập địa chỉ nhận hàng
* Nhập thông tin người nhận
* Xem lại danh sách sản phẩm trong đơn hàng
* Xem tổng tiền đơn hàng
* Xác nhận đặt hàng

Trong phạm vi đồ án, hệ thống sử dụng phương thức thanh toán khi nhận hàng. Chức năng thanh toán trực tuyến có thể được phát triển ở giai đoạn sau.

### 5.1.5. Theo dõi đơn hàng

Khách hàng có thể xem trạng thái đơn hàng theo quy trình:

* Chờ xác nhận
* Đã xác nhận
* Đang đóng gói
* Đang giao
* Hoàn thành
* Hủy

Hệ thống hiển thị lịch sử trạng thái đơn hàng để khách hàng theo dõi quá trình xử lý.

### 5.1.6. Chatbox AI

Chatbox AI hỗ trợ khách hàng:

* Tư vấn sản phẩm theo nhu cầu
* Trả lời câu hỏi thường gặp
* Hướng dẫn sử dụng website
* Hỗ trợ tra cứu thông tin đơn hàng

Chatbox AI được tích hợp thông qua GroqAI API.

---

## 5.2. Phân hệ Quản trị

### 5.2.1. Trang tổng quan

Quản trị viên có thể xem các thông tin tổng quan:

* Tổng số sản phẩm
* Tổng số đơn hàng
* Tổng số người dùng
* Doanh thu
* Đơn hàng mới nhất
* Sản phẩm sắp hết hàng

### 5.2.2. Quản lý sản phẩm

Quản trị viên có thể:

* Thêm sản phẩm mới
* Cập nhật thông tin sản phẩm
* Xóa sản phẩm
* Xem danh sách sản phẩm
* Tìm kiếm sản phẩm
* Lọc sản phẩm theo danh mục hoặc trạng thái
* Quản lý hình ảnh sản phẩm
* Quản lý giá bán và mô tả sản phẩm

### 5.2.3. Quản lý danh mục sản phẩm

Quản trị viên có thể:

* Thêm danh mục mới
* Cập nhật danh mục
* Xóa danh mục
* Xem danh sách danh mục
* Gán sản phẩm vào danh mục tương ứng

### 5.2.4. Quản lý kho hàng

Quản trị viên có thể:

* Cập nhật số lượng tồn kho
* Theo dõi sản phẩm còn hàng hoặc hết hàng
* Xem danh sách sản phẩm sắp hết hàng

### 5.2.5. Quản lý đơn hàng

Quản trị viên có thể:

* Xem danh sách đơn hàng
* Xem chi tiết đơn hàng
* Tìm kiếm đơn hàng theo mã đơn hoặc khách hàng
* Lọc đơn hàng theo trạng thái
* Cập nhật trạng thái đơn hàng
* Xử lý hủy đơn hàng
* Ghi chú xử lý đơn hàng nếu cần

Trạng thái đơn hàng bao gồm:

* Chờ xác nhận
* Đã xác nhận
* Đang đóng gói
* Đang giao
* Hoàn thành
* Hủy

Khi trạng thái đơn hàng thay đổi, hệ thống lưu lịch sử thay đổi để khách hàng có thể theo dõi.

### 5.2.6. Quản lý người dùng và phân quyền

Hệ thống có hai nhóm người dùng chính:

* Khách hàng
* Quản trị viên

Quản trị viên có thể:

* Xem danh sách người dùng
* Tìm kiếm người dùng
* Cập nhật thông tin người dùng
* Khóa hoặc mở khóa tài khoản
* Gán vai trò quản trị cho người dùng

### 5.2.7. Báo cáo và thống kê

Hệ thống cung cấp các thống kê cơ bản:

* Tổng doanh thu
* Tổng số đơn hàng
* Tổng số sản phẩm
* Sản phẩm bán chạy
* Doanh thu theo ngày hoặc tháng
* Số lượng đơn hàng theo trạng thái

### 5.2.8. Quản lý Chatbox AI

Quản trị viên có thể:

* Cấu hình GroqAI API key
* Bật hoặc tắt Chatbox AI
* Cập nhật nội dung hướng dẫn cho AI
* Quản lý câu hỏi thường gặp
* Xem lịch sử hội thoại nếu cần

---

## 6. Yêu cầu phi chức năng

### 6.1. Giao diện

* Giao diện đơn giản, dễ sử dụng
* Tương thích với máy tính và thiết bị di động
* Phân chia rõ ràng giữa giao diện khách hàng và giao diện quản trị
* Sử dụng Tailwind CSS để xây dựng giao diện

### 6.2. Hiệu năng

* Danh sách sản phẩm cần có phân trang
* Tìm kiếm và lọc sản phẩm phản hồi nhanh
* Truy vấn dữ liệu được tối ưu bằng Eloquent Relationship
* Hình ảnh sản phẩm cần được tối ưu dung lượng

### 6.3. Bảo mật

* Người dùng phải đăng nhập để quản lý tài khoản và đặt hàng
* Quản trị viên phải đăng nhập để truy cập trang quản trị
* Mật khẩu được mã hóa
* Phân quyền rõ ràng giữa khách hàng và quản trị viên
* Validate dữ liệu đầu vào
* Bảo vệ form bằng CSRF token
* Không lưu API key trực tiếp trong mã nguồn

### 6.4. Khả năng mở rộng

* Có thể mở rộng tích hợp thanh toán trực tuyến
* Có thể bổ sung chức năng mã giảm giá
* Có thể bổ sung chức năng đánh giá, bình luận, wishlist
* Có thể mở rộng hệ thống thông báo real-time
* Có thể phát triển API cho mobile app trong tương lai

### 6.5. Kiểm thử

* Sử dụng PHPUnit để kiểm thử các chức năng chính
* Kiểm thử đăng ký, đăng nhập
* Kiểm thử xem sản phẩm
* Kiểm thử thêm sản phẩm vào giỏ hàng
* Kiểm thử đặt hàng
* Kiểm thử cập nhật trạng thái đơn hàng
* Kiểm thử phân quyền truy cập quản trị

---

## 7. Yêu cầu dữ liệu

Hệ thống cần quản lý các nhóm dữ liệu chính:

* Người dùng
* Vai trò
* Danh mục sản phẩm
* Sản phẩm
* Hình ảnh sản phẩm
* Kho hàng
* Giỏ hàng
* Chi tiết giỏ hàng
* Đơn hàng
* Chi tiết đơn hàng
* Lịch sử trạng thái đơn hàng
* Cấu hình Chatbox AI
* Lịch sử hội thoại AI nếu cần

---

## 8. Định hướng xây dựng cơ sở dữ liệu

Dự án sử dụng MySQL làm hệ quản trị cơ sở dữ liệu chính.

Các bảng dữ liệu dự kiến bao gồm:

* users
* roles
* categories
* products
* product_images
* carts
* cart_items
* orders
* order_items
* order_status_histories
* ai_settings
* ai_conversations

---

## 9. Quy trình xử lý đơn hàng

Quy trình xử lý đơn hàng của hệ thống:

1. Khách hàng xem và tìm kiếm sản phẩm
2. Khách hàng thêm sản phẩm vào giỏ hàng
3. Khách hàng kiểm tra giỏ hàng
4. Khách hàng nhập thông tin giao hàng
5. Khách hàng xác nhận đặt hàng
6. Hệ thống tạo đơn hàng ở trạng thái Chờ xác nhận
7. Quản trị viên xác nhận đơn hàng
8. Đơn hàng chuyển sang trạng thái Đã xác nhận
9. Quản trị viên cập nhật trạng thái Đang đóng gói
10. Quản trị viên cập nhật trạng thái Đang giao
11. Đơn hàng chuyển sang Hoàn thành khi giao hàng thành công
12. Đơn hàng có thể chuyển sang Hủy nếu khách hàng hoặc quản trị viên hủy đơn

---

## 10. Kết quả mong đợi

Sau khi hoàn thành, hệ thống cần đạt được các kết quả sau:

* Có giao diện khách hàng để xem sản phẩm, tìm kiếm, thêm vào giỏ hàng và đặt hàng
* Có giao diện quản trị để quản lý sản phẩm, danh mục, kho hàng, đơn hàng và người dùng
* Có hệ thống đăng ký, đăng nhập và phân quyền cơ bản
* Có chức năng theo dõi trạng thái đơn hàng
* Có báo cáo thống kê doanh thu cơ bản
* Có Chatbox AI tích hợp GroqAI API để hỗ trợ khách hàng
* Dữ liệu được lưu trữ bằng MySQL
* Mã nguồn có cấu trúc rõ ràng, dễ bảo trì và dễ mở rộng
