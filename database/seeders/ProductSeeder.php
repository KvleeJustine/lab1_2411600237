<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // Lumber
            ['name' => '2x4 Lumber (8ft)',        'sku' => 'LUM-001', 'category' => 'Lumber',    'description' => 'Standard construction grade 2x4 lumber, 8 feet long.',        'quantity' => 250, 'reorder_level' => 50,  'unit_price' => 185.00,  'supplier' => 'PhilLumber Corp'],
            ['name' => '4x4 Post (10ft)',           'sku' => 'LUM-002', 'category' => 'Lumber',    'description' => 'Heavy-duty 4x4 post for framing and structural support.',     'quantity' => 80,  'reorder_level' => 20,  'unit_price' => 420.00,  'supplier' => 'PhilLumber Corp'],
            ['name' => 'Plywood 1/2" (4x8)',        'sku' => 'LUM-003', 'category' => 'Lumber',    'description' => 'Marine-grade plywood sheet, 4x8 feet, 1/2 inch thick.',      'quantity' => 8,   'reorder_level' => 15,  'unit_price' => 950.00,  'supplier' => 'PhilLumber Corp'],

            // Hardware
            ['name' => 'Concrete Nails 4" (1kg)',   'sku' => 'HRD-001', 'category' => 'Hardware',  'description' => 'Hardened steel concrete nails, 4-inch, sold per kilogram.',   'quantity' => 120, 'reorder_level' => 30,  'unit_price' => 95.00,   'supplier' => 'FastenPro Inc'],
            ['name' => 'Wood Screws #10 (100pcs)',   'sku' => 'HRD-002', 'category' => 'Hardware',  'description' => 'Zinc-plated wood screws, #10 size, box of 100 pieces.',      'quantity' => 5,   'reorder_level' => 20,  'unit_price' => 145.00,  'supplier' => 'FastenPro Inc'],
            ['name' => 'Anchor Bolts 1/2" (10pcs)', 'sku' => 'HRD-003', 'category' => 'Hardware',  'description' => 'Expansion anchor bolts, 1/2 inch, pack of 10.',              'quantity' => 60,  'reorder_level' => 15,  'unit_price' => 210.00,  'supplier' => 'FastenPro Inc'],

            // Tools
            ['name' => 'Claw Hammer 16oz',           'sku' => 'TLS-001', 'category' => 'Tools',     'description' => 'Professional 16oz claw hammer with fiberglass handle.',      'quantity' => 25,  'reorder_level' => 10,  'unit_price' => 580.00,  'supplier' => 'ToolMaster PH'],
            ['name' => 'Circular Saw 7-1/4"',        'sku' => 'TLS-002', 'category' => 'Tools',     'description' => '7-1/4 inch circular saw, 1800W motor, with blade.',          'quantity' => 0,   'reorder_level' => 3,   'unit_price' => 3200.00, 'supplier' => 'ToolMaster PH'],
            ['name' => 'Steel Tape Measure 5m',      'sku' => 'TLS-003', 'category' => 'Tools',     'description' => 'Heavy-duty 5-meter steel tape measure with lock.',           'quantity' => 40,  'reorder_level' => 10,  'unit_price' => 220.00,  'supplier' => 'ToolMaster PH'],

            // Electrical
            ['name' => 'Electrical Wire 14AWG (50m)','sku' => 'ELC-001', 'category' => 'Electrical','description' => 'THHN 14 AWG electrical wire, 50-meter roll.',                 'quantity' => 30,  'reorder_level' => 10,  'unit_price' => 875.00,  'supplier' => 'WirePro Supply'],
            ['name' => 'Junction Box 4"x4"',          'sku' => 'ELC-002', 'category' => 'Electrical','description' => 'Metal junction box 4x4 inch for electrical connections.',    'quantity' => 4,   'reorder_level' => 10,  'unit_price' => 65.00,   'supplier' => 'WirePro Supply'],
            ['name' => 'Circuit Breaker 20A',         'sku' => 'ELC-003', 'category' => 'Electrical','description' => 'Single-pole 20 ampere circuit breaker.',                    'quantity' => 18,  'reorder_level' => 10,  'unit_price' => 320.00,  'supplier' => 'WirePro Supply'],

            // Plumbing
            ['name' => 'PVC Pipe 1/2" (6ft)',        'sku' => 'PLM-001', 'category' => 'Plumbing',  'description' => 'Schedule 40 PVC pipe, 1/2-inch diameter, 6 feet long.',     'quantity' => 150, 'reorder_level' => 40,  'unit_price' => 48.00,   'supplier' => 'HydroFit Corp'],
            ['name' => 'PVC Elbow 1/2" 90deg',       'sku' => 'PLM-002', 'category' => 'Plumbing',  'description' => 'PVC 90-degree elbow fitting, 1/2 inch.',                    'quantity' => 7,   'reorder_level' => 20,  'unit_price' => 12.00,   'supplier' => 'HydroFit Corp'],
            ['name' => 'Gate Valve 3/4"',             'sku' => 'PLM-003', 'category' => 'Plumbing',  'description' => 'Brass gate valve, 3/4-inch, for water line shutoff.',       'quantity' => 22,  'reorder_level' => 8,   'unit_price' => 185.00,  'supplier' => 'HydroFit Corp'],

            // Paint
            ['name' => 'Latex Paint White (4L)',      'sku' => 'PNT-001', 'category' => 'Paint',     'description' => 'Interior/exterior latex paint, flat white, 4-liter pail.',  'quantity' => 0,   'reorder_level' => 10,  'unit_price' => 620.00,  'supplier' => 'ColorPhil Inc'],
            ['name' => 'Metal Primer Gray (1L)',      'sku' => 'PNT-002', 'category' => 'Paint',     'description' => 'Rust-inhibiting metal primer, gray, 1-liter can.',           'quantity' => 45,  'reorder_level' => 10,  'unit_price' => 280.00,  'supplier' => 'ColorPhil Inc'],
            ['name' => 'Paint Roller Set',            'sku' => 'PNT-003', 'category' => 'Paint',     'description' => '9-inch paint roller frame with 2 roller covers included.',   'quantity' => 3,   'reorder_level' => 8,   'unit_price' => 195.00,  'supplier' => 'ColorPhil Inc'],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
