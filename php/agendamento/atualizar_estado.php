<?php
session_start();
include('../conexao/conexao.php');

if ($_SESSION['tipo'] != 'funcionario_servicos') {
    echo "Acesso não autorizado.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agendamento = $_POST['id_agendamento'];
    $novo_estado = $_POST['novo_estado'];

    // Validar se o estado é permitido
    $estados_validos = ['Iniciado', 'Terminado'];
    if (!in_array($novo_estado, $estados_validos)) {
        echo "<p>Estado inválido.</p>";
        exit;
    }

    $query = "UPDATE agendamento SET estado = '$novo_estado' WHERE id_agendamento = $id_agendamento";
    
    if (mysqli_query($ligacao, $query)) {
        echo "<p>Estado atualizado para $novo_estado com sucesso.</p>";
        echo "<a href='../../html/paineis/ver_agendamentos.php'>Voltar aos meus agendamentos</a>";
    } else {
        echo "<p>Erro ao atualizar estado: " . mysqli_error($ligacao) . "</p>";
    }
} else {
    echo "<p>Requisição inválida.</p>";
}

mysqli_close($ligacao);
?>
