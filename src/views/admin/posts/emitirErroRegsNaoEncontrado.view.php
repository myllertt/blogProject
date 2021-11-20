<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
<body>

    <h3>
        Desculpe! Não foi possível localizar o registro esperado. Pode ser que o mesmo já não esteja mais disponível.
    </h3>

    <div>
        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.listar') ?>'" value="voltar">
    </div>
    
</body>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>