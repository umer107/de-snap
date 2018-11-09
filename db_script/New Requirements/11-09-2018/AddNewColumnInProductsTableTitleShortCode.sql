ALTER TABLE `de_products` ADD `title_shortcode` CHAR(2) NOT NULL AFTER `title`;


update de_products set title_shortcode = 'ER' where id = 1;
update de_products set title_shortcode = 'WB' where id = 2;
update de_products set title_shortcode = 'EB' where id = 3;
update de_products set title_shortcode = 'LD' where id = 4;
update de_products set title_shortcode = 'E' where id = 5;
update de_products set title_shortcode = 'DR' where id = 6;
update de_products set title_shortcode = 'P' where id = 7;
update de_products set title_shortcode = 'B' where id = 8;
update de_products set title_shortcode = 'T' where id = 9;