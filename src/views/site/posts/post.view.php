<?php Sistema\Views\Views::abrir("site.layout.cabecalho") ?>

<body>

<?php if($results->haRegistro): ?>
    <div>
        <div class="titPost">
            <h4><?php echo $results->reg['titulo'] ?></h4>
        </div>
        <div class="prevPost">
            <?php echo $results->reg['conteudo'] ?>
        </div>
        <div class="userPost">
            <?php echo $results->reg['nomeUSAbrev'] ?> Em: <?php echo $reg['dataCadBR'] ?>
        </div>
    </div>
<?php else: ?>
    <div>Ops! A postagem não foi encontrada!</div>
<?php endif ?>

</body>

<?php Sistema\Views\Views::abrir("site.layout.rodape") ?>