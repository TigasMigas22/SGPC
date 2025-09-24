<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'cliente') {
    echo "<!DOCTYPE html>
    <html lang='pt-PT'>
    <head>
        <meta charset='UTF-8'>
        <title>Acesso Negado</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-danger text-center'>Acesso negado.</div>
        <a href='../autenticacao/login.html' class='btn btn-secondary mt-3'>Voltar ao login</a>
    </body>
    </html>";
    exit;
}

$id_utilizador = $_SESSION['id_utilizador'];

// Calcular pontos disponíveis
$query_pontos = "SELECT SUM(CASE WHEN tipo = 'entrada' THEN pontos ELSE -pontos END) AS saldo 
                 FROM movimento_cartao WHERE id_utilizador = $id_utilizador";
$resultado = mysqli_query($ligacao, $query_pontos);
$pontos = mysqli_fetch_assoc($resultado)['saldo'] ?? 0;

// Obter prémios
$premios = mysqli_query($ligacao, "SELECT * FROM cartao_fidelidade ORDER BY pontos_necessarios ASC");

// Resgatar prémio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['premio_id'])) {
    $id_premio = intval($_POST['premio_id']);
    $premio_selecionado = mysqli_query($ligacao, "SELECT * FROM cartao_fidelidade WHERE id_premio = $id_premio");

    if ($premio = mysqli_fetch_assoc($premio_selecionado)) {
        if ($pontos >= $premio['pontos_necessarios']) {
            mysqli_query($ligacao, "INSERT INTO movimento_cartao (id_utilizador, tipo, pontos, descricao, data_movimento)
                                     VALUES ($id_utilizador, 'saida', {$premio['pontos_necessarios']}, 'Resgate de prémio: {$premio['descricao']}', NOW())");
            $_SESSION['mensagem'] = "Prémio '{$premio['descricao']}' resgatado com sucesso!";
            header("Location: resgatar_premios.php");
            exit();
        } else {
            $_SESSION['mensagem'] = "Pontos insuficientes para resgatar este prémio.";
            header("Location: resgatar_premios.php");
            exit();
        }
    } else {
        $_SESSION['mensagem'] = "Prémio não encontrado.";
        header("Location: resgatar_premios.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Resgatar Prémios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Resgatar Prémios de Fidelização</h2>

    <?php
    if (isset($_SESSION['mensagem'])) {
        echo "<div class='alert alert-info'>" . $_SESSION['mensagem'] . "</div>";
        unset($_SESSION['mensagem']);
    }
    ?>

    <p class="lead">Pontos disponíveis: <strong><?= $pontos ?></strong></p>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="premio_id" class="form-label">Escolha o prémio a resgatar:</label>
            <select name="premio_id" id="premio_id" class="form-select" required>
                <option value="">-- Selecione --</option>
                <?php mysqli_data_seek($premios, 0); while ($row = mysqli_fetch_assoc($premios)): ?>
                    <option value="<?= $row['id_premio'] ?>">
                        <?= htmlspecialchars($row['descricao']) ?> - <?= $row['pontos_necessarios'] ?> pontos
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Resgatar Prémio</button>
    </form>

    <a href="painel_utilizador.php" class="btn btn-secondary">Voltar ao Painel</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
