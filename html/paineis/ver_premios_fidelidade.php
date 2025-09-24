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
        <div class='alert alert-danger'>Acesso negado.</div>
        <a href='../autenticacao/login.html' class='btn btn-secondary mt-3'>Voltar ao login</a>
    </body>
    </html>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Prémios de Fidelidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Prémios Disponíveis no Programa de Fidelidade</h2>

    <?php
    $resultado = mysqli_query($ligacao, "SELECT * FROM cartao_fidelidade ORDER BY pontos_necessarios DESC");

    if (mysqli_num_rows($resultado) > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Descrição</th>
                    <th>Pontos Necessários</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['descricao']) ?></td>
                        <td><?= $row['pontos_necessarios'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">Sem prémios definidos.</div>
    <?php endif;

    mysqli_close($ligacao);
    ?>

    <a href="painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
