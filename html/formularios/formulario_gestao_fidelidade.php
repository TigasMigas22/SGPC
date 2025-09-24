<?php
session_start();
include('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'funcionario_administrativo') {
    echo "<p class='alert alert-danger m-3'>Acesso restrito.</p>";
    exit;
}

$mensagem = '';

// Adicionar novo prémio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'adicionar') {
    $descricao = trim($_POST['descricao']);
    $pontos = floatval($_POST['pontos_necessarios']);

    $inserir = "INSERT INTO cartao_fidelidade (descricao, pontos_necessarios) VALUES (?, ?)";
    $stmt = $ligacao->prepare($inserir);
    $stmt->bind_param("sd", $descricao, $pontos);

    if ($stmt->execute()) {
        $mensagem = "<div class='alert alert-success m-3'>Prémio adicionado com sucesso.</div>";
    } else {
        $mensagem = "<div class='alert alert-danger m-3'>Erro ao adicionar prémio: {$stmt->error}</div>";
    }
}

// Eliminar prémio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'eliminar') {
    $id = intval($_POST['id_premio']);
    mysqli_query($ligacao, "DELETE FROM cartao_fidelidade WHERE id_premio = $id");
    $mensagem = "<div class='alert alert-warning m-3'>Prémio eliminado.</div>";
}

// Atualizar prémio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'atualizar') {
    $id = intval($_POST['id_premio']);
    $descricao = trim($_POST['descricao']);
    $pontos = floatval($_POST['pontos_necessarios']);

    $verificar = mysqli_query($ligacao, "SELECT descricao, pontos_necessarios FROM cartao_fidelidade WHERE id_premio = $id");
    $atual = mysqli_fetch_assoc($verificar);

    if ($atual && ($atual['descricao'] !== $descricao || abs(floatval($atual['pontos_necessarios']) - $pontos) > 0.0001)) {
        $atualizar = "UPDATE cartao_fidelidade SET descricao = ?, pontos_necessarios = ? WHERE id_premio = ?";
        $stmt = $ligacao->prepare($atualizar);
        $stmt->bind_param("sdi", $descricao, $pontos, $id);
        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success m-3'>Prémio atualizado com sucesso.</div>";
        } else {
            $mensagem = "<div class='alert alert-danger m-3'>Erro ao atualizar prémio: {$stmt->error}</div>";
        }
    } else {
        $mensagem = "<div class='alert alert-info m-3'>Nenhuma alteração detetada.</div>";
    }
}

$resultado = mysqli_query($ligacao, "SELECT * FROM cartao_fidelidade ORDER BY pontos_necessarios ASC");
?>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-4">
    <h1 class="mb-4">Gestão de Prémios de Fidelidade</h1>

    <?= $mensagem ?>

    <div class="card mb-4">
        <div class="card-header">Adicionar Novo Prémio</div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="acao" value="adicionar">
                <div class="mb-3">
                    <label class="form-label">Descrição:</label>
                    <input type="text" name="descricao" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pontos Necessários:</label>
                    <input type="number" name="pontos_necessarios" class="form-control" step="0.01" min="0" required>
                </div>
                <button type="submit" class="btn btn-primary">Adicionar Prémio</button>
            </form>
        </div>  
    </div>

    <h3>Prémios Existentes</h3>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr><th>Descrição</th><th>Pontos Necessários</th><th>Ação</th></tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($resultado)) { ?>
            <tr>
                <form method="POST" class="d-flex gap-2 align-items-center">
                    <td><input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($row['descricao']) ?>" required></td>
                    <td><input type="number" name="pontos_necessarios" class="form-control" step="0.01" value="<?= $row['pontos_necessarios'] ?>" required></td>
                    <td>
                        <input type="hidden" name="id_premio" value="<?= $row['id_premio'] ?>">
                        <button type="submit" name="acao" value="atualizar" class="btn btn-primary btn-sm">Atualizar</button>
                        <button type="submit" name="acao" value="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('Eliminar este prémio?')">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <form action='../paineis/painel_utilizador.php'>
        <button type='submit' class='btn btn-secondary'>Voltar ao painel</button>
    </form>
</div>

<!-- Bootstrap JS-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
