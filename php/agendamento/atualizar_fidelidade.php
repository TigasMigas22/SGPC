<?php
session_start();
include('../conexao/conexao.php');

// Verifica se é funcionário administrativo
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'funcionario_administrativo') {
    echo "<p>Acesso restrito.</p>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_utilizador = $_POST['id_utilizador'];
    $limite = $_POST['limite_gasto'];
    $premio = $_POST['premio'];

    // Verifica se já existe cartão
    $verificar = mysqli_query($ligacao, "SELECT * FROM cartao_fidelidade WHERE id_utilizador = $id_utilizador");

    if (mysqli_num_rows($verificar) > 0) {
        // Atualiza limite e prémio
        $update = "UPDATE cartao_fidelidade SET limite_gasto = $limite, premio = '$premio' WHERE id_utilizador = $id_utilizador";
        if (mysqli_query($ligacao, $update)) {
            echo "<p>Cartão atualizado com sucesso!</p>";
        } else {
            echo "<p>Erro ao atualizar: " . mysqli_error($ligacao) . "</p>";
        }
    } else {
        // Cria novo cartão com data de adesão
        $inserir = "INSERT INTO cartao_fidelidade (id_utilizador, limite_gasto, premio, data_adesao)
                    VALUES ($id_utilizador, $limite, '$premio', NOW())";
        if (mysqli_query($ligacao, $inserir)) {
            echo "<p>Cartão criado com sucesso!</p>";
        } else {
            echo "<p>Erro ao criar: " . mysqli_error($ligacao) . "</p>";
        }
    }

    echo "<a href='../../html/paineis/painel_utilizador.php'>Voltar ao painel</a>";
}

mysqli_close($ligacao);
?>
