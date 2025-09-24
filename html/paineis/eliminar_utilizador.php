<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'administrador') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-warning'>ID de utilizador não especificado ou inválido.</div>";
    exit;
}

$id = intval($_GET['id']);

// Evitar autoexclusão por este método
if ($id == $_SESSION['id_utilizador']) {
    echo "<div class='alert alert-info'>Não pode eliminar a sua própria conta por aqui. Use a opção 'Autoexcluir'.</div>";
    exit;
}

$apagar = "DELETE FROM utilizadores WHERE id_utilizador = $id";

?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Eliminar Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<?php
if (mysqli_query($ligacao, $apagar)) {
    echo "<div class='alert alert-success'>Utilizador eliminado com sucesso.</div>";
} else {
    echo "<div class='alert alert-danger'>Erro ao eliminar utilizador: " . mysqli_error($ligacao) . "</div>";
}
?>
</body>
</html>
