<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
<body>

    <h3>
        Desculpe! Você não tem permissões para acessar este conteúdo!
    </h3>

    <div>
        <input type="button" onclick="window.history.back()" value="voltar">
    </div>
    
</body>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>