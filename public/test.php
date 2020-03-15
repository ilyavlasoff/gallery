<?php
$host = '172.19.0.2';
$port = '5432';
$user = 'postgres';
$password = 'mypassword';//заменить на ваш пароль к БД
$dbname = 'postgres';//заменить на ваше название БД

$dsn = "pgsql:host={$host};port={$port};user={$user};password={$password};dbname={$dbname}";
$connection = new PDO($dsn);
$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$prepared = $connection->prepare('SELECT * FROM test');//заменить на запрос к вашей таблице
$result = $prepared->execute();
if ($result) {
    $data = $prepared->fetchAll();
    var_dump($data);
}