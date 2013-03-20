Hi

Yii todoMVC

SQL:

CREATE TABLE IF NOT EXISTS `todolist` (
  `id_todo` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_todo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



contact: https://www.facebook.com/fgursoy0034


