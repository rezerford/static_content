CREATE TABLE IF NOT EXISTS `#__staticcontent_dashboard_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__staticcontent_file_index` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename_full` varchar(250) NOT NULL,
  `modified_date` int(20) NOT NULL,
  `article_id` int(15) NOT NULL,
  `path` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;