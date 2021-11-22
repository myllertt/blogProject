<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Exclusão de usuário</h3>

    <?php if($results['msg']): ?>   

        <?php if($results['sts']): ?>        
            <div style="color:blue; font-size:20px">
                <?php echo $results['msg'] ?>
            </div>        
        <?php else: ?> 
            <div style="color:red; font-size:20px">
                <?php echo $results['msg'] ?>
            </div> 
        <?php endif ?> 

    <?php endif ?> 

    <div>
        <br>
        <input type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.listar') ?>'"  value="voltar">
    </div>


<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>