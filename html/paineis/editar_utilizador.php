<?php
include("../../php/conexao/conexao.php");

$msg = "";

// Eliminar utilizador (exceto cliente e administrador)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    $id_utilizador = $_POST['id'];

    $sql_tipo = "SELECT tipo FROM utilizadores WHERE id_utilizador = $id_utilizador";
    $resultado_tipo = mysqli_query($ligacao, $sql_tipo);
    $dados = mysqli_fetch_assoc($resultado_tipo);

    if ($dados['tipo'] == 'cliente' || $dados['tipo'] == 'administrador') {
        $msg = "<div class='alert alert-warning'>Não é permitido eliminar utilizadores do tipo cliente ou administrador!</div>";
    } else {
        mysqli_query($ligacao, "DELETE mc FROM movimento_cartao mc JOIN cartao_fidelidade cf ON mc.id_cartao = cf.id_cartao WHERE cf.id_utilizador = $id_utilizador");
        mysqli_query($ligacao, "DELETE FROM cartao_fidelidade WHERE id_utilizador = $id_utilizador");
        mysqli_query($ligacao, "DELETE vi FROM venda_item vi JOIN venda v ON vi.id_venda = v.id_venda WHERE v.id_utilizador = $id_utilizador");
        mysqli_query($ligacao, "DELETE FROM venda WHERE id_utilizador = $id_utilizador");
        mysqli_query($ligacao, "DELETE FROM agendamento WHERE id_cliente = $id_utilizador OR id_funcionario = $id_utilizador");
        mysqli_query($ligacao, "DELETE FROM alerta WHERE id_funcionario = $id_utilizador");

        $sql = "DELETE FROM utilizadores WHERE id_utilizador = $id_utilizador";
        if (mysqli_query($ligacao, $sql)) {
            $msg = "<div class='alert alert-danger'>Utilizador eliminado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao eliminar utilizador: " . mysqli_error($ligacao) . "</div>";
        }
    }
}

// Atualizar tipo (não pode mudar para cliente, nem alterar administrador)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar'])) {
    $id_utilizador = $_POST['id'];
    $novo_tipo = $_POST['tipo'];

    $sql_verificar = "SELECT tipo FROM utilizadores WHERE id_utilizador = $id_utilizador";
    $resultado_verificacao = mysqli_query($ligacao, $sql_verificar);
    $row = mysqli_fetch_assoc($resultado_verificacao);

    if ($row['tipo'] == 'administrador') {
        $msg = "<div class='alert alert-warning'>Não é permitido alterar o tipo de um utilizador administrador!</div>";
    } elseif ($novo_tipo == 'cliente') {
        $msg = "<div class='alert alert-warning'>Não é permitido alterar o tipo de utilizador para cliente!</div>";
    } elseif ($row['tipo'] === $novo_tipo) {
        $msg = "<div class='alert alert-warning m-3'>Nenhuma alteração detetada.</div>";
    } else {
        $sql_atualizar = "UPDATE utilizadores SET tipo='$novo_tipo' WHERE id_utilizador=$id_utilizador";
        if (mysqli_query($ligacao, $sql_atualizar)) {
            $msg = "<div class='alert alert-success'>Tipo de utilizador alterado com sucesso!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>Erro ao alterar o tipo: " . mysqli_error($ligacao) . "</div>";
        }
    }
}

$sql_lista = "SELECT * FROM utilizadores";
$resultado = mysqli_query($ligacao, $sql_lista);
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Utilizadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Gestão de Utilizadores</h2>
    <?php if (!empty($msg)) echo $msg; ?>

    <?php if (mysqli_num_rows($resultado) > 0): ?>
    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Tipo</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($resultado)): ?>
            <tr>
                <td><?= htmlspecialchars($row['nome']) ?></td>
                <td>
                    <?php if ($row['tipo'] != 'administrador' && $row['tipo'] != 'cliente'): ?>
                        <form method="POST" class="d-flex">
                            <input type="hidden" name="id" value="<?= $row['id_utilizador'] ?>">
                            <select name="tipo" class="form-select me-2">
                                <option value="operador" <?= $row['tipo']=='operador' ? 'selected' : '' ?>>Operador</option>
                                <option value="funcionario_administrativo" <?= $row['tipo']=='funcionario_administrativo' ? 'selected' : '' ?>>Funcionário Administrativo</option>
                                <option value="funcionario_servicos" <?= $row['tipo']=='funcionario_servicos' ? 'selected' : '' ?>>Funcionário de Serviços</option>
                                <option value="gerente_posto" <?= $row['tipo']=='gerente_posto' ? 'selected' : '' ?>>Gerente de Posto</option>
                            </select>
                            <button type="submit" name="atualizar" class="btn btn-sm btn-primary">Atualizar</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted"><?= ucfirst($row['tipo']) ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['tipo'] != 'cliente' && $row['tipo'] != 'administrador'): ?>
                        <form method="POST" onsubmit="return confirmarEliminacao();">
                            <input type="hidden" name="id" value="<?= $row['id_utilizador'] ?>">
                            <button type="submit" name="eliminar" class="btn btn-sm btn-danger">Eliminar</button>
                        </form>
                    <?php else: ?>
                        <span class="text-muted">Não permitido</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class='alert alert-warning'>Nenhum utilizador encontrado.</div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="painel_utilizador.php" class="btn btn-secondary">Voltar</a>
    </div>

    <script>
        function confirmarEliminacao() {
            return confirm("Tem a certeza que quer eliminar este utilizador?");
        }
    </script>
</body>
</html>
