<?php
// Получаем заказы
$orders = Eshop::getOrders();

// Проверяем, является ли результат итератором
if (!($orders instanceof Iterator)) {
    echo ORDERS_SHOW_ERROR;
    throw new Exception('Ошибка в коде: ожидается объект типа Iterator.');
    exit;
}

// Если заказы пусты, выводим сообщение об ошибке
if ($orders instanceof EmptyIterator) {
    echo ORDERS_SHOW_ERROR;
    exit;
}
?>

<h1>Поступившие заказы:</h1>
<a href='/admin'>Назад в админку</a>
<hr>

<?php
// Перебираем заказы и выводим их данные
foreach ($orders as $order) {
    ?>
    <h2>Заказ номер: <?= htmlspecialchars($order['id']) ?></h2>
    <p><b>Заказчик</b>: <?= htmlspecialchars($order['customer_name']) ?></p>
    <p><b>Email</b>: <?= htmlspecialchars($order['email']) ?></p>
    <p><b>Телефон</b>: <?= htmlspecialchars($order['phone']) ?></p>
    <p><b>Адрес доставки</b>: <?= htmlspecialchars($order['delivery_address']) ?></p>
    <p><b>Дата размещения заказа</b>: <?= htmlspecialchars($order['order_date']) ?></p>

    <h3>Купленные товары:</h3>
    <table>
        <tr>
            <th>N п/п</th>
            <th>Название</th>
            <th>Автор</th>
            <th>Год издания</th>
            <th>Цена, руб.</th>
            <th>Количество</th>
        </tr>
        <?php
        $totalPrice = 0;
        $totalItems = 0;

        foreach ($order['items'] as $index => $item) {
            $totalPrice += $item['price'] * $item['quantity'];
            $totalItems += $item['quantity'];
            ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= htmlspecialchars($item['title']) ?></td>
                <td><?= htmlspecialchars($item['author']) ?></td>
                <td><?= htmlspecialchars($item['year']) ?></td>
                <td><?= htmlspecialchars($item['price']) ?></td>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
            </tr>
            <?php
        }
        ?>
    </table>
    <p>Всего товаров в заказе: <?= $totalItems ?> на сумму: <?= $totalPrice ?> руб.</p>
    <hr>
    <?php
}
?>

