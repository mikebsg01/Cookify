-- Schema db_cookify
CREATE SCHEMA IF NOT EXISTS `db_cookify` DEFAULT CHARACTER SET utf8 ;

USE `db_cookify` ;

-- Table `db_cookify`.`images`
CREATE TABLE IF NOT EXISTS `db_cookify`.`images` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_path` TEXT NOT NULL,
  `file_name` TEXT NOT NULL,
  `file_extension` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;


-- Table `db_cookify`.`users`
CREATE TABLE IF NOT EXISTS `db_cookify`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `first_name` VARCHAR(25) NOT NULL,
  `last_name` VARCHAR(25) NOT NULL,
  `full_name` VARCHAR(51) NOT NULL,
  `photo_id` INT NOT NULL,
  `phone_number` VARCHAR(10) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password` TEXT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_users_images_idx` (`photo_id` ASC),
  CONSTRAINT `fk_users_images`
    FOREIGN KEY (`photo_id`)
    REFERENCES `db_cookify`.`images` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION
) ENGINE = InnoDB;


-- Table `db_cookify`.`categories`
CREATE TABLE IF NOT EXISTS `db_cookify`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `slug` VARCHAR(60) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB;


-- Table `db_cookify`.`plates`
CREATE TABLE IF NOT EXISTS `db_cookify`.`plates` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NULL,
  `slug` VARCHAR(60) NULL,
  `description` TEXT NULL,
  `price` DECIMAL(6,2) NULL,
  `category_id` INT NOT NULL,
  `image_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_plates_categories_idx` (`category_id` ASC),
  INDEX `fk_plates_images_idx` (`image_id` ASC),
  CONSTRAINT `fk_plates_categories`
    FOREIGN KEY (`category_id`)
    REFERENCES `db_cookify`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_plates_images`
    FOREIGN KEY (`image_id`)
    REFERENCES `db_cookify`.`images` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- Table `db_cookify`.`orders`
CREATE TABLE IF NOT EXISTS `db_cookify`.`orders` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `comment` TEXT NULL,
  `address` TEXT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_orders_users_idx` (`user_id` ASC),
  CONSTRAINT `fk_orders_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `db_cookify`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- Table `db_cookify`.`orders_has_plates`
CREATE TABLE IF NOT EXISTS `db_cookify`.`orders_has_plates` (
  `order_id` INT NOT NULL,
  `plate_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX `fk_orders_has_plates_plates_idx` (`plate_id` ASC),
  INDEX `fk_orders_has_plates_orders_idx` (`order_id` ASC),
  CONSTRAINT `fk_orders_has_plates_orders`
    FOREIGN KEY (`order_id`)
    REFERENCES `db_cookify`.`orders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_has_plates_plates`
    FOREIGN KEY (`plate_id`)
    REFERENCES `db_cookify`.`plates` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- Table `db_cookify`.`invoices`
CREATE TABLE IF NOT EXISTS `db_cookify`.`invoices` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `subtotal` DECIMAL(8,2) NOT NULL,
  `iva` DECIMAL(5,2) NOT NULL,
  `total` DECIMAL(8,2) NOT NULL,
  `order_id` INT NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `fk_invoices_orders_idx` (`order_id` ASC),
  CONSTRAINT `fk_invoices_orders`
    FOREIGN KEY (`order_id`)
    REFERENCES `db_cookify`.`orders` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;