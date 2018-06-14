
use openseedcrm;

ALTER TABLE `de_userdetail` ADD `product_shortcode` VARCHAR(8) NULL AFTER `product`;


ALTER TABLE `de_userdetail` ADD `assignto_shortcode` VARCHAR(8) NULL AFTER `assign_to`;