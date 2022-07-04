<?php
// Убедитесь, что параметр id указан в URL-адресе.
if (isset($_GET['id'])) {
    // Подготовить оператор и выполнить, предотвращает внедрение SQL
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    // Получить продукт из базы данных и вернуть результат в виде массива
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    // Проверить, существует ли продукт (массив не пуст)
    if (!$product) {
        // Простая ошибка для отображения, если идентификатор продукта не существует (массив пуст)
        exit('Product does not exist!');
    }
} else {
    // Простая ошибка для отображения, если идентификатор не был указан
    exit('Product does not exist!');
}
?>

<?=template_header('Product')?>

<div class="product content-wrapper">
    <img src="imgs/<?=$product['img']?>" width="500" height="500" alt="<?=$product['name']?>">
    <div>
        <h1 class="name"><?=$product['name']?></h1>
        <span class="price">
            &dollar;<?=$product['price']?>
            <?php if ($product['rrp'] > 0): ?>
            <span class="rrp">&dollar;<?=$product['rrp']?></span>
            <?php endif; ?>
        </span>
        <form action="index.php?page=cart" method="post">
            <input type="number" name="quantity" value="1" min="1" max="<?=$product['quantity']?>" placeholder="Quantity" required>
            <input type="hidden" name="product_id" value="<?=$product['id']?>">
            <input type="submit" value="Добавить в корзину">
        </form>
        <div class="description">
            <?=$product['desc']?>
        </div>
    </div>
</div>

<?=template_footer()?>