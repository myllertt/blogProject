<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>


    <title>
        <?php echo htmlspecialchars($tituloPagina) ?? "Pagina" ?>
    </title>
    <link rel="stylesheet" href="/css/site/site.css">
</head>

<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><?php echo _NOME_SIS_; ?></a>
    </div>
    <ul class="nav navbar-nav">
      <li class="active"><a href="<?php echo \Sistema\Rotas::gerarLink('site.home') ?>">POSTS</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-right">
      <li><a href="<?php echo \Sistema\Rotas::gerarLink('site.us.telaRegs.get') ?>"><span class="glyphicon glyphicon-user"></span> Registrar</a></li>
      <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.site.login') ?>"><span class="glyphicon glyphicon-log-in"></span> Login</a></li>
    </ul>
  </div>
</nav>
</div>
<body>
<div class="container">
