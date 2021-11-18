<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $tituloPagina ?? "Pagina" ?>
    </title>
    <link rel="stylesheet" href="/css/site/site.css">
</head>
<div>
    <H1>Blog Project</H1>
    <ul>
        <li><a href="<?php echo \Sistema\Rotas::gerarLink("site.home") ?>">Home</a></li>
        <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.login') ?>">Login</a></li>
        <li><a href="<?php echo \Sistema\Rotas::gerarLink("site.us.telaRegs.get") ?>">Registrar</a></li>
    </ul>

    <hr>
</div>
