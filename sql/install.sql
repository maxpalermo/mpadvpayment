CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_advpayment_configuration` ( 
    `id_configuration` INT NOT NULL AUTO_INCREMENT , 
    `fee_type` INT NOT NULL , 
    `fee_amount` DECIMAL(20,6) NOT NULL , 
    `fee_percent` DECIMAL(20,6) NOT NULL , 
    `fee_min` DECIMAL(20,6) NOT NULL , 
    `fee_max` DECIMAL(20,6) NOT NULL , 
    `order_min` DECIMAL(20,6) NOT NULL , 
    `order_max` DECIMAL(20,6) NOT NULL , 
    `order_free` DECIMAL(20,6) NOT NULL , 
    `discount`  DECIMAL(20,6) NOT NULL,
    `tax_included` BOOLEAN NOT NULL,
    `tax_rate` DECIMAL(20,6) NOT NULL,
    `carriers` TEXT NOT NULL,
    `categories` TEXT NOT NULL,
    `manufacturers` TEXT NOT NULL,
    `suppliers` TEXT NOT NULL,
    `products` TEXT NOT NULL,
    `id_order_state` INT NOT NULL,
    `payment_type` VARCHAR(30) NOT NULL,
    `is_active` BOOLEAN NOT NULL,
    PRIMARY KEY (`id_configuration`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_advpayment_fee` ( 
    `id_fee` INT NOT NULL AUTO_INCREMENT, 
    `id_order` INT NOT NULL , 
    `total_paid` DECIMAL(20,6) NOT NULL , 
    `total_paid_tax_incl` DECIMAL(20,6) NOT NULL , 
    `total_paid_tax_excl` DECIMAL(20,6) NOT NULL , 
    `total_paid_real` DECIMAL(20,6) NOT NULL , 
    `fee` DECIMAL(20,6) NOT NULL , 
    `fee_tax_incl` DECIMAL(20,6) NOT NULL , 
    `fee_tax_excl` DECIMAL(20,6) NOT NULL , 
    `fee_tax_rate` DECIMAL(20,6) NOT NULL , 
    `total_document` DECIMAL(20,6) NOT NULL , 
    `total_document_tax_incl` DECIMAL(20,6) NOT NULL , 
    `total_document_tax_excl` DECIMAL(20,6) NOT NULL , 
    `transaction_id` VARCHAR(255) NOT NULL , 
    `payment_type` VARCHAR(30),
    `date_add` DATE NOT NULL , 
    `date_upd` TIMESTAMP NOT NULL , 
    PRIMARY KEY (`id_fee`)
) ENGINE = InnoDB;

ALTER TABLE `{_DB_PREFIX_}mp_advpayment_fee` 
ADD UNIQUE INDEX `order_unique` (`id_order`);