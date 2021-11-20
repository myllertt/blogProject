<?php Sistema\Views\Views::abrir("admin.layout.cabecalho", $_refArgsView) ?>
<body>

    <?php /* Verificando se existe resultados */?>
    <?php if($results->haRegistros): ?>
        
        <h4>
            Quantidade de usuários: <?php echo $results->qtdRegs ?>
        </h4>

        <table>
            <tr>
                <th>
                    Nome Completo
                </th>
                <th>
                    Usuario
                </th>
                <th>
                    Status
                </th>
                <th>
                    Data Cadastro
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
                        <input type="button" onclick="window.location.href='<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.editCadUs', $reg['id']) ?>'" value="editar">
                        <input type="button" onclick="alert('Configurar')" value="redefinir senha">
                        <input type="button" onclick="confirmarExclusao('<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.excluir', $reg['id']) ?>')" value="excluir">
                    </td>
                </tr>

            <?php endforeach ?>
        </table> 
    <?php else: ?>   
        <h3>
            Nenhum resultado foi encontrado!
        </h3>
    <?php endif ?>   
    
</body>
<script src="/js/admin/usuarios/listar.js"></script>
<?php Sistema\Views\Views::abrir("admin.layout.rodape", $_refArgsView) ?>