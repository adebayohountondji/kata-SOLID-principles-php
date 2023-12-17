DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS orders;

CREATE TABLE orders
(
    id        INT UNSIGNED PRIMARY KEY NOT NULL,
    sub_total DECIMAL(10, 2) UNSIGNED NOT NULL,
    vat_rate  DECIMAL(3, 2) UNSIGNED NOT NULL,
    total     DECIMAL(10, 2) UNSIGNED NOT NULL
);

CREATE TABLE items
(
    name     VARCHAR(100) NOT NULL,
    price    DECIMAL(10, 2) UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    order_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders (id) ON DELETE CASCADE,
    CONSTRAINT uc_items_name_order_id UNIQUE (name, order_id)
);