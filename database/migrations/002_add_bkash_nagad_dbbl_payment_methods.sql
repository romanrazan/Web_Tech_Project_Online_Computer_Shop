-- Add bKash, Nagad, and DBBL/Rocket payment methods to existing database
-- Run this only if your database was imported before this update.

ALTER TABLE orders
MODIFY payment_method ENUM('cash_on_delivery', 'online_wallet', 'bkash', 'nagad', 'dbbl') NOT NULL;

UPDATE orders
SET payment_method = 'bkash'
WHERE payment_method = 'online_wallet';

ALTER TABLE orders
MODIFY payment_method ENUM('cash_on_delivery', 'bkash', 'nagad', 'dbbl') NOT NULL;
