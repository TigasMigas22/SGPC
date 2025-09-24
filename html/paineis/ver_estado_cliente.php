<?php
session_start();
include('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'cliente') {
    echo "<!DOCTYPE html>
    <html lang='pt-PT'>
    <head>
        <meta charset='UTF-8'>
        <title>Acesso Negado</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-danger text-center'>Acesso não autorizado.</div>
        <a href='../autenticacao/login.html' class='btn btn-secondary mt-3'>Voltar ao login</a>
    </body>
    </html>";
    exit;
}

$id_utilizador = $_SESSION['id_utilizador'];

// Cancelar agendamento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'cancelar') {
    $id_agendamento = $_POST['id_agendamento'];
    $sql = "DELETE FROM agendamento WHERE id_agendamento = ?";
    $stmt = mysqli_prepare($ligacao, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id_agendamento);
        mysqli_stmt_execute($stmt);
    }
    header("Location: ver_estado_cliente.php");
    exit;
}

// Guardar edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'guardar') {
    $id_agendamento = $_POST['id_agendamento'];
    $id_servico = $_POST['id_servico'];
    $data_hora = $_POST['data_hora'];
    $estado = 'não iniciado';

    $sql = "UPDATE agendamento SET id_servico = ?, data_hora = ?, estado = ?, id_funcionario = NULL WHERE id_agendamento = ?";
    $stmt = mysqli_prepare($ligacao, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $id_servico, $data_hora, $estado, $id_agendamento);
        mysqli_stmt_execute($stmt);
    }
    header("Location: ver_estado_cliente.php");
    exit;
}

// Mostrar formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['acao'] === 'editar') {
    $id_agendamento = $_POST['id_agendamento'];
    $sql = "SELECT id_servico, data_hora FROM agendamento WHERE id_agendamento = ?";
    $stmt = mysqli_prepare($ligacao, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_agendamento);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $dados = mysqli_fetch_assoc($resultado);

    $servicos = mysqli_query($ligacao, "SELECT id_servico, descricao FROM servico");
    ?>
    <!DOCTYPE html>
    <html lang="pt-PT">
    <head>
        <meta charset="UTF-8">
        <title>Editar Agendamento</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="container mt-5">
        <h2 class="mb-4">Editar Agendamento</h2>
        <form method="POST">
            <input type="hidden" name="id_agendamento" value="<?= $id_agendamento ?>">
            <input type="hidden" name="acao" value="guardar">

            <div class="mb-3">
                <label for="id_servico" class="form-label">Serviço:</label>
                <select name="id_servico" class="form-select" required>
                    <?php while ($row = mysqli_fetch_assoc($servicos)): ?>
                        <option value="<?= $row['id_servico'] ?>" <?= $row['id_servico'] == $dados['id_servico'] ? 'selected' : '' ?> >
                            <?= htmlspecialchars($row['descricao']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="data_hora" class="form-label">Nova Data e Hora:</label>
                <input type="datetime-local" name="data_hora" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($dados['data_hora'])) ?>" required>
            </div>

            <button type="submit" class="btn btn-success">Guardar Alterações</button>
            <a href="ver_estado_cliente.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </body>
    </html>
    <?php exit; } ?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Meus Agendamentos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2 class="mb-4">Meus Agendamentos</h2>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Serviço</th>
                <th>Data e Hora</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT a.id_agendamento, s.descricao, a.data_hora, a.estado 
                FROM agendamento a 
                JOIN servico s ON a.id_servico = s.id_servico 
                WHERE a.id_cliente = $id_utilizador";
        $resultado = mysqli_query($ligacao, $sql);
        while ($row = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($row['descricao']) ?></td>
                <td><?= htmlspecialchars($row['data_hora']) ?></td>
                <td><?= htmlspecialchars($row['estado']) ?></td>
                <td>
                    <?php if ($row['estado'] != 'iniciado'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="id_agendamento" value="<?= $row['id_agendamento'] ?>">
                            <button type="submit" name="acao" value="editar" class="btn btn-sm btn-success">Atualizar</button>
                            <button type="submit" name="acao" value="cancelar" class="btn btn-sm btn-danger">Cancelar</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">Sem ações</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="painel_utilizador.php" class="btn btn-secondary">Voltar ao painel</a>
</body>
</html>
