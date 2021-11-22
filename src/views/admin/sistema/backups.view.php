<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
<body>

    <h3>
        Backups da Base de Dados
    </h3>

    <div>
        <input type="button" onclick="window.location='<?php echo \Sistema\Rotas::gerarLink('rota.admin.sistema.backups.download') ?>'" value="DOWNLOAD">
    </div>
    
</body>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>