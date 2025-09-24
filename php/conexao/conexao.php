<?php
$servidor = 'localhost';
$user = 'root';
$password = 'BaseDeDadosDoT!@g0'; 
$db_name = 'sgpc_projeto';

$ligacao = mysqli_connect($servidor, $user, $password, $db_name) or die ("Erro na conexão à base de dados" . mysqli_connect_error());
?>