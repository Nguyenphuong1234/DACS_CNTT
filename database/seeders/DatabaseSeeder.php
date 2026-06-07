<?php

namespace Database\Seeders;

use App\Models\AiSetting;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::query()->firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Quản trị viên'],
        );

        $customerRole = Role::query()->firstOrCreate(
            ['slug' => 'customer'],
            ['name' => 'Khách hàng'],
        );

        $admin = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'role_id' => $adminRole->id,
                'name' => 'Admin Demo',
                'phone' => '0900000001',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $customer = User::query()->updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'role_id' => $customerRole->id,
                'name' => 'Khách Hàng Demo',
                'phone' => '0900000002',
                'password' => 'password',
                'email_verified_at' => now(),
            ],
        );

        $address = UserAddress::query()->updateOrCreate(
            [
                'user_id' => $customer->id,
                'address_line' => '12 Nguyễn Trãi',
            ],
            [
                'recipient_name' => $customer->name,
                'recipient_phone' => $customer->phone,
                'ward' => 'Phường Bến Thành',
                'district' => 'Quận 1',
                'city' => 'TP. Hồ Chí Minh',
                'is_default' => true,
            ],
        );

        $categories = collect([
            ['name' => 'Điện thoại', 'description' => 'Smartphone chính hãng, phù hợp học tập và làm việc.'],
            ['name' => 'Laptop', 'description' => 'Laptop văn phòng, học tập và sáng tạo nội dung.'],
            ['name' => 'Phụ kiện', 'description' => 'Tai nghe, sạc, chuột, bàn phím và phụ kiện công nghệ.'],
            ['name' => 'Thiết bị nhà thông minh', 'description' => 'Thiết bị hỗ trợ cuộc sống tiện nghi hơn.'],
        ])->mapWithKeys(function (array $category): array {
            $model = Category::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ],
            );

            return [$model->name => $model];
        });

        $products = [
            [
                'category' => 'Điện thoại',
                'name' => 'Aster Phone X1',
                'sku' => 'PHONE-X1',
                'price' => 8990000,
                'stock_quantity' => 24,
                'description' => 'Màn hình AMOLED 6.5 inch, pin 5000mAh, camera kép hỗ trợ chụp thiếu sáng.',
                'image' => 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Điện thoại',
                'name' => 'Nova Mini 5G',
                'sku' => 'PHONE-NM5G',
                'price' => 5490000,
                'stock_quantity' => 4,
                'description' => 'Điện thoại nhỏ gọn, 5G, hiệu năng ổn định cho nhu cầu hằng ngày.',
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Laptop',
                'name' => 'LumaBook Air 14',
                'sku' => 'LAP-LBA14',
                'price' => 18990000,
                'stock_quantity' => 12,
                'description' => 'Laptop mỏng nhẹ 14 inch, RAM 16GB, SSD 512GB, phù hợp sinh viên và văn phòng.',
                'image' => 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Laptop',
                'name' => 'WorkPro 15',
                'sku' => 'LAP-WP15',
                'price' => 22990000,
                'stock_quantity' => 0,
                'description' => 'Laptop hiệu năng cao cho lập trình, thiết kế cơ bản và đa nhiệm.',
                'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Phụ kiện',
                'name' => 'Tai nghe Sonic Pods',
                'sku' => 'ACC-SPODS',
                'price' => 1290000,
                'stock_quantity' => 36,
                'description' => 'Tai nghe không dây, chống ồn chủ động, hộp sạc nhỏ gọn.',
                'image' => 'https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Phụ kiện',
                'name' => 'Chuột Ergo Click',
                'sku' => 'ACC-ERGO',
                'price' => 490000,
                'stock_quantity' => 8,
                'description' => 'Chuột không dây thiết kế công thái học, pin lâu, kết nối nhanh.',
                'image' => 'https://images.unsplash.com/photo-1527814050087-3793815479db?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Thiết bị nhà thông minh',
                'name' => 'Smart Lamp Halo',
                'sku' => 'HOME-HALO',
                'price' => 790000,
                'stock_quantity' => 18,
                'description' => 'Đèn thông minh đổi màu, điều khiển qua ứng dụng và giọng nói.',
                'image' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'category' => 'Thiết bị nhà thông minh',
                'name' => 'Camera HomeSafe C2',
                'sku' => 'HOME-C2',
                'price' => 1490000,
                'stock_quantity' => 3,
                'description' => 'Camera trong nhà, xoay 360 độ, cảnh báo chuyển động và lưu trữ đám mây.',
                'image' => 'https://images.unsplash.com/photo-1558002038-1055907df827?auto=format&fit=crop&w=900&q=80',
            ],
        ];

        $createdProducts = collect($products)->map(function (array $product) use ($categories): Product {
            $model = Product::query()->updateOrCreate(
                ['sku' => $product['sku']],
                [
                    'category_id' => $categories[$product['category']]->id,
                    'name' => $product['name'],
                    'slug' => Str::slug($product['name']),
                    'description' => $product['description'],
                    'price' => $product['price'],
                    'stock_quantity' => $product['stock_quantity'],
                    'low_stock_threshold' => 5,
                    'is_active' => true,
                ],
            );

            ProductImage::query()->updateOrCreate(
                [
                    'product_id' => $model->id,
                    'path' => $product['image'],
                ],
                [
                    'alt_text' => $product['name'],
                    'is_primary' => true,
                ],
            );

            return $model;
        });

        $order = Order::query()->firstOrCreate(
            ['order_code' => 'DHDEMO001'],
            [
                'user_id' => $customer->id,
                'status' => Order::SHIPPING,
                'payment_method' => 'cod',
                'recipient_name' => $address->recipient_name,
                'recipient_phone' => $address->recipient_phone,
                'shipping_address' => $address->fullAddress(),
                'subtotal' => 0,
                'total_amount' => 0,
                'customer_note' => 'Giao hàng trong giờ hành chính.',
            ],
        );

        if ($order->wasRecentlyCreated) {
            $orderProducts = $createdProducts->where('stock_quantity', '>', 0)->take(2);
            $total = 0;

            foreach ($orderProducts as $product) {
                $quantity = 1;
                $lineTotal = (float) $product->price * $quantity;
                $total += $lineTotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'unit_price' => $product->price,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ]);

                $product->decrement('stock_quantity', $quantity);
            }

            $order->update([
                'subtotal' => $total,
                'total_amount' => $total,
            ]);

            foreach ([Order::PENDING, Order::CONFIRMED, Order::PACKING, Order::SHIPPING] as $index => $status) {
                OrderStatusHistory::query()->create([
                    'order_id' => $order->id,
                    'changed_by' => $index === 0 ? null : $admin->id,
                    'from_status' => $index === 0 ? null : [Order::PENDING, Order::CONFIRMED, Order::PACKING][$index - 1],
                    'to_status' => $status,
                    'note' => $index === 0 ? 'Đơn hàng được tạo.' : 'Cập nhật trạng thái đơn hàng.',
                    'created_at' => now()->subHours(4 - $index),
                    'updated_at' => now()->subHours(4 - $index),
                ]);
            }
        }

        AiSetting::query()->create([
            'is_enabled' => true,
            'model' => env('GROQ_MODEL', 'llama-3.1-8b-instant'),
            'system_prompt' => 'Bạn là trợ lý bán hàng thân thiện cho cửa hàng trực tuyến. Trả lời ngắn gọn, ưu tiên tư vấn sản phẩm dựa trên dữ liệu được cung cấp, không bịa thông tin đơn hàng và chỉ tra cứu đơn của người dùng hiện tại.',
            'faq' => [
                ['question' => 'Cửa hàng hỗ trợ thanh toán nào?', 'answer' => 'Hiện tại hệ thống hỗ trợ thanh toán khi nhận hàng (COD).'],
                ['question' => 'Làm sao theo dõi đơn hàng?', 'answer' => 'Đăng nhập, vào mục Đơn hàng của tôi và mở chi tiết đơn để xem timeline trạng thái.'],
                ['question' => 'Sản phẩm hết hàng có đặt được không?', 'answer' => 'Không. Hệ thống chỉ cho đặt số lượng trong phạm vi tồn kho.'],
            ],
        ]);
    }
}
