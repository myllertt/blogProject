<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
<body>

    <?php /* Verificando se existe resultados */?>
    <?php if($results->haRegistros): ?>
        
        <!--
        <h4>
            Quantidade de posts:
        </h4>
        -->

        <table>
            <tr>
                <th>
                    Título
                </th>
                <th>
                    Status
                </th>
                <th>
                    Usuario
                </th>
                <th>
                    Data Postagem
                </th>
                <th>
                    D. Ult Alteração
                </th>
                <th>
                    Ações
                </th>
            </tr>
        
            <?php foreach($results->regs AS $reg): ?>
                
                <tr>
                    <td>
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
                        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.edit.id', $reg['id']) ?>'" value="editar">
                        <input type="button" onclick="confirmarExclusao('<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.excluir.id', $reg['id']) ?>')" value="excluir">
                    </td>
                </tr>

            <?php endforeach ?>
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
    
</body>
<script src="/js/admin/posts/listar.js"></script>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>