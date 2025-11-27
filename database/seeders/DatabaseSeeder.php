<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Categories
        $categories = [
            [
                'name' => 'Mirrorless Cameras',
                'slug' => 'mirrorless-cameras',
                'description' => 'Fujifilm X Series and GFX mirrorless cameras with advanced features.',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Film Cameras',
                'slug' => 'film-cameras',
                'description' => 'Classic Fujifilm film cameras for analog photography enthusiasts.',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Instant Cameras',
                'slug' => 'instant-cameras',
                'description' => 'Fujifilm Instax instant cameras for fun and creative photography.',
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Lenses',
                'slug' => 'lenses',
                'description' => 'Fujinon lenses for X Mount and GF Mount cameras.',
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Accessories',
                'slug' => 'accessories',
                'description' => 'Camera accessories, bags, straps, and more.',
                'is_active' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Products
        $products = [
            // Mirrorless Cameras
            [
                'category_id' => 1,
                'name' => 'Fujifilm X-T5',
                'slug' => 'fujifilm-x-t5',
                'description' => '<p>The FUJIFILM X-T5 is a mirrorless digital camera that combines outstanding image quality with a compact, lightweight body. With its 40.2 megapixel X-Trans CMOS 5 HR sensor and powerful X-Processor 5, this camera delivers stunning images with exceptional detail.</p><p>Perfect for both photography enthusiasts and professionals who demand the highest quality in a portable package.</p>',
                'specifications' => [
                    'sensor' => 'APS-C X-Trans CMOS 5 HR',
                    'megapixels' => '40.2 MP',
                    'video_resolution' => '6.2K 30fps, 4K 60fps',
                    'stabilization' => '5-axis IBIS, up to 7 stops',
                    'iso_range' => '125-12800 (ext. 64-51200)',
                    'lens_mount' => 'Fujifilm X Mount',
                    'other_features' => "3-way tilting touchscreen LCD\n1.84M EVF\nDual SD card slots\nWeather resistant",
                ],
                'price' => 28999000,
                'stock_quantity' => 15,
                'sku' => 'FC-XT5-BK',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => 1,
                'name' => 'Fujifilm X-H2S',
                'slug' => 'fujifilm-x-h2s',
                'description' => '<p>The X-H2S is Fujifilm\'s flagship APS-C camera, designed for professionals who need speed and reliability. With its stacked BSI sensor and improved autofocus, it excels in both stills and video.</p>',
                'specifications' => [
                    'sensor' => 'APS-C X-Trans CMOS 5 HS (Stacked BSI)',
                    'megapixels' => '26.1 MP',
                    'video_resolution' => '6.2K 30fps, 4K 120fps',
                    'stabilization' => '5-axis IBIS, up to 7 stops',
                    'iso_range' => '160-12800 (ext. 80-51200)',
                    'lens_mount' => 'Fujifilm X Mount',
                    'other_features' => "40fps continuous shooting\nSubject detection AF\nCFexpress Type B + SD card slots",
                ],
                'price' => 39999000,
                'stock_quantity' => 8,
                'sku' => 'FC-XH2S-BK',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'category_id' => 1,
                'name' => 'Fujifilm X-S20',
                'slug' => 'fujifilm-x-s20',
                'description' => '<p>The X-S20 is a versatile all-rounder that combines excellent image quality with advanced video capabilities. Perfect for content creators and vloggers.</p>',
                'specifications' => [
                    'sensor' => 'APS-C X-Trans CMOS 4',
                    'megapixels' => '26.1 MP',
                    'video_resolution' => '6.2K 30fps, 4K 60fps',
                    'stabilization' => '5-axis IBIS, up to 7 stops',
                    'iso_range' => '160-12800 (ext. 80-51200)',
                    'lens_mount' => 'Fujifilm X Mount',
                    'other_features' => "Vlog mode\nExternal recording via HDMI\nCompact body design",
                ],
                'price' => 19999000,
                'stock_quantity' => 20,
                'sku' => 'FC-XS20-BK',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
            [
                'category_id' => 1,
                'name' => 'Fujifilm X100VI',
                'slug' => 'fujifilm-x100vi',
                'description' => '<p>The X100VI continues the legacy of the beloved X100 series with a new 40.2MP sensor and improved IBIS. The classic design meets cutting-edge technology.</p>',
                'specifications' => [
                    'sensor' => 'APS-C X-Trans CMOS 5 HR',
                    'megapixels' => '40.2 MP',
                    'video_resolution' => '6.2K 30fps, 4K 60fps',
                    'stabilization' => '5-axis IBIS',
                    'iso_range' => '125-12800 (ext. 64-51200)',
                    'lens_mount' => 'Fixed 23mm f/2 lens',
                    'other_features' => "Hybrid viewfinder (OVF/EVF)\nClassic rangefinder design\nBuilt-in ND filter",
                ],
                'price' => 27999000,
                'stock_quantity' => 5,
                'sku' => 'FC-X100VI-SV',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 4,
            ],
            // Instant Cameras
            [
                'category_id' => 3,
                'name' => 'Instax Mini 12',
                'slug' => 'instax-mini-12',
                'description' => '<p>The Instax Mini 12 is a fun and easy-to-use instant camera perfect for parties, events, and everyday moments. Features automatic exposure and a selfie mirror.</p>',
                'specifications' => [
                    'sensor' => 'N/A',
                    'megapixels' => 'N/A',
                    'video_resolution' => 'N/A',
                    'stabilization' => 'N/A',
                    'iso_range' => 'N/A',
                    'lens_mount' => 'Fixed lens',
                    'other_features' => "Automatic exposure\nSelfie mirror\nClose-up mode\nInstax Mini film",
                ],
                'price' => 1299000,
                'stock_quantity' => 50,
                'sku' => 'FC-MINI12-BL',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 10,
            ],
            [
                'category_id' => 3,
                'name' => 'Instax Wide 400',
                'slug' => 'instax-wide-400',
                'description' => '<p>Capture wider memories with the Instax Wide 400. Features a larger film format for more detailed instant photos.</p>',
                'specifications' => [
                    'sensor' => 'N/A',
                    'megapixels' => 'N/A',
                    'video_resolution' => 'N/A',
                    'stabilization' => 'N/A',
                    'iso_range' => 'N/A',
                    'lens_mount' => 'Fixed lens',
                    'other_features' => "Wide format film\nAutomatic exposure\nReal image finder",
                ],
                'price' => 1999000,
                'stock_quantity' => 25,
                'sku' => 'FC-WIDE400-BK',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 11,
            ],
            // Lenses
            [
                'category_id' => 4,
                'name' => 'XF 35mm f/1.4 R',
                'slug' => 'xf-35mm-f14-r',
                'description' => '<p>The legendary XF 35mm f/1.4 R is a classic prime lens known for its beautiful rendering and character. A must-have for portrait and street photography.</p>',
                'specifications' => [
                    'sensor' => 'N/A',
                    'megapixels' => 'N/A',
                    'video_resolution' => 'N/A',
                    'stabilization' => 'None',
                    'iso_range' => 'N/A',
                    'lens_mount' => 'Fujifilm X Mount',
                    'other_features' => "52mm filter thread\n6 groups, 8 elements\nMinimum focus: 28cm\nWeight: 187g",
                ],
                'price' => 9499000,
                'stock_quantity' => 12,
                'sku' => 'FC-XF3514-BK',
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 20,
            ],
            [
                'category_id' => 4,
                'name' => 'XF 56mm f/1.2 R WR',
                'slug' => 'xf-56mm-f12-r-wr',
                'description' => '<p>The XF 56mm f/1.2 R WR is a professional portrait lens with weather resistance. Delivers stunning bokeh and sharpness.</p>',
                'specifications' => [
                    'sensor' => 'N/A',
                    'megapixels' => 'N/A',
                    'video_resolution' => 'N/A',
                    'stabilization' => 'None',
                    'iso_range' => 'N/A',
                    'lens_mount' => 'Fujifilm X Mount',
                    'other_features' => "Weather resistant\n67mm filter thread\n8 groups, 11 elements\nLinear motor AF",
                ],
                'price' => 16999000,
                'stock_quantity' => 3,
                'sku' => 'FC-XF5612-BK',
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 21,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Create sample orders
        $orders = [
            [
                'customer_name' => 'John Doe',
                'customer_email' => 'john@example.com',
                'customer_phone' => '081234567890',
                'customer_whatsapp' => '081234567890',
                'shipping_address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'status' => 'pending',

            ],
            [
                'customer_name' => 'Jane Smith',
                'customer_email' => 'jane@example.com',
                'customer_phone' => '082345678901',
                'customer_whatsapp' => '082345678901',
                'shipping_address' => 'Jl. Gatot Subroto No. 456, Jakarta Pusat',
                'status' => 'confirmed',
                'confirmed_at' => now()->subDays(2),
            ],
            [
                'customer_name' => 'Bob Wilson',
                'customer_email' => 'bob@example.com',
                'customer_phone' => '083456789012',
                'customer_whatsapp' => '083456789012',
                'shipping_address' => 'Jl. Thamrin No. 789, Jakarta Pusat',
                'status' => 'completed',
                'confirmed_at' => now()->subDays(5),
                'shipped_at' => now()->subDays(3),
                'completed_at' => now()->subDays(1),
            ],
        ];

        foreach ($orders as $orderData) {
            $order = Order::create($orderData);

            // Add random items to each order
            $randomProducts = Product::inRandomOrder()->take(rand(1, 3))->get();

            foreach ($randomProducts as $product) {
                $quantity = rand(1, 2);
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'subtotal' => $product->price * $quantity,
                    'product_snapshot' => [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => $product->price,
                        'specifications' => $product->specifications,
                    ],
                ]);
            }

            // Update order total
            $order->total_amount = $order->items()->sum('subtotal');
            $order->save();
        }

        $this->command->info('Database seeded successfully with Fujifilm camera products!');
    }
}
