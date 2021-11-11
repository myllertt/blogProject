<?php

//Incluindo classes fundamentais
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ProcessamentoRotas.php";
//------------------

use Sistema\ProcessamentoRotas;
use Sistema\Rotas;
use Sistema\ProcessamentoRotas\Exceptions\PcRException; 
use Sistema\Views\Exceptions\VwException; 

# Instancia do processamento de rotas
$objProcessamentoRotas = new ProcessamentoRotas();

#Setando objeto de trabalho
Rotas::setObjetoTrabalho($objProcessamentoRotas);

Rotas::setObjetoTrabalho($objProcessamentoRotas);

try {

    # Definições das rotas do sistema --------------------------------------------------

    #Rota raiz do sistema
    $objProcessamentoRotas->definirRota_TODOS(
        "/",                                                        # Rota HTTP
        "PaginaInicialController",                                  # Nome Classe Controlador
        "redirecionarHome",                                         # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/PaginaInicialController.php",  # Endereço de inclusão do arquivo controlador respectivo,
        'site.index'                                                # Nome da rota.
    ); 

    # Rotas da página home --------------------------------------------------
    $objProcessamentoRotas->definirRota_TODOS(
        "/home",                        # Rota HTTP
        "PaginaInicialController",                                  # Nome Classe Controlador
        "inicio",                                                   # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/PaginaInicialController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'site.home'                                                 # Nome da rota.
    ); 

    $objProcessamentoRotas->definirRota_TODOS(
        "/home/{pagina}",                                           # Rota HTTP
        "PaginaInicialController",                                  # Nome Classe Controlador
        "inicio",                                                   # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/PaginaInicialController.php"   # Endereço de inclusão do arquivo controlador respectivo
    ); 

    # Posts do Blog -----------------------------------------------------------


    $objProcessamentoRotas->definirRota_TODOS(
        "/posts",                                                   # Rota HTTP
        "PostsController",                                          # Nome Classe Controlador
        "inicio",                                                   # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/PostsController.php"           # Endereço de inclusão do arquivo controlador respectivo
    ); 

    $objProcessamentoRotas->definirRota_GET(
        "/posts/{id}",                                              # Rota HTTP
        "PostsController",                                          # Nome Classe Controlador
        "getPost",                                                  # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/PostsController.php",          # Endereço de inclusão do arquivo controlador respectivo
        'site.posts.get.id'                                         # Nome da rota.
    );

    # Registro Site -----------------------------------------------------------

    //obter tela inicial registro usuario
    $objProcessamentoRotas->definirRota_GET(
        "/registrar",                                               # Rota HTTP
        "UsuariosSiteController",                                   # Nome Classe Controlador
        "exibirTelaRegistro",                                       # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/UsuariosSiteController.php",   # Endereço de inclusão do arquivo controlador respectivo
        'site.us.telaRegs.get'                                  # Nome da rota.
    );

    //Processo de registrar usuário
    $objProcessamentoRotas->definirRota_POST(
        "/registrar",                                               # Rota HTTP
        "UsuariosSiteController",                                   # Nome Classe Controlador
        "processoRegistrarUsuario",                                 # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/UsuariosSiteController.php",   # Endereço de inclusão do arquivo controlador respectivo
        'site.us.ProcRegs.post'                                  # Nome da rota.
    );

  

    # Acionando processamento rotas ---------------------------------------------------

    $objProcessamentoRotas->iniciarProcessamento();

} catch(PcRException $e) { //Erro no processamento e definição de rotas.

    echo "Erro Rota:<br>";

    echo "(".$e->getMessage().")";

} catch(VwException $e) { //Erro em alguma chamada de view
    echo "Erro view.<br>";

    echo "(".$e->getMessage().")";
}

?>