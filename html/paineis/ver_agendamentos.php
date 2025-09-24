<?php
session_start();
include('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'funcionario_servicos') {
    echo "<div class='alert alert-danger text-center mt-5'>Acesso não autorizado.</div>";
    exit;
}

$id_funcionario = $_SESSION['id_utilizador'];

// Processar término de serviço
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminar'])) {
    $id_agendamento = $_POST['id_agendamento'];

    mysqli_query($ligacao, "UPDATE agendamento SET estado = 'Terminado' WHERE id_agendamento = $id_agendamento AND id_funcionario = $id_funcionario");

    $info = mysqli_fetch_assoc(mysqli_query($ligacao, "SELECT id_cliente, id_servico FROM agendamento WHERE id_agendamento = $id_agendamento"));
    $id_cliente = $info['id_cliente'];
    $id_servico = $info['id_servico'];

    $preco = mysqli_fetch_assoc(mysqli_query($ligacao, "SELECT preco FROM servico WHERE id_servico = $id_servico"))['preco'];

    mysqli_query($ligacao, "INSERT INTO venda (id_cliente, id_operador) VALUES ($id_cliente, NULL)");
    $id_venda = mysqli_insert_id($ligacao);

    mysqli_query($ligacao, "INSERT INTO venda_item (id_venda, tipo_item, id_ref, quantidade, preco_unitario)
                            VALUES ($id_venda, 'servico', $id_servico, 1, $preco)");

    mysqli_query($ligacao, "INSERT INTO movimento_cartao (id_utilizador, tipo, pontos, motivo, data)
                            VALUES ($id_cliente, 'ganho', $preco, 'Serviço concluído', NOW())");

    $cf = mysqli_fetch_assoc(mysqli_query($ligacao, "
        SELECT cf.limite_gasto, cf.premio,
               (SELECT SUM(pontos) FROM movimento_cartao WHERE id_utilizador = $id_cliente) AS saldo
        FROM cartao_fidelidade cf WHERE cf.id_utilizador = $id_cliente
    "));

    if ($cf && $cf['saldo'] >= $cf['limite_gasto']) {
        mysqli_query($ligacao, "
            INSERT INTO movimento_cartao (id_utilizador, tipo, pontos, motivo, data)
            VALUES ($id_cliente, 'resgate', -{$cf['limite_gasto']}, 'Resgate: {$cf['premio']}', NOW())
        ");
    }

    echo "<div class='alert alert-success text-center'>Serviço terminado e venda registada com sucesso!</div>";
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Meus Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Meus Agendamentos</h2>
    <?php
    $sql = "SELECT a.id_agendamento, s.descricao, a.data_hora, a.estado
            FROM agendamento a
            JOIN servico s ON a.id_servico = s.id_servico
            WHERE a.id_funcionario = $id_funcionario
            ORDER BY a.data_hora DESC";
    $res = mysqli_query($ligacao, $sql);

    if (mysqli_num_rows($res) > 0) {
        echo "<table class='table table-bordered mt-3'>
                <thead class='table-dark'>
                <tr><th>Serviço</th><th>Data/Hora</th><th>Estado</th><th>Ação</th></tr>
                </thead><tbody>";
        while ($row = mysqli_fetch_assoc($res)) {
            echo "<tr>
                    <td>{$row['descricao']}</td>
                    <td>{$row['data_hora']}</td>
                    <td>{$row['estado']}</td>
                    <td>";
            if ($row['estado'] === 'Iniciado') {
                echo "<form method='POST'>
                        <input type='hidden' name='id_agendamento' value='{$row['id_agendamento']}'>
                        <button type='submit' name='terminar' class='btn btn-primary btn-sm'>Terminar</button>
                      </form>";
            } else {
                echo "-";
            }
            echo "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-info'>Não tem agendamentos atribuídos.</div>";
    }

    mysqli_close($ligacao);
    ?>
    <a href="painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel </a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
