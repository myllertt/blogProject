<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Editar Meus Dados</h3>

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

        <?php /*Debug*/?>
        
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
                <label for="nme">Primeiro Nome:</label>
                <input type="text" value="<?php echo htmlspecialchars($results['parms']['nome']) ?>" name="nme" id="nme">
                </div>
            
            <div>
                <label for="sno">sobrenome:</label>
                <input type="text" name="sno" value="<?php echo htmlspecialchars($results['parms']['sobrenome']) ?>" id="sno">
            </div>
         
            <div>
                <label for="gen">Genero:</label>
                <select name="gen" id="gen">
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                    <option value="NM">Não Mencionado</option>
                </select>
            </div>
            
            <div>
                <label for="eml">e-mail:</label>
                <input type="text" name="eml" value="<?php echo htmlspecialchars($results['parms']['email']) ?>" id="eml">
            </div>

            <div>
            <br>
            <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink(_ROTA_ADMIN_HOME_) ?>'" class="btn btn-secondary" value="cancelar"> <input type="submit" class="btn btn-primary" value="Editar">
            </div>

        </form>

    </div>


<script src="/js/admin/minhaConta/editarCad.js"></script>
<script>
    this.objForm.setCampoGenero("<?php echo addslashes($results['parms']['genero']) ?>");
    this.objForm.setCampoStatus("<?php echo addslashes($results['parms']['status']) ?>");
</script>

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>