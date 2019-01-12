# study

--Обновление на 13.01.19--
1. Подключение ajax с загрузкой в БД, обновленные файлы:
- config.json
- private/Base/Controller.php
- private/Base/DBConnection.php
- private/Base/Request.php
- private/Controllers/ShareController.php
- private/Models/PicModel.php
- private/Views/share.php
- public/js/collection.js

--Обновление на 11.01.19--

Обновленные файлы:
1. config.json (добавлен loadpics)
2. Controllers/ShareController(добавлен метод LoadPicsAction)
3. Models/PicModel (добавлен метод loadPics)
4. View/share.php (в action сейчас стоит /share/loadpics)

---про js и php----
в обновленных файлах share.php есть связь с js/collection.js. Как сделать так, чтобы после проверки js данные записывались?
внутри файла js писать команды для БД?..вроде бы так нельзя


--Обновление на 30.12.18--
1. Добавлены в Base Application, Request, Response, Router, Session
2. Обновлен index.php

--Обновление на 26.12.18--
1. Обновлен DBConnection.php
2. Обонвлен config.json
3. Обновлен index.php
4. Добавлена структура с базы данных (с workbench)

--Обновление 3 на 23.12.18---
Добавлен Cookies.php и перечисление таблиц для базы данных

--Обновление 2 на 23.12.18---
Добавлено подключение БД к проекту через PDO


--Обновление 23.12.18---
Полностью перенесен проект на MVC с FrontController
Инициализирован Composer c файлом composer.json
Создан файл config.json


--Обновление 20.12.18---
Создана структура проекта: public и private
Созданы Controllers и Models(пока один) и базовый Controller, добавлено пару страниц в View

--Обновление 10.12.18---
Страница share.html -> share.php 
Добавлен скрипт внутри файла, чтобы картинки в левом столбце (верхняя и нижняя) менялись случайным образом независимо друг от друга при перезагрузке страницы

--Обновление 05.12.18---
Добавлены две страницы на php - pencils.php и pic.php(открывается только при переходе на ссылку под картинкой "Подробнее")
