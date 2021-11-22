<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>


    <h3>Excluir minha conta</h3>

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
                <br>
                <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.site.areaUs') ?>'" class="btn btn-secondary" value="voltar">
                <input type="submit" class="btn btn-danger" value="Confirmar Exclusão">
            </div>

        </form>
    </div>

    <?php elseif($results['sts']): ?>
        <h2>
            <?php echo $results['msg'] ?>
        </h2>
        <a href="<?php echo \Sistema\Rotas::gerarLink('site.home') ?>">Acessar home</a>
    <?php endif ?>

    <script src="/js/site/areaUsuario/excluirMinhaConta.js"></script>
    <script>
        this.objForm.setCampoGenero("<?php echo addslashes($results['parms']['genero']) ?>");
    </script>
    

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>