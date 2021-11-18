<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>

<link rel="stylesheet" href="/css/site/areaUsuario/areaUsuario.css">
                             

<body>

    <h3>Olá <?php echo $auth['nome'] ?>, Seja bem-vindo(a)</h3>

    <div>
        <div class="divLink">
            <a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.areaUs.editCad') ?>">Editar Cadastro</a>
        </div>
        <div class="divLink">
            <a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.areaUs.altSenha') ?>">Alterar Minha Senha</a>
        </div>
        <div class="divLink">
            <a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.areaUs.excMinConta') ?>">Excluir Minha Conta</a>
        </div>
        <div class="divLink">
            <a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.logout') ?>">Sair</a>
        </div>
    </div>
    
    
</body>

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>
