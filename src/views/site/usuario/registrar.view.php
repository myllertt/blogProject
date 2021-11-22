<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>

<body>

    <h3>Registro do Usuário</h3>
    <br>

    <?php if(!$results['sts']): ?>        
        <?php if($results['msg']): ?>   
            <div style="color:red; font-size:20px">
                <?php echo $results['msg'] ?>
            </div>
            <br>
        <?php endif ?> 

    <div>

        <?php /*Debug*/?>


        <div class="m-4">
            <form action="" id="form" method="post">

                <div class="row mb-3">
                    <label for="nme" class="col-sm-2 col-form-label">Primeiro Nome</label>
                    <div class="col-sm-10">
                        <input type="text" id="nme" value="<?php echo htmlspecialchars($results['parms']['nome']) ?>" name="nme" class="form-control" placeholder="Primeiro Nome" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="sno" class="col-sm-2 col-form-label">Sobrenome</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="sno" name="sno" value="<?php echo htmlspecialchars($results['parms']['sobrenome']) ?>" placeholder="Sobrenome">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="usu" class="col-sm-2 col-form-label">Usuário</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="usu" name="usu" value="<?php echo htmlspecialchars($results['parms']['usuario']) ?>" placeholder="Usuário" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="gen" class="col-sm-2 col-form-label">Genero</label>
                    <div class="col-sm-10">
                        <select name="gen" class="form-control" id="gen">
                            <option value="M">Masculino</option>
                            <option value="F">Feminino</option>
                            <option value="NM">Não Mencionado</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="eml" class="col-sm-2 col-form-label">e-mail</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="eml" name="eml" value="<?php echo htmlspecialchars($results['parms']['email']) ?>" placeholder="e-mail">
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="sen" class="col-sm-2 col-form-label">Senha</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="sen" name="sen" placeholder="Senha" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="rse" class="col-sm-2 col-form-label">Repita senha</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="rse" name="rse" placeholder="Repita senha" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </div>

            </form>
        </div>



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