<?php Sistema\Views\Views::abrir("site.layout.cabecalho") ?>

<body>

<?php if($results->haRegistros): ?>
    
    <?php foreach($results->regs AS $ele): ?>
    <div>
        <div class="titPost">
            <h4><?php echo $ele['titulo'] ?></h4>
        </div>
        <div class="prevPost">
            <?php echo $ele['previa'] ?> <a href="<?php echo Sistema\Rotas::gerarLink("site.posts.get.id", $ele['id']) ?>">Ver mais</a>
        </div>
        <div class="userPost">
            <?php echo $ele['nomeUSAbrev'] ?> Em: <?php echo $ele['dataCadBR'] ?>
            <?php if($ele['dataAtBR']) echo "(atualizado em: ".$ele['dataAtBR'].")" ?>
        </div>
    </div>
    <?php endforeach ?>
<?php else: ?>
    <div>Nenhum resultado encontrado para esta página.</div>
<?php endif ?>

</body>

<?php Sistema\Views\Views::abrir("site.layout.rodape") ?>