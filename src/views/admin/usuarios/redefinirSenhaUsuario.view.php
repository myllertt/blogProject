<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Redefinir Senha Usuário</h3>

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

        <h3>Usuario: <?php echo $results['parms']['usuario']?></h3>
        <form action="" id="form" method="post">
            <input type="hidden" name="_method" value = "PUT">
            
            <div>
                <label for="nse">nova senha:</label>
                <input type="password" name="nse" id="nse">
            </div> 

            <div>
                <label for="rnse">repita nova senha:</label>
                <input type="password" name="rnse" id="rnse">
            </div> 

            <div>
                <br>
                <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.listar') ?>'" class="btn btn-secondary" value="cancelar"> <input type="submit" class="btn btn-primary" value="Alterar">
            </div>            

        </form>

    </div>
    
<script src="/js/global/sha512.js"></script>
<script src="/js/admin/usuarios/redefSenha.js"></script>

<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>