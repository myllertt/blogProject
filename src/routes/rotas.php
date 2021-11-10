<?php

//Incluindo classes fundamentais
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ProcessamentoRotas.php";
//------------------

use Sistema\ProcessamentoRotas;
use Sistema\ProcessamentoRotas\Exceptions\PcRException; 
use Sistema\Views\Exceptions\VwException; 

# Instancia do processamento de rotas
$objProcessamentoRotas = new ProcessamentoRotas();


try {

    # Definições das rotas do sistema --------------------------------------------------

    #Rota raiz do sistema

    $objProcessamentoRotas->definirRota_TODOS(
        "/",                   # Rota HTTP
        "PaginaInicialController",                              # Nome Classe Controlador
        "inicio",                                               # Nome método inicial de ataque
        null,                                                   # Argumento passado 
        __DIR_CONTROLADORES__."/PaginaInicialController.php"    # Endereço de inclusão do arquivo controlador respectivo
    ); 

    $objProcessamentoRotas->definirRota_PUT(
        "/",                        # Rota HTTP
        "PaginaInicialController",                              # Nome Classe Controlador
        "inicio",                                               # Nome método inicial de ataque
        null,                                                   # Argumento passado 
        __DIR_CONTROLADORES__."/PaginaInicialController.php"    # Endereço de inclusão do arquivo controlador respectivo
    ); 

  

    # Acionando processamento rotas ---------------------------------------------------

    $objProcessamentoRotas->iniciarProcessamento();

} catch(PcRException $e) { //Erro no processamento e definição de rotas.

    echo "Erro Rota:<br>";

    echo $e->getMessage();

} catch(VwException $e) { //Erro em alguma chamada de view
    echo "Erro view.<br>";

    echo "(".$e->getMessage().")";
}

?>