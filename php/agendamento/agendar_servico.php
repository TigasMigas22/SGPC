<?php
session_start();
include('../../php/conexao/conexao.php');

// Verifica login
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'cliente') {
    echo "<!DOCTYPE html>
    <html lang='pt-PT'>
    <head>
        <meta charset='UTF-8'>
        <title>Erro</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-danger'>Operação não permitida.</div>
        <a href='../../html/formularios/login.html' class='btn btn-secondary mt-3'>Voltar ao login</a>
    </body>
    </html>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = $_SESSION['id_utilizador'];
    $id_servico = $_POST['id_servico'];
    $data_agendamento = $_POST['data_agendamento'];
    $hora_agendamento = $_POST['hora_agendamento'];
    $observacoes = $_POST['observacoes'] ?? '';

    $data_hora_agendamento = $data_agendamento . ' ' . $hora_agendamento;

    $agora = new DateTime();
    $escolhida = new DateTime($data_hora_agendamento);

    if ($agora->add(new DateInterval('P1D')) > $escolhida) {
        header("Location: ../../html/formularios/agendar_servico_formulario.php?erro=1");
        exit();
    } else {
        $sql = "INSERT INTO agendamento (id_cliente, id_servico, data_hora, estado, observacoes)
                VALUES ('$id_cliente', '$id_servico', '$data_hora_agendamento', 'Não iniciado', '$observacoes')";
        if (mysqli_query($ligacao, $sql)) {
            header("Location: ../../html/formularios/agendar_servico_formulario.php?sucesso=1");
            exit();
        } else {
            header("Location: ../../html/formularios/agendar_servico_formulario.php?erro=2");
            exit();
        }
    }
}
?>
