Weblife CMS
================================================================================

Сайт-магазин с возможностью управлением контента

ОСОБЕННОСТИ:
 - простое подключение
 - простое управление
 - шаблонизатор smarty

СТРУКТУРА ПАПОК
--------------------------------------------------------------------------------

    admin/      содержит админские модули (закрытая папка из web)
        ajax/   содержит админские модули запускаемых в iframe  (закрытая)
    backup/     содержит бекапы БД системы  (закрытая)
    cronjobs/   содержит консольные приложение  (закрытая)
    css/        содержит файлы стилей  (открытая)
    flash/      содержит flash файлы  (открытая)
    fonts/      содержит необходимые шрифты  (открытая)
    images/     содержит изображения  (открытая)
    includes/   содержит файлы ядра (закрытая)
    interactive/содержит оперативные файлы  (открытая)
    js/         содержит скрипты (открытая)
    module/     содержит модули для фронтенда (закрытая)
    seo/
    sxd/        содержит Sypex Dumper Pro 2.0.11
    nbproject/  содержит файлы проекта для NetBeans IDE (закрытая, не выкачивается на хостинг)
    temp/       содержит временные оперативные файлы (закрытая)
    tpl/        содержит шаблоны smarty (закрытая)
    uploaded/   содержит файлы для загрузки через TinyMCE (открытая)
    verstka/    содержит первоначальную верстку (открытая)
... 



ВИМОГИ
--------------------------------------------------------------------------------

 - PHP 5.4.0
 - MySql 
 - MemCache
 - optipng
 - jpegoptim
 - gifsicle
...

УСТАНОВКА
--------------------------------------------------------------------------------

### Файлы 
 - через ftp всех файлов 
 - локально
 - svn

### База данных 
 - запуск файла через браузер /dumper.php
 - импорт последнего архива из папки /backup


КОНФІГУРАЦІЯ
--------------------------------------------------------------------------------

### База данных

Редактировать файл `/includes/system/SystemComponent.php` в фунции initDBSettings() с реальными данными, например:

```php
array(
    "dbhost"     => "localhost",
    "dbusername" => "example_user",
    "dbpassword" => "1232321",
    "dbname"     => "example_name"
)
```

**Заметки:**
- Обязательно нуждно вручную создать базу данных.



ТЕСТИРОВАНИЕ
--------------------------------------------------------------------------------



ЗАМЕТКИ ПО РАЗРАБОТКЕ
--------------------------------------------------------------------------------
1. Для сохранения соответствия фронтенда и бекенда и путей загрузки файлов было решено, 
что именно конечные товары будут называться catalog, а не product, как изначально планировалось, а модели будут models

2. Размеры картинок для Каталога товаров (не утверждены окончательно)
- big       540x620
- middle    278x299
- small     88x116
- thumb     77x83"# maikoff" 
