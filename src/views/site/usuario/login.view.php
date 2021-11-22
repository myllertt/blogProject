<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>
<body>

    <div class="container"> 
        <h3>Login do Usuário</h3>
        <br>

        <?php if($results['msg']): ?>   
            <div style="color:red; font-size:20px">
                <?php echo $results['msg'] ?>
            </div>
            <br>
        <?php endif ?> 
        

        <div class="m-4">
            <form action="" id="form" method="post">
                <div class="row mb-3">
                    <label for="usu" class="col-sm-2 col-form-label">Usuário</label>
                    <div class="col-sm-10">
                        <input type="text" id="usu" value="<?php echo htmlspecialchars($results['parms']['usuario']) ?>" name="usu" class="form-control" placeholder="Usuario" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="sen" class="col-sm-2 col-form-label">Senha</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="sen" name="sen" placeholder="Senha" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-10 offset-sm-2">
                        <button type="submit" class="btn btn-primary">login</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

</body>

<script src="/js/global/sha512.js"></script>
<script src="/js/site/login.js"></script>

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>