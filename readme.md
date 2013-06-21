# What is Yii todo.MVC ?
Yii.todoMVC is example todo application with YII Framework.

# Installation:
Run the following SQL script before setup yii.TodoMVC application.
SQL:

```sql
CREATE TABLE IF NOT EXISTS `todolist` (
  `id_todo` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_todo`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
```
Clone this repository:

```sh
$ git clone git@github.com:fgursoy34/yii.TodoMVC.git
```

Build your virtual host configuration or run on built-in web server with the foloowing command:

```
$ php -s localhost:8080 -t ./yii.TodoMVC
```
# Contact:
https://www.facebook.com/fgursoy0034


