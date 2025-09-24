<?php
session_start();
include('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'cliente') {
    echo "Acesso restrito.";
    exit;
}

$id_utilizador = $_SESSION['id_utilizador'];

// Eliminar o cliente e todos os dados relacionados por ON DELETE CASCADE
$sql = "DELETE FROM utilizadores WHERE id_utilizador = $id_utilizador";
if (mysqli_query($ligacao, $sql)) {
    session_destroy();
    header("Location: ../../html/formularios/login.html");
    exit;
} else {
    echo "<p>Erro ao encerrar conta: " . mysqli_error($ligacao) . "</p>";
}
?>
