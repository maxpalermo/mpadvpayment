CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_adv_payment_exclusions` ( 
    `id_adv_payment_exclusions` INT NOT NULL AUTO_INCREMENT , 
    `id_product` INT NOT NULL , 
    `cash` BOOLEAN NOT NULL , 
    `bankwire` BOOLEAN NOT NULL , 
    `paypal` BOOLEAN NOT NULL , 
    PRIMARY KEY (`id_adv_payment_exclusions`) 
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_adv_payment_carriers` ( 
    `id_adv_payment_carriers` INT NOT NULL AUTO_INCREMENT , 
    `id_adv_payment_payments` INT NOT NULL , 
    `id_carrier` INT(10) NOT NULL , 
    `cash` BOOLEAN NOT NULL , 
    `bankwire` BOOLEAN NOT NULL , 
    `paypal` BOOLEAN NOT NULL , 
    PRIMARY KEY (`id_adv_payment_carriers`)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `{_DB_PREFIX_}mp_adv_payment_payments` ( 
    `id_adv_payment_payments` INT NOT NULL AUTO_INCREMENT , 
    `id_cart` INT NOT NULL , 
    `id_order` INT NOT NULL , 
    `total_amount` DECIMAL(20,6) NOT NULL , 
    `tax_rate` DECIMAL(20,6) NOT NULL , 
    `discounts` DECIMAL(20,6) NOT NULL , 
    `fee_amount` DECIMAL(20,6) NOT NULL , 
    `fee_percent` DECIMAL(20,6) NOT NULL ,
    `fee_type` INT NOT NULL ,
    `fee_min` DECIMAL(20,6) NOT NULL , 
    `fee_max` DECIMAL(20,6) NOT NULL , 
    `order_min` DECIMAL(20,6) NOT NULL , 
    `order_max` DECIMAL(20,6) NOT NULL , 
    `order_free` DECIMAL(20,6) NOT NULL , 
    `transaction_id` VARCHAR(255) NOT NULL , 
    `payment_type` VARCHAR(30),
    PRIMARY KEY (`id_adv_payment_payments`)
) ENGINE = InnoDB;

ALTER TABLE `{_DB_PREFIX_}mp_adv_payment_carriers`
  ADD CONSTRAINT `FK_payments` FOREIGN KEY (`id_adv_payment_payments`) 
  REFERENCES `{_DB_PREFIX_}mp_adv_payment_payments` (`id_adv_payment_payments`) ON DELETE CASCADE ON UPDATE CASCADE;