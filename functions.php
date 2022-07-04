<?php
function pdo_connect_mysql() {
    // данные MySQL.
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'shoppingcart';
    try {
    	return new PDO('mysql:host=' . $DATABASE_HOST . ';dbname=' . $DATABASE_NAME . ';charset=utf8', $DATABASE_USER, $DATABASE_PASS);
    } catch (PDOException $exception) {
    	// Если есть ошибка с подключением, остановите скрипт и отобразите ошибку.
    	exit('Failed to connect to database!');
    }
}

// Заголовок шаблона
function template_header($title) {
 $num_items_in_cart = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;   
echo <<<EOT
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>$title</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
       <header class="site-header"> 
            <nav class="site-navigation">
                 <a class="logo-link" href="index.php">
                 <img src ="imgs/ecolife.png" width="100" height="100" alt="Логотип магазина ecolife">
                     </a>
                    <ul class="navigation-list">
                    <li><h1><a href="index.php?page=products">Каталог</a></h1></li>
                    <li><h1><a href="index.php?page=contacts">Контакты</a></h1></li>
                    <li><h1><a href="index.php?page=delivery">Доставка</a></h1></li>
                    <li><h1><a href="index.php?page=cart"> <i class="fas fa-shopping-cart"></i> 
                    <span>$num_items_in_cart</span> </a></h1></li>
                    </ul>  
                 </nav>
        </header>
        <main>
EOT;
}


// Нижний колонтитул шаблона
function template_footer() {
$year = date('Y');
echo <<<EOT
        </main>
            <footer class="site-footer">
            <div class="container">
             <p>&copy; $year, ecolife</p>
              <ul class="navigation-list">
                <li><a href="index.php?page=products">Каталог</a></li>
                <li><a href="index.php?page=delivery">Доставка</a></li>
                <li><a href="index.php?page=contacts">Контакты</a></li>
              </ul>
              <ul class="social-list">
                <li>
                  <a class="social-link-VK" href="https://vk.com">
                    <span class="visually-hidden">Вконтакте</span>
                  </a>
                </li>
                <li>
                  <a class="social-link-Odnoklassniki" href="https://ok.ru">
                    <span class="visually-hidden">Одноклассники</span>
                  </a>
                </li>
              </ul>
            </div>
    </footer>
        <script src="script.js"></script>
    </body>
</html>
EOT;
}
?>

