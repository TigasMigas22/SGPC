<?php
session_start();
include('..\..\php\conexao\conexao.php');

if (isset($_SESSION['id_utilizador'])) {
    $id_utilizador = $_SESSION['id_utilizador'];

    $tempo_fim = date('Y-m-d H:i:s');

    $atualizar_login = "UPDATE login 
                        SET tempo_fim = '$tempo_fim' 
                        WHERE id_utilizador = '$id_utilizador' 
                        ORDER BY id_login DESC 
                        LIMIT 1";
  

    if (!mysqli_query($ligacao, $atualizar_login)) {
        echo "<p>Erro ao atualizar a hora de logout: " . mysqli_error($ligacao) . "</p>";
    }
    session_unset();
    session_destroy();
}
header('Location: ..\..\html\formularios\login.html');
exit();
?>
