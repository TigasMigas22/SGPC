<?php
session_start();
include ('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador'])) {
    header('Location: ../autenticacao/login.html');
    exit();
}

$id_utilizador = $_SESSION['id_utilizador'];

$stmt = $ligacao->prepare("SELECT nome, email, password FROM utilizadores WHERE id_utilizador = ?");
$stmt->bind_param("i", $id_utilizador);
$stmt->execute();
$dados_utilizador = $stmt->get_result()->fetch_assoc();
$mensagem = "";
$classe_alerta = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_novo = trim($_POST['nome']);
    $palavra_passe_nova = $_POST['nova_password'];
    $nome_atual = $dados_utilizador['nome'];
    $senha_atual_hash = $dados_utilizador['password'];

    if (!empty($nome_novo)) {
        if ($nome_novo === $nome_atual && empty($palavra_passe_nova)) {
            $mensagem = "Nenhuma alteração foi feita.";
            $classe_alerta = "warning";
        } elseif (!empty($palavra_passe_nova) && password_verify($palavra_passe_nova, $senha_atual_hash)) {
            $mensagem = "A nova palavra-passe não pode ser igual à atual.";
            $classe_alerta = "danger";
        } else {
            if (!empty($palavra_passe_nova)) {
                $hash = password_hash($palavra_passe_nova, PASSWORD_DEFAULT);
                $sql = "UPDATE utilizadores SET nome = ?, password = ? WHERE id_utilizador = ?";
                $stmt = $ligacao->prepare($sql);
                $stmt->bind_param("ssi", $nome_novo, $hash, $id_utilizador);
            } else {
                $sql = "UPDATE utilizadores SET nome = ? WHERE id_utilizador = ?";
                $stmt = $ligacao->prepare($sql);
                $stmt->bind_param("si", $nome_novo, $id_utilizador);
            }

            if ($stmt->execute()) {
                $_SESSION['nome'] = $nome_novo;
                $mensagem = "Dados atualizados com sucesso!";
                $classe_alerta = "success";
                $dados_utilizador['nome'] = $nome_novo;
            } else {
                $mensagem = "Erro ao atualizar os dados.";
                $classe_alerta = "danger";
            }
        }
    } else {
        $mensagem = "O nome não pode estar vazio.";
        $classe_alerta = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Dados Pessoais</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="mb-4 text-center">Atualizar Dados Pessoais</h1>
    <form method="POST" class="p-4 bg-white shadow rounded">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($dados_utilizador['nome']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="nova_password" class="form-label">Nova Palavra-passe (deixe em branco para manter):</label>
            <input type="password" name="nova_password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-<?= $classe_alerta ?> mt-3"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>
    </form>
    <a href="painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
