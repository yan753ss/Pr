<?php
error_reporting(E_ALL);

const CATALOG_ITEM_ADD_OK = 'Товар добавлен в каталог';
const CATALOG_ITEM_ADD_ERROR = 'Ошибка при добавлении товара в каталог';
const CATALOG_SHOW_ERROR = 'Ошибка при показе каталога';
const BASKET_SHOW_ERROR = 'Ошибка при показе корзины';
const ORDER_SAVED_OK = 'Ошибка при оформлении заказа';
const ORDER_SAVED_ERROR = 'Ваш заказ успешно оформлен';
const ORDERS_SHOW_ERROR = 'Ошибка при создании заказа';
const USER_ADD_DUBLICATE = 'Пользователь с таким именем уже существует';
const USER_ADD_OK = 'Пользователь создан';
const USER_ADD_ERROR = 'Ошибка при создании пользователя';
const USER_LOGIN_ERROR = 'Логин или пароль не верны';
require_once 'core/init.php';
require_once 'app/__header.php';
require_once 'app/__router.php';
require_once 'app/__footer.php';
