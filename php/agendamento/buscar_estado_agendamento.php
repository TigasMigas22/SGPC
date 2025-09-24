<?php
session_start();
include('..\..\php\conexao\conexao.php');

// Verifica se o utilizador está autenticado e é funcionário de serviços
if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] != 'funcionario_servicos') {
    echo "<p>Operação não permitida.</p>";
    exit();
}

$id_funcionario = $_SESSION['id_utilizador'];

// Buscar os agendamentos com respetivo estado
$query = "
    SELECT a.id_agendamento, s.descricao, a.data_hora, a.estado
    FROM agendamento a
    JOIN servico s ON a.id_servico = s.id_servico
    WHERE a.id_funcionario = $id_funcionario
    ORDER BY a.data_hora DESC
";

$resultado = mysqli_query($ligacao, $query);

if (mysqli_num_rows($resultado) > 0) {
    echo "<ul>";
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<li>Serviço #{$row['id_agendamento']} - {$row['descricao']} ({$row['data_hora']}) → <strong>{$row['estado']}</strong></li>";
    }
    echo "</ul>";
} else {
    echo "<p>Não tem serviços atribuídos.</p>";
}

mysqli_close($ligacao);
?>
