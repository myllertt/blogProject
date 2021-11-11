<?php Sistema\Views\Views::abrir("site.layout.cabecalho") ?>

<body>

    <h3>Registro do Usuário</h3>

    <?php if(!$results['sts']): ?>

    <div>

        <form action="" id="form" method="post">

            <div>
                <label for="usu">Usuário:</label>
                <input type="text" name="usu" id="usu">
            </div>

            <div>
                <label for="nme">Nome:</label>
                <input type="text" name="nme" id="nme">
                </div>
            
            <div>
                <label for="sno">sobrenome:</label>
                <input type="text" name="sno" id="sno">
            </div>
            
            <div>
                <label for="gen">Genero:</label>
                <select name="gen" id="gen">
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                    <option value="NF">Não Mencionado</option>
                </select>
            </div>
            
            <div>
                <label for="eml">e-mail:</label>
                <input type="text" name="eml" id="eml">
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
    <?php endif ?>

    <script src="/js/site/registrar.js"></script>
    

</body>

<?php Sistema\Views\Views::abrir("site.layout.rodape") ?>