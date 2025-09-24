<?php
session_start();

if (!isset($_SESSION['id_utilizador']) || !isset($_SESSION['tipo'])) {
    header("Location: ../../html/autenticacao/login.html");
    exit();
}

$tipo = $_SESSION['tipo'];
$nome = $_SESSION['nome'];
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Painel de Utilizador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-3">Painel de Utilizador</h1>
    <p class="lead">Bem-vindo, <strong><?= htmlspecialchars($nome) ?></strong>!</p>

    <div class="list-group">

    <?php if ($tipo == 'administrador'): ?>
        <a href='editar_utilizador.php' class='list-group-item list-group-item-action'>Gerir Utilizadores</a>
        <a href='../../html/formularios/registar_utilizador.html' class='list-group-item list-group-item-action'>Registar Novo Utilizador</a>
        <a href='dados_pessoais.php' class='list-group-item list-group-item-action'>Dados Pessoais</a>
        <a href='../../php/autenticacao/autoexcluir_admin.php' class='list-group-item list-group-item-action text-danger' onclick='return confirmarExclusao();'>Autoexcluir Conta</a>

    <?php elseif ($tipo == 'gerente_posto'): ?>
        <a href='../../php/alertas/gerar_alertas_bombas.php' class='list-group-item list-group-item-action'>Ver Alertas de Bombas</a>
        <a href='dados_pessoais.php' class='list-group-item list-group-item-action'>Dados Pessoais</a>

    <?php elseif ($tipo == 'operador'): ?>
        <a href='../../php/vendas/registar_venda_form.php' class='list-group-item list-group-item-action'>Registar Venda</a>
        <a href='../../php/agendamento/inserir_produto.php' class='list-group-item list-group-item-action'>Inserir Produto</a>
        <a href='../../html/formularios/consultar_movimentos_fidelidade.php' class='list-group-item list-group-item-action'>Consultar Histórico de Pontos</a>
        <a href='dados_pessoais.php' class='list-group-item list-group-item-action'>Dados Pessoais</a>

    <?php elseif ($tipo == 'funcionario_administrativo'): ?>
        <a href='../../php/agendamento/inserir_servico.php' class='list-group-item list-group-item-action'>Inserir Serviço</a>
        <a href='../../html/formularios/atribuir_funcionario_form.php' class='list-group-item list-group-item-action'>Atribuir Funcionário a Serviço</a>
        <a href='../../html/formularios/formulario_gestao_fidelidade.php' class='list-group-item list-group-item-action'>Gerir Fidelização de Clientes</a>
        <a href='dados_pessoais.php' class='list-group-item list-group-item-action'>Dados Pessoais</a>

    <?php elseif ($tipo == 'funcionario_servicos'): ?>
        <a href='ver_agendamentos.php' class='list-group-item list-group-item-action'>Ver Agendamentos</a>
        <a href='../lss/executar_comando_lss.php' class='list-group-item list-group-item-action'>Executar LSS</a>
        <a href='dados_pessoais.php' class='list-group-item list-group-item-action'>Dados Pessoais</a>

    <?php elseif ($tipo == 'cliente'): ?>
        <a href='../formularios/agendar_servico_formulario.php' class='list-group-item list-group-item-action'>Agendar Serviço</a>
        <a href='ver_premios_fidelidade.php' class='list-group-item list-group-item-action'>Consultar Catálogo de Pontos</a>
        <a href='consultar_pontos_clientes.php' class='list-group-item list-group-item-action'>Consultar Histórico de Movimentos</a>
        <a href='resgatar_premios.php' class='list-group-item list-group-item-action'>Resgatar Prémios</a>
        <a href='ver_estado_cliente.php' class='list-group-item list-group-item-action'>Ver Agendamentos</a>
        <a href='dados_pessoais.php' class='list-group-item list-group-item-action'>Dados Pessoais</a>
        <a href='../../php/autenticacao/encerrar_conta.php' class='list-group-item list-group-item-action text-danger' onclick='return confirmarEncerramento();'>Encerrar Conta</a> 

    <?php else: ?>
        <div class='alert alert-warning'>Tipo de utilizador não reconhecido.</div>
    <?php endif; ?>

    </div>

    <form action='../../php/autenticacao/logout.php' method='POST' class="mt-4">
        <button type='submit' class='btn btn-outline-danger'>Terminar Sessão</button>
    </form>
</div>

<script>
    function confirmarExclusao() {
        return confirm('Tem a certeza que quer autoexcluir a sua conta de administrador?');
    }

    function confirmarEncerramento(){
        return confirm('Tem a certeza que quer encerrar a sua conta cliente?');
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
