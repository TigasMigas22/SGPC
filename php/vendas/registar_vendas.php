<?php
session_start();
include('../../php/conexao/conexao.php');

if ($_SESSION['tipo'] != 'operador') {
    echo "<div class='alert alert-danger'>Acesso restrito.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_cliente = intval($_POST['id_cliente']);
    $itens = [];

    // Produtos de loja
    if (isset($_POST['id_produto']) && is_array($_POST['id_produto'])) {
        foreach ($_POST['id_produto'] as $id_produto) {
            $quantidade = intval($_POST["quantidade_$id_produto"] ?? 0);
            if ($quantidade > 0) {
                $itens[] = ['tipo' => 'produto', 'id' => $id_produto, 'quantidade' => $quantidade];
            }
        }
    }

    // Combustíveis
    if (isset($_POST['id_combustivel']) && is_array($_POST['id_combustivel'])) {
        foreach ($_POST['id_combustivel'] as $id_combustivel) {
            $quantidade = floatval($_POST["litros_$id_combustivel"] ?? 0);
            if ($quantidade > 0) {
                $itens[] = ['tipo' => 'combustivel', 'id' => $id_combustivel, 'quantidade' => $quantidade];
            }
        }
    }

    if (empty($itens)) {
        echo "<div class='alert alert-info'>Nenhum item selecionado para venda.</div>";
        exit;
    }

    $total = 0;

    // Cálculo do total e verificação de stock
    foreach ($itens as $item) {
        if ($item['tipo'] === 'produto') {
            $res = mysqli_query($ligacao, "SELECT preco_unitario, stock_atual FROM produto WHERE id_produto = {$item['id']}");
            $dados = mysqli_fetch_assoc($res);

            if ($item['quantidade'] > $dados['stock_atual']) {
                echo "<div class='alert alert-info'>Stock insuficiente para o produto ID {$item['id']}.</div>";
                exit;
            }

            $subtotal = $item['quantidade'] * $dados['preco_unitario'];
            $total += $subtotal;

        } elseif ($item['tipo'] === 'combustivel') {
            $res = mysqli_query($ligacao, "SELECT preco_unitario, stock_litros, capacidade_maxima FROM bomba WHERE id_registo = {$item['id']}");
            $dados = mysqli_fetch_assoc($res);

            if ($item['quantidade'] > $dados['stock_litros']) {
                echo "<div class='alert alert-info'>Stock insuficiente para a bomba ID {$item['id']}.</div>";
                exit;
            }

            $subtotal = $item['quantidade'] * $dados['preco_unitario'];
            $total += $subtotal;
        }
    }

    // Inserir venda
    mysqli_query($ligacao, "INSERT INTO venda (id_cliente, data_venda, valor_total) VALUES ($id_cliente, NOW(), $total)");
    $id_venda = mysqli_insert_id($ligacao);

    // Inserir itens e atualizar stock
    foreach ($itens as $item) {
        if ($item['tipo'] === 'produto') {
            $res = mysqli_query($ligacao, "SELECT preco_unitario FROM produto WHERE id_produto = {$item['id']}");
            $dados = mysqli_fetch_assoc($res);
            $preco = $dados['preco_unitario'];

            // Inserir item
            mysqli_query($ligacao, "INSERT INTO venda_item (id_venda, id_produto, quantidade, preco_unitario)
                                    VALUES ($id_venda, {$item['id']}, {$item['quantidade']}, $preco)");

            // Atualizar stock
            mysqli_query($ligacao, "UPDATE produto SET stock_atual = stock_atual - {$item['quantidade']} WHERE id_produto = {$item['id']}");

        } elseif ($item['tipo'] === 'combustivel') {
            $res = mysqli_query($ligacao, "SELECT preco_unitario, stock_litros, capacidade_maxima FROM bomba WHERE id_registo = {$item['id']}");
            $dados = mysqli_fetch_assoc($res);
            $preco = $dados['preco_unitario'];
            $novo_stock = $dados['stock_litros'] - $item['quantidade'];
            $novo_nivel = ($novo_stock / $dados['capacidade_maxima']) * 100;

            mysqli_query($ligacao, "UPDATE bomba 
                                    SET stock_litros = $novo_stock, nivel_combustivel = $novo_nivel 
                                    WHERE id_registo = {$item['id']}");
        }
    }

    // Pontos fidelidade
    $pontos = floor($total / 10);
    if ($pontos > 0) {
        mysqli_query($ligacao, "INSERT INTO movimento_cartao (id_utilizador, tipo, pontos, descricao, data_movimento, id_venda)
                                VALUES ($id_cliente, 'entrada', $pontos, 'Compra', NOW(), $id_venda)");
    }

    echo "<script>alert('Venda registada com sucesso. Foram atribuídos $pontos pontos.'); window.location.href='registar_venda_form.php';</script>";
}
?>
