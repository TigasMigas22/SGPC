<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'operador') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

$res_clientes = mysqli_query($ligacao, "SELECT id_utilizador, nome FROM utilizadores WHERE tipo = 'cliente'");
$res_produtos = mysqli_query($ligacao, "SELECT id_produto, nome_produto, stock_atual, preco_unitario FROM produto WHERE tipo = 'loja'");
$res_combustiveis = mysqli_query($ligacao, "
    SELECT b.id_registo, p.nome_produto, b.stock_litros, b.preco_unitario 
    FROM bomba b 
    JOIN produto p ON b.id_produto = p.id_produto
");
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Registar Venda</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Registar Venda</h2>

    <form method="POST" action="registar_vendas.php" class="border p-4 rounded shadow-sm bg-light">
        <div class="mb-3">
            <label for="id_cliente" class="form-label">Cliente:</label>
            <select name="id_cliente" id="id_cliente" class="form-select" required>
                <option value="">Selecione um cliente</option>
                <?php while ($c = mysqli_fetch_assoc($res_clientes)) : ?>
                    <option value="<?= $c['id_utilizador'] ?>">
                        <?= htmlspecialchars($c['nome'], ENT_QUOTES) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <h4 class="mt-4">Produtos de Loja</h4>
        <?php while ($p = mysqli_fetch_assoc($res_produtos)) : ?>
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="id_produto[]" 
                       value="<?= $p['id_produto'] ?>" 
                       id="prod_<?= $p['id_produto'] ?>">
                <label class="form-check-label" for="prod_<?= $p['id_produto'] ?>">
                    <?= "{$p['nome_produto']} ({$p['stock_atual']} un) - " 
                        . number_format($p['preco_unitario'], 2, ',', '.') 
                        . "€" ?>
                </label>
                <input type="number" 
                       name="quantidade_<?= $p['id_produto'] ?>" 
                       class="form-control mt-1" 
                       placeholder="Qtd" 
                       min="0">
            </div>
        <?php endwhile; ?>

        <h4 class="mt-4">Combustíveis</h4>
        <?php while ($c = mysqli_fetch_assoc($res_combustiveis)) : ?>
            <div class="form-check mb-2">
                <input class="form-check-input" 
                       type="checkbox" 
                       name="id_combustivel[]" 
                       value="<?= $c['id_registo'] ?>" 
                       id="comb_<?= $c['id_registo'] ?>">
                <label class="form-check-label" for="comb_<?= $c['id_registo'] ?>">
                    <?= "{$c['nome_produto']} - {$c['stock_litros']}L - " 
                        . number_format($c['preco_unitario'], 2, ',', '.') 
                        . "€/L" ?>
                </label>
                <input type="number" 
                       name="litros_<?= $c['id_registo'] ?>" 
                       class="form-control mt-1" 
                       placeholder="Litros" 
                       min="0">
            </div>
        <?php endwhile; ?>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Finalizar Venda</button>
            <a href="../../html/paineis/painel_utilizador.php" class="btn btn-secondary ms-2">
                Voltar ao painel
            </a>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
