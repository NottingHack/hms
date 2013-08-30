CREATE TABLE IF NOT EXISTS `consumable_repeat_purchases` (
  `repeat_purchase_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `description` text,
  `min` text NOT NULL,
  `max` text NOT NULL,
  `area_id` int(11) NOT NULL,
  PRIMARY KEY (`repeat_purchase_id`)
) ENGINE=InnoDB DEFAULT ;