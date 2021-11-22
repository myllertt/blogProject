<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>

    <h3>Lista de Usuários</h3>
    <br>


    <?php /* Verificando se existe resultados */?>
    <?php if($results->haRegistros): ?>
        
        <h4>
            Quantidade de usuários: <?php echo $results->qtdRegs ?>
        </h4>

        <table class="table table-striped">
        <thead>
            <tr>
            <th scope="col">Nome Completo</th>
            <th scope="col">Usuario</th>
            <th scope="col">Status</th>
            <th scope="col">Data Cadastro</th>
            <th scope="col">D. Ult Alteração</th>
            <th scope="col">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($results->regs AS $reg): ?>
                
                <tr>
                    <th scope="row">
                        <?php echo htmlspecialchars($reg['nomeComp']) ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($reg['usuario']) ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($reg['nomeStatus']) ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($reg['dataHoraCadBR']) ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($reg['dataHoraAtBR']) ?>
                    </td>
                    <td>
                        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.editCadUs', $reg['id']) ?>'" class="btn btn-primary" value="editar">
                        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.editPermsAc.id', $reg['id']) ?>'" class="btn btn-warning" value="permissões">
                        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.redefSenha', $reg['id']) ?>'" class="btn btn-primary" value="redefinir senha">
                        <input type="button" onclick="confirmarExclusao('<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.excluir', $reg['id']) ?>')" class="btn btn-danger" value="excluir">
                    </td>
                </tr>

        <?php endforeach ?>
        </tbody>
        </table>
        
    <?php else: ?>   
        <h3>
            Nenhum resultado foi encontrado!
        </h3>
    <?php endif ?>   
    
<script src="/js/admin/usuarios/listar.js"></script>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>