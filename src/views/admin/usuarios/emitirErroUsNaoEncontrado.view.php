<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
<body>

    <h3>
        Desculpe! Não foi possível localizar o usuário esperado. Pode ser que o mesmo já não esteja mais disponível.
    </h3>

    <div>
        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.listar') ?>'" value="voltar">
    </div>
    
</body>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>