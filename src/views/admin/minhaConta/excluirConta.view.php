<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>


    <h3>Excluir Minha Conta</h3>

    <?php if(!$results['sts']): ?>        
        <?php if($results['msg']): ?>   
            <div style="color:red; font-size:20px">
                <?php echo $results['msg'] ?>
            </div>
        <?php endif ?> 

    <div>
        <?php /*Debug*/?>
        
        <form action="" id="form" method="POST">
            <input type="hidden" name="_method" value = "DELETE">

            <h2>
                Atenção! Após realizar este processo não será mais possível desfazê-lo!
            </h2>
            
            <div>
                <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink(_ROTA_ADMIN_HOME_) ?>'" class="btn btn-secondary" value="cancelar"> <input type="submit" class="btn btn-danger" value="Confirmar Exclusão">
            </div>

        </form>
    </div>

    <?php elseif($results['sts']): ?>
        <h2>
            <?php echo $results['msg'] ?>
        </h2>
        <a href="<?php echo \Sistema\Rotas::gerarLink(_ROTA_ADMIN_LOGIN_) ?>">Retornar</a>
    <?php endif ?>

    <script src="/js/admin/minhaConta/excluirConta.js"></script>
    <script>
        this.objForm.setCampoGenero("<?php echo addslashes($results['parms']['genero']) ?>");
    </script>
    

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>