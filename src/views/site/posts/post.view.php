<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>

<?php if($results->haRegistro): ?>
    <div>
        <div class="titPost">
            <h4><?php echo htmlspecialchars($results->reg['titulo']) ?></h4>
        </div>
        <div class="prevPost">
        <?php echo htmlspecialchars($results->reg['conteudo']) ?>
            
        </div>
        <br>
        <div class="userPost" style="color:gray">
            <?php echo htmlspecialchars($results->reg['nomeUSAbrev']) ?> Em: <?php echo $results->reg['dataCadBR'] ?>
            <?php if($results->reg['dataAtBR']) echo "(atualizado em: ".$results->reg['dataAtBR'].")" ?>
        </div>
    </div>
<?php else: ?>
    <div>Ops! A postagem não foi encontrada!</div>
<?php endif ?>

<br>
<div>
    <a onclick="window.history.go(-1);" href="#">Voltar</a>
</div>

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>