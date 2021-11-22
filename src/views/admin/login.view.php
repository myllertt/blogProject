<?php Sistema\Views\Views::abrir("admin.layout.cabecalhoLogin", $_refArgsView) ?>

<div style="margin-top:100px">
    <h1>Blog Project</h1>
    <h3>Login Administrativo
    </h3>

    <?php if($results['msg']): ?>   
        <div style="color:red; font-size:20px">
            <?php echo $results['msg'] ?>
        </div>
        <br>
    <?php endif ?> 
    
    <div>

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

        <!--
        <form action="" id="form" method="post">

            <div>
                <label for="usu">Usuário:</label>
                <input type="text" name="usu" value="<?php echo htmlspecialchars($results['parms']['usuario']) ?>" id="usu">
            </div>

            <div>
                <label for="sen">senha:</label>
                <input type="password" name="sen" id="sen">
            </div>

            <div>
               <input type="submit" value="Login">
            </div>            

        </form>

    </div>
    -->
</div>

<script src="/js/global/sha512.js"></script>
<script src="/js/admin/login.js"></script>

<?php Sistema\Views\Views::abrir("admin.layout.rodapeLogin", $_refArgsView) ?>