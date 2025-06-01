<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Treatment;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'admin@kulitkuskincare.com',
            'password' => Hash::make('password'),
        ]);

        // Create treatments
        $treatments = [
            [
                'name' => 'Facial Treatment',
                'price' => 350000,
                'description' => 'Deep cleaning facial treatment for all skin types.',
                'is_bundle' => false,
            ],
            [
                'name' => 'Acne Treatment',
                'price' => 450000,
                'description' => 'Specialized treatment for acne-prone skin.',
                'is_bundle' => false,
            ],
            [
                'name' => 'Chemical Peeling',
                'price' => 550000,
                'description' => 'Chemical exfoliation to improve skin texture and tone.',
                'is_bundle' => false,
            ],
            [
                'name' => 'Whitening Mask',
                'price' => 250000,
                'description' => 'Brightening mask treatment to reduce hyperpigmentation.',
                'is_bundle' => false,
            ],
            [
                'name' => 'Skin Hydration',
                'price' => 300000,
                'description' => 'Deep hydration treatment for dry skin.',
                'is_bundle' => false,
            ],
            [
                'name' => 'Complete Facial Package',
                'price' => 850000,
                'description' => 'Complete facial package including facial treatment, mask, and hydration.',
                'is_bundle' => true,
                'bundle_name' => 'Facial Premium Package',
            ],
            [
                'name' => 'Acne Control Package',
                'price' => 950000,
                'description' => 'Complete acne treatment package including facial, peeling, and specialized care.',
                'is_bundle' => true,
                'bundle_name' => 'Acne Solution Package',
            ],
            [
                'name' => 'Skin Brightening Package',
                'price' => 1200000,
                'description' => 'Complete brightening package for dull skin, including facial, peeling, and whitening mask.',
                'is_bundle' => true,
                'bundle_name' => 'Glow Boost Package',
            ],
        ];

        foreach ($treatments as $treatment) {
            \App\Models\Treatment::create($treatment);
        }

        // Create customers
        $customers = [
            [
                'customer_code' => 'CUST-001',
                'name' => 'Anisa Wijaya',
                'email' => 'anisa@example.com',
                'phone' => '081234567890',
            ],
            [
                'customer_code' => 'CUST-002',
                'name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567891',
            ],
            [
                'customer_code' => 'CUST-003',
                'name' => 'Citra Dewi',
                'email' => 'citra@example.com',
                'phone' => '081234567892',
            ],
        ];

        foreach ($customers as $customer) {
            \App\Models\Customer::create($customer);
        }

        // Create sample orders
        $orders = [
            [
                'order_code' => 'ORD-20230501-0001',
                'customer_id' => 1,
                'total_amount' => 350000,
                'order_date' => '2023-05-01',
                'status' => 'completed',
                'notes' => 'First time customer',
                'items' => [
                    [
                        'treatment_id' => 1,
                        'quantity' => 1,
                        'price' => 350000,
                        'subtotal' => 350000,
                    ],
                ],
            ],
            [
                'order_code' => 'ORD-20230515-0002',
                'customer_id' => 2,
                'total_amount' => 850000,
                'order_date' => '2023-05-15',
                'status' => 'completed',
                'notes' => 'Regular customer',
                'items' => [
                    [
                        'treatment_id' => 6,
                        'quantity' => 1,
                        'price' => 850000,
                        'subtotal' => 850000,
                    ],
                ],
            ],
            [
                'order_code' => 'ORD-20230601-0003',
                'customer_id' => 3,
                'total_amount' => 1200000,
                'order_date' => '2023-06-01',
                'status' => 'completed',
                'notes' => 'Package treatment',
                'items' => [
                    [
                        'treatment_id' => 8,
                        'quantity' => 1,
                        'price' => 1200000,
                        'subtotal' => 1200000,
                    ],
                ],
            ],
            [
                'order_code' => 'ORD-20230615-0004',
                'customer_id' => 1,
                'total_amount' => 900000,
                'order_date' => '2023-06-15',
                'status' => 'completed',
                'notes' => 'Multiple treatments',
                'items' => [
                    [
                        'treatment_id' => 1,
                        'quantity' => 1,
                        'price' => 350000,
                        'subtotal' => 350000,
                    ],
                    [
                        'treatment_id' => 4,
                        'quantity' => 1,
                        'price' => 250000,
                        'subtotal' => 250000,
                    ],
                    [
                        'treatment_id' => 5,
                        'quantity' => 1,
                        'price' => 300000,
                        'subtotal' => 300000,
                    ],
                ],
            ],
            [
                'order_code' => 'ORD-20230705-0005',
                'customer_id' => 2,
                'total_amount' => 550000,
                'order_date' => '2023-07-05',
                'status' => 'completed',
                'notes' => '',
                'items' => [
                    [
                        'treatment_id' => 3,
                        'quantity' => 1,
                        'price' => 550000,
                        'subtotal' => 550000,
                    ],
                ],
            ],
            [
                'order_code' => 'ORD-'.date('Ymd').'-0006',
                'customer_id' => 3,
                'total_amount' => 950000,
                'order_date' => date('Y-m-d'),
                'status' => 'pending',
                'notes' => 'Recent order',
                'items' => [
                    [
                        'treatment_id' => 7,
                        'quantity' => 1,
                        'price' => 950000,
                        'subtotal' => 950000,
                    ],
                ],
            ],
        ];

        foreach ($orders as $orderData) {
            $items = $orderData['items'];
            unset($orderData['items']);
            
            $order = \App\Models\Order::create($orderData);
            
            foreach ($items as $item) {
                $item['order_id'] = $order->id;
                \App\Models\OrderItem::create($item);
            }
        }
    }
}
