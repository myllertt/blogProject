<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

<body>

    <h3>Editar Permissões de Acesso</h3>

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

    <h3>Usuário: <?php echo htmlspecialchars($results['parms']['usuario']) ?></h3>

    <div>

        <?php /*Debug*/?>
        
        <form action="" id="form" method="post">
            <input type="hidden" name="_method" value = "PUT">
            <input type="hidden" id="strCatPerms" name="strCatPerms" value = "">

            <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.editCadUs', $results['parms']['id']) ?>'" value="voltar"> <input type="submit" value="salvar edições">
            <br>
            <br>
            
            <div>
                <label for="regBase">Regra Base Padrão:</label>
                <select name="regBase" id="regBase">
                    <option value="permitir">permitir</option>
                    <option value="negar">negar</option>
                </select>
            </div>
        </form>
        <br>

        <?php if(!empty($results['arrTodasPerms'])): ?>

            <div>
                <input type = "checkbox" id="opc_marDesTodos" name = "" value = "1">
                <label for = "opc_marDesTodos">(Marcar/Desmarcar) TODOS</label>
            </div>
            <br>
            
            <?php foreach($results['arrTodasPerms'] AS $perm): ?>
                
                <div>
                    <input type = "checkbox" id = "<?php echo $perm['codigo']?>" name = "" acPerm="true" value = "<?php echo $perm['codigo']?>">
                    <label for = "<?php echo $perm['codigo']?>"><?php echo $perm['descricao']?> (<?php echo $perm['codigo']?>)</label>
                </div>

            <?php endforeach ?>

        <?php else: ?>
            <h3>Nenhuma permissão disponível no momento</h3>
        <?php endif ?>


    </div>

</body>

<script src="/js/admin/usuarios/editarPermsUs.js"></script>
<script>
    this.objForm.setCampoRegraBase("<?php echo addslashes($results['parms']['basePermsACL']) ?>");

    //Registrando estado das permissões.
    <?php foreach($results['arrPermsUs'] AS $perm): ?>
        objForm.marcarPorCodigo("<?php echo $perm['codigo']?>");
    <?php endforeach ?>

</script>

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>