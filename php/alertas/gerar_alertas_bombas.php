<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'gerente_posto') {
    echo "<p class='text-danger text-center mt-5'>Acesso restrito.</p>";
    exit;
}

// GERA ALERTAS AUTOMATICAMENTE PARA BOMBAS < 20%
$query = "SELECT id_registo, nivel_combustivel FROM bomba WHERE nivel_combustivel < 20";
$resultado = mysqli_query($ligacao, $query);

while ($row = mysqli_fetch_assoc($resultado)) {
    $id_registo = $row['id_registo'];
    $nivel = $row['nivel_combustivel'];

    $verifica = mysqli_query($ligacao, "
        SELECT 1 FROM alerta 
        WHERE id_registo = $id_registo AND estado = 'pendente' AND tipo = 'Combustível Baixo'
    ");

    if (mysqli_num_rows($verifica) == 0) {
        $descricao = "Nível crítico: $nivel% na bomba (registo $id_registo)";
        mysqli_query($ligacao, "
            INSERT INTO alerta (id_registo, descricao, tipo, data, estado)
            VALUES ($id_registo, '$descricao', 'Combustível Baixo', NOW(), 'pendente')
        ");
    }
}

// RESOLVER ALERTA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'resolver') {
    $id_alerta = intval($_POST['id_alerta']);
    $novo_nivel = floatval($_POST['novo_nivel']);

    // Buscar capacidade máxima da bomba
    $res_bomba = mysqli_query($ligacao, "
        SELECT capacidade_maxima FROM bomba 
        WHERE id_registo = (SELECT id_registo FROM alerta WHERE id_alerta = $id_alerta)
    ");
    $dados = mysqli_fetch_assoc($res_bomba);
    $capacidade = $dados['capacidade_maxima'];

    // Calcular novo stock em litros
    $novo_litros = ($capacidade * $novo_nivel) / 100;

    // Atualizar bomba e alerta
    mysqli_query($ligacao, "
        UPDATE bomba 
        SET nivel_combustivel = $novo_nivel, stock_litros = $novo_litros 
        WHERE id_registo = (SELECT id_registo FROM alerta WHERE id_alerta = $id_alerta)
    ");

    mysqli_query($ligacao, "
        UPDATE alerta SET estado = 'resolvido' WHERE id_alerta = $id_alerta
    ");

    echo "<div class='alert alert-success text-center mt-3'>Alerta resolvido e valores atualizados com sucesso.</div>";
}

// MOSTRAR ALERTAS
$alertas = mysqli_query($ligacao, "
    SELECT a.id_alerta, a.id_registo, a.descricao, a.tipo, a.data, a.estado
    FROM alerta a
    ORDER BY a.data DESC
");
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Alertas de Bombas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Alertas de Bombas - Painel do Gerente</h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Bomba (registo)</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Data</th>
                <th>Estado</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
        <?php if (mysqli_num_rows($alertas) > 0): ?>
            <?php while ($a = mysqli_fetch_assoc($alertas)): ?>
                <tr>
                    <td><?= $a['id_alerta'] ?></td>
                    <td><?= $a['id_registo'] ?></td>
                    <td><?= $a['tipo'] ?></td>
                    <td><?= $a['descricao'] ?></td>
                    <td><?= $a['data'] ?></td>
                    <td><?= $a['estado'] ?></td>
                    <td>
                        <?php if ($a['estado'] === 'pendente'): ?>
                            <form method="POST" class="d-flex gap-2">
                                <input type="hidden" name="id_alerta" value="<?= $a['id_alerta'] ?>">
                                <input type="hidden" name="acao" value="resolver">
                                <input type="number" step="0.01" name="novo_nivel" class="form-control form-control-sm" placeholder="Novo nível (%)" required>
                                <button type="submit" class="btn btn-sm btn-primary">Confirmar</button>
                            </form>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">Sem alertas registados.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <a href="../../html/paineis/painel_utilizador.php" class="btn btn-secondary">Voltar ao Painel</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
