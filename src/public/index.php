<?php

//Incluindo arquivo geral de configurações.
require(__DIR__."/../configs/GlobalConfigs.php");

//Redirecionando trafego para o arquivo de tratamento das rotas.
require(__DIR_RAIZ__."/".GBCFGS::$nomeDirRotas."/rotas.php");


?>