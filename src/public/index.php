<?php


//Incluindo arquivo geral de configurações.
require(__DIR__."/../configs/GlobalConfigs.php");
//Incluindo utilitários dos controladores
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ControladoresModo.php";
//Incluindo utilitários das Views
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ViewsModo.php";




//Redirecionando trafego para o arquivo de tratamento das rotas.
require(__DIR_RAIZ__."/".GBCFGS::$nomeDirRotas."/rotas.php");



?>