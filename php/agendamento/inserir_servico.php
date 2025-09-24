<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'funcionario_administrativo') {
    echo "Acesso restrito.";
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $id = isset($_POST['id_servico']) ? intval($_POST['id_servico']) : 0;

    if ($acao === 'inserir') {
        $descricao = trim($_POST['descricao']);
        $material = trim($_POST['material_necessario']);
        $preco = floatval($_POST['preco']);

        $sql = "INSERT INTO servico (descricao, material_necessario, preco) VALUES (?, ?, ?)";
        $stmt = $ligacao->prepare($sql);
        $stmt->bind_param("ssd", $descricao, $material, $preco);

        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Serviço adicionado com sucesso!</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao adicionar serviço: {$stmt->error}</div>";
        }
    } elseif ($acao === 'atualizar' && $id > 0) {
        $descricao = trim($_POST['descricao']);
        $material = trim($_POST['material_necessario']);
        $preco = floatval($_POST['preco']);

        $query_existente = "SELECT descricao, material_necessario, preco FROM servico WHERE id_servico = ?";
        $stmt_check = $ligacao->prepare($query_existente);
        $stmt_check->bind_param("i", $id);
        $stmt_check->execute();
        $resultado_check = $stmt_check->get_result()->fetch_assoc();

        if ($resultado_check && ($descricao != $resultado_check['descricao'] || $material != $resultado_check['material_necessario'] || $preco != $resultado_check['preco'])) {
            $sql = "UPDATE servico SET descricao = ?, material_necessario = ?, preco = ? WHERE id_servico = ?";
            $stmt = $ligacao->prepare($sql);
            $stmt->bind_param("ssdi", $descricao, $material, $preco, $id);

            if ($stmt->execute()) {
                $mensagem = "<div class='alert alert-success'>Serviço atualizado com sucesso!</div>";
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao atualizar serviço: {$stmt->error}</div>";
            }
        } else {
            $mensagem = "<div class='alert alert-info m-3'>Nenhuma alteração detetada.</div>";
        }
    } elseif ($acao === 'eliminar' && $id > 0) {
        $sql = "DELETE FROM servico WHERE id_servico = ?";
        $stmt = $ligacao->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $mensagem = "<div class='alert alert-success'>Serviço eliminado com sucesso!</div>";
        } else {
            $mensagem = "<div class='alert alert-danger'>Erro ao eliminar serviço: {$stmt->error}</div>";
        }
    }
}

$resultado = mysqli_query($ligacao, "SELECT * FROM servico");
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="utf-8">
    <title>Gerir Serviços - SGPC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4 text-center">Gestão de Serviços</h1>

    <?= $mensagem ?>

    <form method="POST" class="p-4 bg-white shadow rounded mb-5">
        <input type="hidden" name="acao" value="inserir">

        <div class="mb-3">
            <label for="descricao" class="form-label">Descrição do Serviço:</label>
            <input type="text" id="descricao" name="descricao" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="material" class="form-label">Materiais Necessários:</label>
            <textarea id="material" name="material_necessario" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label for="preco" class="form-label">Preço (€):</label>
            <input type="number" id="preco" name="preco" class="form-control" step="0.01" min="0" required>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary">Adicionar Serviço</button>
        </div>
    </form>

    <h4>Serviços Existentes</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Descrição</th>
                <th>Materiais</th>
                <th>Preço (€)</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($s = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <form method="POST">
                    <td><?= $s['id_servico'] ?></td>
                    <td><input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($s['descricao']) ?>" required></td>
                    <td><input type="text" name="material_necessario" class="form-control" value="<?= htmlspecialchars($s['material_necessario']) ?>" required></td>
                    <td><input type="number" step="0.01" min="0" name="preco" class="form-control" value="<?= $s['preco'] ?>" required></td>
                    <td>
                        <input type="hidden" name="id_servico" value="<?= $s['id_servico'] ?>">
                        <button type="submit" name="acao" value="atualizar" class="btn btn-primary btn-sm">Atualizar</button>
                        <button type="submit" name="acao" value="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('Tem a certeza que deseja eliminar este serviço?');">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <a href="../../html/paineis/painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
