<?php
$basketItems = Eshop::getItemsFromBasket($id); // Передаем ID товара
if (!($basketItems instanceof Iterator)) {
    echo BASKET_SHOW_ERROR;
    throw new Exception('Бага в коде!');
    exit;
}
if ($basketItems instanceof EmptyIterator) {
    echo BASKET_SHOW_ERROR;
    echo "<p>Вернуться в <a href='/catalog'>каталог</a></p>";
    exit;
}
?>

<p>Вернуться в <a href='/catalog'>каталог</a></p>
<h1>Ваша корзина</h1>
<table>
<tr>
    <th>N п/п</th>
    <th>Название</th>
    <th>Автор</th>
    <th>Год издания</th>
    <th>Цена, руб.</th>
    <th>Количество</th>
    <th>Удалить</th>
</tr>
<?php
$totalAmount = 0;
$totalSum = 0;
$counter = 1;

foreach ($basketItems as $item):
    $totalAmount += $item->quantity;
    $totalSum += $item->price * $item->quantity;
?>
<tr>
    <td><?= $counter++ ?></td>
    <td><?= htmlspecialchars($item->title) ?></td>
    <td><?= htmlspecialchars($item->author) ?></td>
    <td><?= htmlspecialchars($item->pubyear) ?></td>
    <td><?= number_format($item->price, 2, ',', ' ') ?></td>
    <td><?= $item->quantity ?></td>
    <td>
        <a href='/remove_item_from_basket?id=<?= $item->id ?>'>Удалить</a>
    </td>
</tr>
<?php
endforeach;
?>
</table>

<p>Всего товаров в корзине: <?= $totalAmount ?> на сумму: <?= number_format($totalSum, 2, ',', ' ') ?> руб.</p>

<div style="text-align:center">
    <input type="button" value="Оформить заказ!"
                  onclick="location.href='/create_order'" />
</div>
