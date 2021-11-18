<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>

<body>

    <h3>Registro do Usuário</h3>

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
                <label for="nme">Primeiro Nome:</label>
                <input type="text" value="<?php echo htmlspecialchars($results['parms']['nome']) ?>" name="nme" id="nme">
                </div>
            
            <div>
                <label for="sno">sobrenome:</label>
                <input type="text" name="sno" value="<?php echo htmlspecialchars($results['parms']['sobrenome']) ?>" id="sno">
            </div>

            <div>
                <label for="usu">Usuário:</label>
                <input type="text" value="<?php echo htmlspecialchars($results['parms']['usuario']) ?>" name="usu" id="usu">
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
                <label for="sen">senha:</label>
                <input type="password" name="sen" id="sen">
            </div>
            
            <div>
                <label for="rse">repita senha:</label>
                <input type="password" name="rse" id="rse">
            </div> 
            
            <div>
               <input type="submit" value="Cadastrar">
            </div>

        </form>

    </div>
    <?php elseif($results['sts']): ?>
        <h2>
            <?php echo $results['msg'] ?>
        </h2>
        <a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.login') ?>">Realizar Login</a>
    <?php endif ?>

    <script src="/js/site/registrar.js"></script>
    <script>
        this.objForm.setCampoGenero("<?php echo addslashes($results['parms']['genero']) ?>");
    </script>
    

</body>

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>