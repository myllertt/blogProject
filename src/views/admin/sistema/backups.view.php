<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
    <h3>
        Backups da Base de Dados
    </h3>

    <div>
        <input type="button" onclick="window.location='<?php echo \Sistema\Rotas::gerarLink('rota.admin.sistema.backups.download') ?>'" class="btn btn-primary" value="DOWNLOAD">
    </div>
    
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>