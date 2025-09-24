<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'operador') {
    echo "Acesso restrito.";
    exit;
}

$mensagem = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $acao = $_POST['acao'];
    $id = isset($_POST['id_produto']) ? intval($_POST['id_produto']) : 0;

    if ($acao === 'inserir') {
        $nome = trim($_POST['nome_produto']);
        $preco = floatval($_POST['preco_unitario']);
        $stock = floatval($_POST['stock_atual']);
        $tipo = $_POST['tipo'];

        if ($tipo === 'combustivel' && $stock > 100000) {
            $mensagem = "<div class='alert alert-warning'>O stock inicial não pode exceder 100000L.</div>";
        } else {
            $query_produto = "INSERT INTO produto (nome_produto, preco_unitario, stock_atual, tipo) VALUES ('$nome', $preco, $stock, '$tipo')";
            $res_produto = mysqli_query($ligacao, $query_produto);

            if ($res_produto) {
                $id_produto = mysqli_insert_id($ligacao);
                $mensagem = "<div class='alert alert-success'>Produto inserido com sucesso.</div>";

                if ($tipo === 'combustivel') {
                    $capacidade_maxima = 100000;
                    $nivel = ($stock / $capacidade_maxima) * 100;

                    $query_bomba = "INSERT INTO bomba (id_produto, preco_unitario, stock_litros, capacidade_maxima, nivel_combustivel)
                                    VALUES ($id_produto, $preco, $stock, $capacidade_maxima, $nivel)";
                    if (!mysqli_query($ligacao, $query_bomba)) {
                        $mensagem .= "<div class='alert alert-danger'>Erro ao registar combustível na bomba: " . mysqli_error($ligacao) . "</div>";
                    }
                }
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao inserir produto: " . mysqli_error($ligacao) . "</div>";
            }
        }

    } elseif ($acao === 'atualizar' && $id > 0) {
        $nome_novo = trim($_POST['nome_produto']);
        $preco_novo = floatval($_POST['preco_unitario']);
        $stock_adicional = floatval($_POST['stock_atual']);

        $res_atual = mysqli_query($ligacao, "SELECT nome_produto, preco_unitario, tipo FROM produto WHERE id_produto = $id");
        $dados_atuais = mysqli_fetch_assoc($res_atual);
        $tipo_produto = $dados_atuais['tipo'];

        $updates = [];
        if ($nome_novo !== $dados_atuais['nome_produto']) {
            $updates[] = "nome_produto = '$nome_novo'";
        }
        if ($preco_novo != $dados_atuais['preco_unitario']) {
            $updates[] = "preco_unitario = $preco_novo";
        }
        if ($stock_adicional > 0) {
            $updates[] = "stock_atual = stock_atual + $stock_adicional";
        }

        if (!empty($updates)) {
            if ($tipo_produto === 'combustivel' && $stock_adicional > 0) {
                $bomba = mysqli_fetch_assoc(mysqli_query($ligacao, "SELECT stock_litros, capacidade_maxima FROM bomba WHERE id_produto = $id"));
                $novo_stock = $bomba['stock_litros'] + $stock_adicional;

                if ($novo_stock > $bomba['capacidade_maxima']) {
                    $mensagem = "<div class='alert alert-warning'>Stock excede a capacidade máxima da bomba.</div>";
                } else {
                    $query_update = "UPDATE produto SET " . implode(', ', $updates) . " WHERE id_produto = $id";
                    if (mysqli_query($ligacao, $query_update)) {
                        $novo_nivel = ($novo_stock / $bomba['capacidade_maxima']) * 100;
                        mysqli_query($ligacao, "UPDATE bomba SET stock_litros = $novo_stock, nivel_combustivel = $novo_nivel WHERE id_produto = $id");
                        $mensagem = "<div class='alert alert-success'>Produto e bomba atualizados com sucesso.</div>";
                    } else {
                        $mensagem = "<div class='alert alert-danger'>Erro ao atualizar produto: " . mysqli_error($ligacao) . "</div>";
                    }
                }
            } else {
                $query_update = "UPDATE produto SET " . implode(', ', $updates) . " WHERE id_produto = $id";
                if (mysqli_query($ligacao, $query_update)) {
                    $mensagem = "<div class='alert alert-success'>Produto atualizado com sucesso.</div>";
                } else {
                    $mensagem = "<div class='alert alert-danger'>Erro ao atualizar produto: " . mysqli_error($ligacao) . "</div>";
                }
            }
        } else {
            $mensagem = "<div class='alert alert-info m-3'>Nenhuma alteração detetada.</div>";
        }

    } elseif ($acao === 'eliminar' && $id > 0) {
        $verifica_bomba = mysqli_query($ligacao, "SELECT 1 FROM bomba WHERE id_produto = $id");
        if (mysqli_num_rows($verifica_bomba) > 0) {
            $mensagem = "<div class='alert alert-warning'>Combustível não pode ser eliminado da bomba.</div>";
        } else {
            $query_delete = "DELETE FROM produto WHERE id_produto = $id";
            if (mysqli_query($ligacao, $query_delete)) {
                $mensagem = "<div class='alert alert-success'>Produto eliminado com sucesso.</div>";
            } else {
                $mensagem = "<div class='alert alert-danger'>Erro ao eliminar produto: " . mysqli_error($ligacao) . "</div>";
            }
        }
    }
}

$resultado = mysqli_query($ligacao, "SELECT * FROM produto");
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Produtos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Gestão de Produtos / Combustíveis</h2>

    <?= $mensagem ?>

    <form method="POST" class="border p-4 rounded bg-light shadow-sm mb-5">
        <input type="hidden" name="acao" value="inserir">
        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input type="text" class="form-control" name="nome_produto" required>
        </div>
        <div class="mb-3">
            <label for="preco" class="form-label">Preço Unitário (€):</label>
            <input type="number" step="0.01" min ="0" class="form-control" name="preco_unitario" required>
        </div>
        <div class="mb-3">
            <label for="stock" class="form-label">Stock Inicial:</label>
            <input type="number" step="0.01" min ="0" class="form-control" name="stock_atual" required>
        </div>
        <div class="mb-3">
            <label for="tipo" class="form-label">Tipo:</label>
            <select name="tipo" class="form-select" required>
                <option value="">Selecione o tipo</option>
                <option value="loja">Loja</option>
                <option value="combustivel">Combustível</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Inserir</button>
        <a href="../../html/paineis/painel_utilizador.php" class="btn btn-secondary ms-2">Voltar ao painel</a>
    </form>

    <h4>Lista de Produtos Existentes</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço (€)</th>
                <th>Adicionar Stock</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($p = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <form method="POST">
                    <td><?= $p['id_produto'] ?></td>
                    <td><input type="text" name="nome_produto" class="form-control" value="<?= htmlspecialchars($p['nome_produto']) ?>" required></td>
                    <td><input type="number" step="0.01" min="0" name="preco_unitario" class="form-control" value="<?= $p['preco_unitario'] ?>" required></td>
                    <td><input type="number" step="0.01" min="0" name="stock_atual" class="form-control" placeholder="Adicionar"></td>
                    <td><?= $p['tipo'] ?></td>
                    <td>
                        <input type="hidden" name="id_produto" value="<?= $p['id_produto'] ?>">
                        <button type="submit" name="acao" value="atualizar" class="btn btn-primary btn-sm">Atualizar</button>
                        <button type="submit" name="acao" value="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('Tem a certeza que deseja eliminar este produto?');">Eliminar</button>
                    </td>
                </form>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
