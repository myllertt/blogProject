<?php

//Incluindo classe geral para processamento de rotas.
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ProcessamentoRotas.php";
//------------------

use Sistema\ProcessamentoRotas;

# Instancia do processamento de rotas
$objProcessamentoRotas = new ProcessamentoRotas();


try {

    # Definições das rotas do sistema --------------------------------------------------

    #Rota raiz do sistema
    $objProcessamentoRotas->definirRota_GET(
        "/",                              # Rota HTTP
        "PaginaInicialController",                              # Nome Classe Controlador
        "inicio",                                               # Nome método inicial de ataque
        null,                                                   # Argumento passado 
        __DIR_CONTROLADORES__."/PaginaInicialController.php"    # Endereço de inclusão do arquivo controlador respectivo
    ); 

    $objProcessamentoRotas->definirRota_TODOS(
        "/rotas/posts/{id}/comments/10/{id}",                   # Rota HTTP
        "PaginaInicialController",                              # Nome Classe Controlador
        "inicio",                                               # Nome método inicial de ataque
        null,                                                   # Argumento passado 
        __DIR_CONTROLADORES__."/PaginaInicialController.php"    # Endereço de inclusão do arquivo controlador respectivo
    ); 


    $objProcessamentoRotas->definirRota_DELETE(
        "/",                                                    # Rota HTTP
        "PaginaInicialController",                              # Nome Classe Controlador
        "inicio",                                               # Nome método inicial de ataque
        null,                                                   # Argumento passado 
        __DIR_CONTROLADORES__."/PaginaInicialController.php"    # Endereço de inclusão do arquivo controlador respectivo
    ); 

    # Acionando processamento rotas ---------------------------------------------------

    $objProcessamentoRotas->iniciarProcessamento();

} catch(Exception $e) {

    echo "Erro";

    echo $e->getMessage();

}

?>