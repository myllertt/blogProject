<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

<body>

    <h3>Alterar Senha</h3>

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

        <form action="" id="form" method="post">
            <input type="hidden" name="_method" value = "PUT">
            <div>
                <label for="sea">senha atual:</label>
                <input type="password" name="sea" id="sea">
            </div>

            <div>
                <label for="nse">nova senha:</label>
                <input type="password" name="nse" id="nse">
            </div> 

            <div>
                <label for="rnse">repita nova senha:</label>
                <input type="password" name="rnse" id="rnse">
            </div> 

            <div>
               <input type="submit" value="Alterar"> <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink(_ROTA_ADMIN_HOME_) ?>'" value="voltar">
            </div>            

        </form>

    </div>
    
</body>

<script src="/js/global/sha512.js"></script>
<script src="/js/admin/minhaConta/alterarSenha.js"></script>

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>