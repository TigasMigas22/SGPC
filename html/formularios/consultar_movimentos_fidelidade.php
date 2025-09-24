<?php
include('../../php/conexao/conexao.php');

$mensagem_erro = '';
$movimentos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id_cliente'])) {
        $mensagem_erro = "Por favor, selecione um cliente.";
    } else {
        $id_cliente = intval($_POST['id_cliente']);

        $query = "SELECT data_movimento, tipo, pontos, descricao FROM movimento_cartao WHERE id_utilizador = ? ORDER BY data_movimento DESC";
        $stmt = $ligacao->prepare($query);
        $stmt->bind_param("i", $id_cliente);
        $stmt->execute();
        $movimentos = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Consultar Movimentos Fidelidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Consultar Histórico de Pontos</h2>

    <?php if ($mensagem_erro): ?>
        <div class="text-danger mb-3"><?= htmlspecialchars($mensagem_erro) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <label for="id_cliente" class="form-label">Selecionar Cliente:</label>
        <select name="id_cliente" id="id_cliente" class="form-select" required>
            <option value="">-- Selecione um cliente --</option>
            <?php
            $clientes = mysqli_query($ligacao, "SELECT id_utilizador, nome FROM utilizadores WHERE tipo = 'cliente'");
            while ($cliente = mysqli_fetch_assoc($clientes)) {
                $selected = isset($_POST['id_cliente']) && $_POST['id_cliente'] == $cliente['id_utilizador'] ? 'selected' : '';
                echo "<option value='{$cliente['id_utilizador']}' $selected>{$cliente['nome']}</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary mt-2">Consultar</button>
        
    </form>
    <a href="../paineis/painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel</a>

    <?php if (!empty($movimentos) && $movimentos->num_rows > 0): ?>
        <table class="table table-dark">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Pontos</th>
                    <th>Descrição</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($movimentos)): ?>
                    <tr>
                        <td><?= $row['data_movimento'] ?></td>
                        <td><?= $row['tipo'] ?></td>
                        <td><?= $row['pontos'] ?></td>
                        <td><?= $row['descricao'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$mensagem_erro): ?>
        <div class="alert alert-warning">Nenhum movimento encontrado para o cliente selecionado.</div>
    <?php endif; ?>
</body>
</html>
