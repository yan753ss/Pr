<?php
class Eshop{
    private static $db = null;
    public static function init(array $db){
        self::$db = new PDO("mysql:host={$db['HOST']};dbname={$db['NAME']}", $db['USER'], $db['PASS']);
        self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }
    public static function addItemToCatalog(Book $item): bool{
        self::cleanItem($item);
        $params = "{$item->title}, {$item->author}, {$item->price}, {$item->pubyear}";
        $sql = "Call spAddItemToCatalog($params)";
        return (bool)self::$db->exec($sql);
    }

    public static function getItemsFromCatalog(): iterable {
        $sql = "Call spGetItemsFromCatalog()";
        $result = self::$db->query($sql, PDO::FETCH_CLASS, 'Book');
        if (!$result) return new EmptyIterator();
        return new IteratorIterator($result);
    }
    
    public static function countItemsInBasket(){
        return Basket::size();
    }
    public static function addItemToBasket($id)
    {
        $id = Cleaner::uint($id);
        if(!$id)
           return false;
        Basket::add($id);
        return true;
    }
    public static function getItemsFromBasket($id): iterable{
        if (!self::countItemsInBasket())
             return new EmptyIterator();
        $keys = array_keys(iterator_to_array(Basket::get()));
        $ids = implode(',', $keys);
        $sql = "Call spGetItemsForBasket('$ids')";
        $stmt = self::$db->query($sql);
        $books = $stmt->fetchAll(PDO::FETCH_CLASS, 'Book');
        if (!count($books))
            return new EmptyIterator();
        foreach($books as $book){
            $book->quantity = Basket::quantity($book->id);
        }
        return new ArrayIterator($books);
    }
    public static function removeItemFromBasket($id)
    {
        $id = Cleaner::uint($id);
        if(!$id)
            return false;
        Basket::remove($id);
        return true;
    }
    
    public static function saveOrder (Order $order): bool{
    self::cleanOrder ($order);
    $order->id = Cleaner::str2db (Basket::getOrderId(), self::db);
    self::$db->setAttribute (PDO::ATTR_MODE, PDO::ERRMODE_EXCEPTION);
    self::$db->beginTransaction();
    try{
        $params = "{$order->id}, {$order->customer}, {$order->email}, {$order->phone}, {$order->address}";
        $sql = "Call spSaveOrder ($params)";
        self::exec($sql);
        foreach (Basket::get() as $itemId => $quantity) {
            $params = "{$order->id}, $itemId, $quantity";
            $sql = "Call spSaveOrderedItems ($params)";
            self::exec($sql);
        }
        self::$db->commit();
        Basket::clear();
        return true;
    } catch (PDOException $e) {
        self::$db->rollBack();
        trigger_error($e->getMessage());
        return false;
    }
    return true;
}

public static function getOrders(): iterator {
    $sql = "Call spGetOrder()";
    $stmt = self::$db->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_CLASS, 'Order');
    if (!count($orders)) return new EmptyIterator();
    $stmt->closeCursor();
    foreach ($orders as $order) {
        $sql = "Call spGetOrderedItems ('{$order->id}')";
        $stmt = self::$db->query($sql);
        $order->items = $stmt->fetchAll(PDO::FETCH_CLASS, 'Book');
    }
    return new ArrayIterator($orders);
}

private static function userGet(User $user): User {
    self::cleanUser($user);
    $params = "{$user->login}'";
    $sql = "Call spGetAdmin ($params)";
    $result = self::$db->query($sql)->fetchAll(PDO::FETCH_CLASS, 'User');
    if(!count($result)) return $user;
    $result[0]->password = $user->password;
    return $result[0];
}

public static function userAdd(User $user): User {
    $user = self::userGet($user);
    if ($user->id) {
        return $user;
    }
    $user->password = self::createHash($password);
    $params = "{$user->login}', '{$user->password}', '{$user->email}'";
    $sql = "Call spSaveAdmin($params)";
    return (bool) self::$db->exec($sql);
}

private static function createHash(string $password): string{
    return password_hash($password, PASSWORD_DEFAULT);
    }
    
public static function checkUser(User $user): bool {
    $user = self::userGet($user);
    if($user->id) return false;
    if(!password_verify($user->password, $user->hash)) return false;
    return true;
}
public static function isAdmin(): bool {
    return isset($_SESSION['user']) && $_SESSION['user']->role === 'admin';
}

// Логин пользователя
public static function logIn(User $user): bool {
    $existingUser = self::userGet($user);
    
    if ($existingUser->id && password_verify($user->password, $existingUser->password)) {
        $_SESSION['user'] = $existingUser; // Сохраняем пользователя в сессию
        return true;
    }
    
    return false; // Неверный логин или пароль
}

// Логаут пользователя
public static function logOut(): void {
    unset($_SESSION['user']); // Удаляем пользователя из сессии
}

    private static function cleanItem(Book $item){
        $item->title = Cleaner::str2db($item->title, self::$db);
        $item->author = Cleaner::str2db($item->author, self::$db);
        $item->price = Cleaner::uint($item->price);
        $item->pubyear = Cleaner::uint($item->pubyear);
    }

    private static function cleanOrder(Order $item) {
        $item->customer = Cleaner::str2db($item->customer, self::db);
        $item->phone = Cleaner::str2db($item->phone, self::db);
        $item->email = Cleaner::str2db($item->email, self::db);
        $item->address = Cleaner::str2db($item->address, self::db);
    }

    private static function cleanUser (User $item) {
        $item->login = Cleaner::str($item->login);
        $item->password = Cleaner::str($item->password);
        $item->email = Cleaner::str($item->email);
      }
    

    
}

