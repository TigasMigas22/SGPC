<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Agendar Serviço</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Agendar Novo Serviço</h2>

    <form action="../../php/agendamento/agendar_servico.php" method="POST" class="border p-4 rounded bg-light shadow-sm">

        <div class="mb-3">
            <label for="servico" class="form-label">Serviço:</label>
            <select name="id_servico" class="form-select" required>
                <option value="">Selecione um serviço</option>
                <?php
                include('../../php/conexao/conexao.php');
                $resultado = mysqli_query($ligacao, "SELECT id_servico, descricao, preco FROM servico");
                while ($row = mysqli_fetch_assoc($resultado)) {
                    echo "<option value='{$row['id_servico']}'>{$row['descricao']} - " .
                         number_format($row['preco'], 2, ',', '.') . "€</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="data" class="form-label">Data:</label>
            <input type="date" name="data_agendamento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="hora" class="form-label">Hora:</label>
            <input type="time" name="hora_agendamento" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="observacoes" class="form-label">Observações:</label>
            <textarea name="observacoes" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Agendar Serviço</button>
        <button type="reset" class="btn btn-secondary ms-2">Limpar</button>
    </form>
    <a href="../paineis/painel_utilizador.php" class="btn btn-secondary mt-3">Voltar ao painel</a>
</div>

<script>
    const parametros = new URLSearchParams(window.location.search);
    if (parametros.get('sucesso') === '1') {
        alert('Agendamento realizado com sucesso!');
    } else if (parametros.get('erro') === '1') {
        alert('Erro: O agendamento deve ser feito com pelo menos 24 horas de antecedência.');
    } else if (parametros.get('erro') === '2') {
        alert('Erro ao processar o agendamento. Tente novamente.');
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
