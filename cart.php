<?php
// Если пользователь нажал кнопку «Добавить в корзину» на странице товара, мы можем проверить данные формы.
if (isset($_POST['product_id'], $_POST['quantity']) && is_numeric($_POST['product_id']) && is_numeric($_POST['quantity'])) {
    // Установите переменные сообщения, чтобы мы могли легко их идентифицировать, также убедитесь, что они являются целыми числами.
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    // Подготовьте оператор SQL, мы в основном проверяем, существует ли продукт в нашей базе данных.
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_POST['product_id']]);
    // Получить продукт из базы данных и вернуть результат в виде массива
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Проверить, существует ли продукт (массив не пуст)
    if ($product && $quantity > 0) {
        // Товар существует в базе данных, теперь мы можем создать/обновить переменную сеанса для корзины
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            if (array_key_exists($product_id, $_SESSION['cart'])) {
                // Товар есть в корзине, просто обновите количество
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                // Товара нет в корзине, добавьте его
                $_SESSION['cart'][$product_id] = $quantity;
            }
        } else {
            // В корзине нет товаров, это добавит первый товар в корзину
            $_SESSION['cart'] = array($product_id => $quantity);
        }
    }
    // Запретить повторную отправку формы...
    header('location: index.php?page=cart');
    exit;
}

// Удалите продукт из корзины, проверьте параметр URL «удалить», это идентификатор продукта, убедитесь, что это число, и проверьте, есть ли он в корзине.
if (isset($_GET['remove']) && is_numeric($_GET['remove']) && isset($_SESSION['cart']) && isset($_SESSION['cart'][$_GET['remove']])) {
    // Удалить товар из корзины
    unset($_SESSION['cart'][$_GET['remove']]);
}

// Обновление количества товаров в корзине, если пользователь нажимает кнопку «Обновить» на странице корзины.
if (isset($_POST['update']) && isset($_SESSION['cart'])) {
    // Прокрутите данные сообщения, чтобы мы могли обновить количество для каждого продукта в корзине.
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'quantity') !== false && is_numeric($v)) {
            $id = str_replace('quantity-', '', $k);
            $quantity = (int)$v;
            // Всегда выполняйте проверки и проверки
            if (is_numeric($id) && isset($_SESSION['cart'][$id]) && $quantity > 0) {
                // Обновить новое количество
                $_SESSION['cart'][$id] = $quantity;
            }
        }
    }
    // Запретить повторную отправку формы...
    header('location: index.php?page=cart');
    exit;
}
// Отправьте пользователя на страницу оформления заказа, если он нажмет кнопку «Разместить заказ», также корзина не должна быть пустой
if (isset($_POST['placeorder']) && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    header('Location: index.php?page=placeorder');
    exit;
}

// Проверьте переменную сеанса для продуктов в корзине
$products_in_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
$products = array();
$subtotal = 0.00;
// Если в корзине есть товары
if ($products_in_cart) {
// В корзине есть товары, поэтому нам нужно выбрать эти товары из базы данных
     // Массив продуктов в корзине для массива строк вопросительного знака, нам нужно, чтобы оператор SQL включал IN (?,?,?,... и т. д.)
    $array_to_question_marks = implode(',', array_fill(0, count($products_in_cart), '?'));
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id IN (' . $array_to_question_marks . ')');
    // Нам нужны только ключи массива, а не значения, ключи - это идентификаторы продуктов.
    $stmt->execute(array_keys($products_in_cart));
    // Получить продукты из базы данных и вернуть результат в виде массива
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Рассчитать промежуточный итог
    foreach ($products as $product) {
        $subtotal += (float)$product['price'] * (int)$products_in_cart[$product['id']];
    }
}
?>

<?=template_header('Cart')?>

<div class="cart content-wrapper">
    <h1>Корзина покупателя</h1>
    <form action="index.php?page=cart" method="post">
        <table>
            <thead>
                <tr>
                    <td colspan="2">Продукты</td>
                    <td>Цена</td>
                    <td>Количество</td>
                    <td>Итог</td>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">У вас нет товаров, добавленных в корзину</td>
                </tr>
                <?php else: ?>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td class="img">
                        <a href="index.php?page=product&id=<?=$product['id']?>">
                            <img src="imgs/<?=$product['img']?>" width="50" height="50" alt="<?=$product['name']?>">
                        </a>
                    </td>
                    <td>
                        <a href="index.php?page=product&id=<?=$product['id']?>"><?=$product['name']?></a>
                        <br>
                        <a href="index.php?page=cart&remove=<?=$product['id']?>" class="remove">Удалить</a>
                    </td>
                    <td class="price">&dollar;<?=$product['price']?></td>
                    <td class="quantity">
                        <input type="number" name="quantity-<?=$product['id']?>" value="<?=$products_in_cart[$product['id']]?>" min="1" max="<?=$product['quantity']?>" placeholder="Quantity" required>
                    </td>
                    <td class="price">&dollar;<?=$product['price'] * $products_in_cart[$product['id']]?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="subtotal">
            <span class="text">Промежуточный итог</span>
            <span class="price">&dollar;<?=$subtotal?></span>
        </div>
        <div class="buttons">
            <input type="submit" value="Обновлять" name="update">
            <input type="submit" value="Разместить заказ" name="placeorder">
        </div>
    </form>
</div>

<?=template_footer()?>