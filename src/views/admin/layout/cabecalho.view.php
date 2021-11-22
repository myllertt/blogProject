<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo htmlspecialchars($tituloPagina) ?? "Pagina" ?>
    </title>
    <link rel="stylesheet" href="/css/site/site.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>


<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="#"><?php echo _NOME_SIS_ ?></a>
    </div>
    
    <ul class="nav navbar-nav">

      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Postagens
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.listar') ?>">Listar</a></li>
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.posts.postar') ?>">Criar Postagem</a></li>
        </ul>
      </li>

      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Usuários
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.listar') ?>">Listar</a></li>
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.usuarios.cadastro') ?>">Cadastrar</a></li>
        </ul>
      </li>

      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Minha Conta
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.minhaConta.editCad') ?>">Editar Dados</a></li>
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.minhaConta.altSenha') ?>">Alterar Senha</a></li>
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.minhaConta.excConta') ?>">Excluir Meu Usuário</a></li>
        </ul>
      </li>


      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">Sistema
        <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <li><a href="<?php echo \Sistema\Rotas::gerarLink('rota.admin.sistema.backups') ?>">Backups</a></li>
        </ul>
      </li>


    </ul>

    <?php if(isset($auth) && isset($auth['usuario'])): ?>
      <button onclick="window.location='<?php echo \Sistema\Rotas::gerarLink('rota.admin.logout') ?>'" class="btn btn-danger navbar-btn">logout (<?php echo $auth['usuario'] ?>)</button>
    <?php else: ?>
      <button onclick="window.location='<?php echo \Sistema\Rotas::gerarLink('rota.admin.logout') ?>'" class="btn btn-danger navbar-btn">logout</button>
    <?php endif ?>
  </div>
</nav>

<div style="margin-top:60px"></div>
<div class="container">


