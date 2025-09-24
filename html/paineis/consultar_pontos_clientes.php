<?php
session_start();
include('../../php/conexao/conexao.php');

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'cliente') {
    echo "<!DOCTYPE html>
    <html lang='pt-PT'>
    <head>
        <meta charset='UTF-8'>
        <title>Acesso Não Autorizado</title>
        <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    </head>
    <body class='container mt-5'>
        <div class='alert alert-danger'>Acesso não autorizado.</div>
        <a href='../autenticacao/login.html' class='btn btn-secondary mt-3'>Voltar ao login</a>
    </body>
    </html>";
    exit;
}

$id = intval($_SESSION['id_utilizador']);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Movimentos de Fidelidade</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<h2>Os Meus Movimentos de Fidelidade</h2>

<?php
$query = "
    SELECT tipo, pontos, descricao, data_movimento
    FROM movimento_cartao
    WHERE id_utilizador = $id
    ORDER BY data_movimento ASC
";

$resultado = mysqli_query($ligacao, $query);

if (!$resultado) {
    echo "<div class='alert alert-danger'>Erro na consulta: " . mysqli_error($ligacao) . "</div>";
    exit;
}

if (mysqli_num_rows($resultado) > 0) {
    echo "<table class='table table-striped'>
            <thead class='table-dark'>
                <tr>
                    <th>Tipo</th>
                    <th>Pontos</th>
                    <th>Descrição</th>
                    <th>Data</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<tr>
                <td>" . htmlspecialchars($row['tipo']) . "</td>
                <td>" . intval($row['pontos']) . "</td>
                <td>" . htmlspecialchars($row['descricao']) . "</td>
                <td>" . htmlspecialchars($row['data_movimento']) . "</td>
              </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-info'>Não tem movimentos registados.</div>";
}

mysqli_close($ligacao);
?>

<a href="painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao Painel</a>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
