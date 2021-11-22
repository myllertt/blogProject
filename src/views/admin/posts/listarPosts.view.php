<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Lista de Postagens</h3>
    <br>


    <?php /* Verificando se existe resultados */?>
    <?php if($results->haRegistros): ?>
        
        <!--
        <h4>
            Quantidade de posts:
        </h4>
        -->

        <table class="table table-striped">
        <thead>
            <tr>
            <th scope="col">Título</th>
            <th scope="col">Status</th>
            <th scope="col">Usuario</th>
            <th scope="col">Data Postagem</th>
            <th scope="col">D. Ult Alteração</th>
            <th scope="col">Ações</th>
            </tr>
        </thead>
        
        <tbody>
        <?php foreach($results->regs AS $reg): ?>
                
            <tr>
                <th scope="row">
                    <?php echo htmlspecialchars($reg['titulo']) ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($reg['nomeStatus']) ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($reg['nomeUSAbrev']) ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($reg['dataHoraCadBR']) ?>
                </td>
                <td>
                    <?php echo htmlspecialchars($reg['dataHoraAt']) ?>
                </td>
                
                <td>
                    <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.edit.id', $reg['id']) ?>'" class="btn btn-primary" value="editar">
                    <input type="button" onclick="confirmarExclusao('<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.excluir.id', $reg['id']) ?>')" class="btn btn-danger" value="excluir">
                </td>
            </tr>

        <?php endforeach ?>
        </tbody>
        </table>

        <div style="margin-top: 20px">
            <?php if($results->pagina > 1): ?>
                <a href="<?php echo Sistema\Rotas::gerarLink("rota.admin.posts.listar.pagina", $results->pagina-1) ?>">&lt;Página Anterior</a>
            <?php endif ?>
            <span>(<?php echo $results->pagina ?>)</span>
            <a href="<?php echo Sistema\Rotas::gerarLink("rota.admin.posts.listar.pagina", $results->pagina+1) ?>">Próxima Página&gt;</a>
        </div>

    <?php else: ?>   
        <h3>
            Nenhum resultado foi encontrado nesta página!
        </h3>

        <div style="margin-top: 20px">
            <a href="<?php echo Sistema\Rotas::gerarLink("rota.admin.posts.listar.pagina", $results->pagina-1) ?>">&lt;Página Anterior</a>
            <span>(<?php echo $results->pagina ?>)</span>
        </div>
    <?php endif ?>   
    
<script src="/js/admin/posts/listar.js"></script>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>