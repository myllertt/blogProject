<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Criar Postagem</h3>

    <?php if(!$results['sts']): ?>        
        <?php if($results['msg']): ?>   
            <div style="color:red; font-size:20px">
                <?php echo $results['msg'] ?>
            </div>
        <?php endif ?> 

    <div>

        <?php /*Debug*/?>
        
        <form action="" id="form" method="post">

            <div>
                <label for="sts">Status:</label>
                <select name="sts" id="sts">
                    <option value="1">Ativado</option>
                    <option value="0">Desativado</option>
                </select>
            </div>

            <div>
                <label for="tit">Título:</label>
                <input type="text" value="<?php echo htmlspecialchars($results['parms']['titulo']) ?>" name="tit" id="tit">
                </div>
            
            <div>
                <label for="cnt">Conteúdo:</label>
                <textarea name="cnt" id="cnt" cols="50" rows="10"><?php echo htmlspecialchars($results['parms']['conteudo']) ?></textarea>
            </div>
            
            <div>
                <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.listar') ?>'" class="btn btn-secondary" value="cancelar"> <input type="submit" class="btn btn-primary" value="Postar">
            </div>

        </form>

    </div>
    <?php elseif($results['sts']): ?>
        <h2>
            <?php echo $results['msg'] ?>
        </h2>
        <a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.listar') ?>">Voltar</a>
    <?php endif ?>

    <script src="/js/admin/posts/postar.js"></script>
    <script>
        this.objForm.setCampoStatus("<?php echo addslashes($results['parms']['status']) ?>");
    </script>
    

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>