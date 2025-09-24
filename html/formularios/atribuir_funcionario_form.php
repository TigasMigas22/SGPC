<?php
session_start();
include('../../php/conexao/conexao.php');

$mensagem = '';

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'funcionario_administrativo') {
    echo "<div class='alert alert-danger text-center mt-5'>Acesso não autorizado.</div>";
    exit;
}

// Processar POST da atribuição
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agendamento = $_POST['id_agendamento'];
    $id_funcionario = $_POST['id_funcionario'];

    $sql = "UPDATE agendamento SET id_funcionario = ? WHERE id_agendamento = ?";
    $stmt = mysqli_prepare($ligacao, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $id_funcionario, $id_agendamento);

    if (mysqli_stmt_execute($stmt)) {
        $mensagem = "<div class='alert alert-success text-center'>Funcionário atribuído com sucesso!</div>";
    } else {
        $mensagem = "<div class='alert alert-danger text-center'>Erro ao atribuir funcionário: " . mysqli_error($ligacao) . "</div>";
    }

    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Atribuir Funcionário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Atribuir Funcionário a Serviço</h2>

    <?= $mensagem ?>

    <form method="POST" class="border p-4 rounded shadow-sm bg-light">
        <div class="mb-3">
            <label for="id_agendamento" class="form-label">Agendamento:</label>
            <select name="id_agendamento" id="id_agendamento" class="form-select" required>
                <option value="">Selecione um agendamento:</option>
                <?php
                $query = "
                    SELECT a.id_agendamento, u.nome AS cliente, s.descricao, a.data_hora
                    FROM agendamento a
                    JOIN utilizadores u ON a.id_cliente = u.id_utilizador
                    JOIN servico s ON a.id_servico = s.id_servico
                    WHERE a.id_funcionario IS NULL
                    ORDER BY a.data_hora ASC
                ";
                $resultado = mysqli_query($ligacao, $query);
                while ($row = mysqli_fetch_assoc($resultado)) {
                    echo "<option value='{$row['id_agendamento']}'>
                            #{$row['id_agendamento']} - {$row['cliente']} - {$row['descricao']} - {$row['data_hora']}
                          </option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_funcionario" class="form-label">Funcionário:</label>
            <select name="id_funcionario" id="id_funcionario" class="form-select" required>
                <option value="">Selecione um funcionário:</option>
                <?php
                $query_func = "SELECT id_utilizador, nome FROM utilizadores WHERE tipo = 'funcionario_servicos'";
                $resultado_func = mysqli_query($ligacao, $query_func);
                while ($func = mysqli_fetch_assoc($resultado_func)) {
                    echo "<option value='{$func['id_utilizador']}'>{$func['nome']}</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Atribuir</button>
    </form>
    <a href="../paineis/painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
