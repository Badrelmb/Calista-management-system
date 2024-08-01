DROP TABLE IF EXISTS items;

CREATE TABLE items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name_brand VARCHAR(100) NOT NULL,
    photos TEXT NOT NULL,
    date_of_purchase DATE NOT NULL,
    price_of_purchase DECIMAL(10, 2) NOT NULL,
    selling_price DECIMAL(10, 2),
    date_of_sale DATE
);

SHOW COLUMNS FROM items LIKE 'photos';
