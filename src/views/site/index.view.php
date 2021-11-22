<?php Sistema\Views\Views::abrir("site.layout.cabecalho", $_refArgsView) ?>

<h2>Postagens</h2>
<br>

<?php if($results->haRegistros): ?>
    
    <?php foreach($results->regs AS $ele): ?>
    <div>
        <div class="titPost">
            <h4><?php echo htmlspecialchars($ele['titulo']) ?></h4>
        </div>
        <div class="prevPost">
            <?php echo htmlspecialchars($ele['previa']) ?> <a href="<?php echo Sistema\Rotas::gerarLink("site.posts.get.id", $ele['id']) ?>">Ver mais</a>
        </div>
        <div class="userPost" style="color:gray">
            <h5><?php echo $ele['nomeUSAbrev'] ?> Em: <?php echo $ele['dataCadBR'] ?></h5>
            <?php if($ele['dataAtBR']) echo "(atualizado em: ".$ele['dataAtBR'].")" ?>
        </div>
        <br>
    </div>
    <?php endforeach ?>
    <div style="margin-top: 20px">
        <?php if($results->pagina > 1): ?>
            <a href="<?php echo Sistema\Rotas::gerarLink("site.home.pagina", $results->pagina-1) ?>">&lt;Página Anterior</a>
        <?php endif ?>
        <span>(<?php echo $results->pagina ?>)</span>
        <a href="<?php echo Sistema\Rotas::gerarLink("site.home.pagina", $results->pagina+1) ?>">Próxima Página&gt;</a>
    </div>
<?php else: ?>
    <div>Nenhum resultado encontrado para esta página.</div>
    <div style="margin-top: 20px">
        <a href="<?php echo Sistema\Rotas::gerarLink("site.home.pagina", $results->pagina-1) ?>">&lt;Página Anterior</a>
        <span>(<?php echo $results->pagina ?>)</span>
    </div>
<?php endif ?>
</div>

<?php Sistema\Views\Views::abrir("site.layout.rodape", $_refArgsView) ?>