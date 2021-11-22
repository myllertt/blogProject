<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Editar Postagem</h3>

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

    
    <form action="" id="form" method="post">
        <input type="hidden" name="_method" value = "PUT">
        
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
            <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.listar') ?>'" class="btn btn-secondary" value="cancelar"> <input type="submit" class="btn btn-primary" value="editar">
        </div>

    </form>

    <script src="/js/admin/posts/editar.js"></script>
    <script>
        this.objForm.setCampoStatus("<?php echo addslashes($results['parms']['status']) ?>");
    </script>
    

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>