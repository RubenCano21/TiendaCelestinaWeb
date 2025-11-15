<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            // Bebidas
            [
                'codigo_producto' => 1001,
                'nombre' => 'Coca-Cola 2L',
                'categoria' => 'Bebidas',
                'precio_unitario' => 12.00,
                'unidad_medida' => 'Lts',
            ],
            [
                'codigo_producto' => 1002,
                'nombre' => 'Pepsi 2L',
                'categoria' => 'Bebidas',
                'precio_unitario' => 11.50,
                'unidad_medida' => 'Lts',
            ],
            [
                'codigo_producto' => 1003,
                'nombre' => 'Agua Mineral Vital 2L',
                'categoria' => 'Bebidas',
                'precio_unitario' => 6.00,
                'unidad_medida' => 'Lts',
            ],
            [
                'codigo_producto' => 1004,
                'nombre' => 'Cerveza Paceña 330ml',
                'categoria' => 'Bebidas',
                'precio_unitario' => 8.00,
                'unidad_medida' => 'ml',
            ],

            // Granos y Cereales
            [
                'codigo_producto' => 2001,
                'nombre' => 'Arroz Grano de Oro 1kg',
                'categoria' => 'Granos y Cereales',
                'precio_unitario' => 7.50,
                'unidad_medida' => 'kg',
            ],
            [
                'codigo_producto' => 2002,
                'nombre' => 'Azúcar San Aurelio 1kg',
                'categoria' => 'Granos y Cereales',
                'precio_unitario' => 6.50,
                'unidad_medida' => 'kg',
            ],
            [
                'codigo_producto' => 2003,
                'nombre' => 'Fideo Nutrimax 500g',
                'categoria' => 'Granos y Cereales',
                'precio_unitario' => 5.00,
                'unidad_medida' => 'gr',
            ],

            // Harinas y Productos de Panadería
            [
                'codigo_producto' => 3001,
                'nombre' => 'Harina Flor 1kg',
                'categoria' => 'Harinas',
                'precio_unitario' => 8.00,
                'unidad_medida' => 'kg',
            ],
            [
                'codigo_producto' => 3002,
                'nombre' => 'Pan de Batalla Unidad',
                'categoria' => 'Panadería',
                'precio_unitario' => 0.50,
                'unidad_medida' => 'Unidad',
            ],

            // Aceites y Grasas
            [
                'codigo_producto' => 4001,
                'nombre' => 'Aceite Cil 1L',
                'categoria' => 'Aceites',
                'precio_unitario' => 15.00,
                'unidad_medida' => 'Lts',
            ],
            [
                'codigo_producto' => 4002,
                'nombre' => 'Aceite Fino 900ml',
                'categoria' => 'Aceites',
                'precio_unitario' => 13.50,
                'unidad_medida' => 'ml',
            ],

            // Lácteos
            [
                'codigo_producto' => 5001,
                'nombre' => 'Leche PIL 1L',
                'categoria' => 'Lácteos',
                'precio_unitario' => 7.50,
                'unidad_medida' => 'Lts',
            ],
            [
                'codigo_producto' => 5002,
                'nombre' => 'Yogurt Bella Holanda 1L',
                'categoria' => 'Lácteos',
                'precio_unitario' => 9.00,
                'unidad_medida' => 'Lts',
            ],
            [
                'codigo_producto' => 5003,
                'nombre' => 'Queso Edam 250g',
                'categoria' => 'Lácteos',
                'precio_unitario' => 18.00,
                'unidad_medida' => 'gr',
            ],

            // Conservas y Enlatados
            [
                'codigo_producto' => 6001,
                'nombre' => 'Atún Van Camps 170g',
                'categoria' => 'Conservas',
                'precio_unitario' => 10.00,
                'unidad_medida' => 'gr',
            ],
            [
                'codigo_producto' => 6002,
                'nombre' => 'Sardina Isabel 425g',
                'categoria' => 'Conservas',
                'precio_unitario' => 12.00,
                'unidad_medida' => 'gr',
            ],
            [
                'codigo_producto' => 6003,
                'nombre' => 'Arvejas Enlatadas Arcor 300g',
                'categoria' => 'Conservas',
                'precio_unitario' => 8.50,
                'unidad_medida' => 'gr',
            ],

            // Condimentos y Especias
            [
                'codigo_producto' => 7001,
                'nombre' => 'Sal Embol 1kg',
                'categoria' => 'Condimentos',
                'precio_unitario' => 3.00,
                'unidad_medida' => 'kg',
            ],
            [
                'codigo_producto' => 7002,
                'nombre' => 'Comino Molido 100g',
                'categoria' => 'Especias',
                'precio_unitario' => 5.50,
                'unidad_medida' => 'gr',
            ],

            // Snacks
            [
                'codigo_producto' => 8001,
                'nombre' => 'Papas Pringles 124g',
                'categoria' => 'Snacks',
                'precio_unitario' => 15.00,
                'unidad_medida' => 'gr',
            ],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }

        $this->command->info('20 productos de abarrotes creados exitosamente.');
    }
}

