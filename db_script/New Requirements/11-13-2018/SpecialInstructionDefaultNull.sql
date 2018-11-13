ALTER TABLE `de_leads` CHANGE `special_instructions` `special_instructions` VARCHAR(120) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL;

ALTER TABLE `de_leads` ADD `country_id` INT NULL AFTER `updated_by`;

ALTER TABLE `de_leads` CHANGE `state` `state_id` INT(11) NOT NULL;

ALTER TABLE `de_leads` CHANGE `state_id` `state_id` INT(11) NULL;