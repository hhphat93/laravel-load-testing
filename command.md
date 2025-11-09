php artisan make:migration <tên_migration> --path=database/migrations/<thư_mục_bạn_muốn>

chmod -R 777 app/Models/
chmod -R 777 database/migrations/

php artisan make:model Cart
php artisan make:model CartItem
php artisan make:model OrderItem

php artisan make:migration create_carts_table
php artisan make:migration create_cart_items_table
php artisan make:migration create_orders_table
php artisan make:migration create_order_items_table
php artisan make:migration create_order_reservations_table
