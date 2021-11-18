<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>

<body>

    <h3>Login do Usuário</h3>

    <?php if($results['msg']): ?>   
        <div style="color:red; font-size:20px">
            <?php echo $results['msg'] ?>
        </div>
    <?php endif ?> 
    
    <div>

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
    
</body>

<script src="/js/global/sha512.js"></script>
<script src="/js/site/login.js"></script>

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>