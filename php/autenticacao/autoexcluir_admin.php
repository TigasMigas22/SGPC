<?php
session_start();
include('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'administrador') {
    echo "Acesso restrito.";
    exit;
}

$id_utilizador = $_SESSION['id_utilizador'];

// Eliminar o próprio utilizador (o restante será eliminado por ON DELETE CASCADE)
$sql = "DELETE FROM utilizadores WHERE id_utilizador = $id_utilizador";
if (mysqli_query($ligacao, $sql)) {
    session_destroy();
    header("Location: ../../html/formularios/registar_admin.html");
    exit;
} else {
    echo "<p>Erro ao eliminar conta: " . mysqli_error($ligacao) . "</p>";
}
?>
