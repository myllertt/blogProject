<?php

//Incluindo classe geral para processamento de rotas.
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ProcessamentoRotas.php";
//------------------

use Sistema\ProcessamentoRotas;

# Instancia do processamento de rotas
$objProcessamentoRotas = new ProcessamentoRotas();

# Definições das rotas do sistema.
try {

    #/rotas/blogs/{id}/comentario/{id}

    #Rota raiz do sistema
    $objProcessamentoRotas->definirMetodoGET(
        "/rotas/postsgastos/{id}",                                                    # Rota HTTP
        "PaginaInicialController",                              # Nome Classe Controlador
        "inicio",                                               # Nome método inicial de ataque
        __DIR_CONTROLADORES__."/PaginaInicialController.php"    # Endereço de inclusão do arquivo controlador respectivo
    ); 



} catch(Exception $e) {

    echo "Erro";

    echo $e->getMessage();

}

?>