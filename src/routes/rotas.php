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
        'site.us.telaRegs.get'                                      # Nome da rota.
    );

    //Processo de registrar usuário
    $objProcessamentoRotas->definirRota_POST(
        "/registrar",                                               # Rota HTTP
        "UsuariosSiteController",                                   # Nome Classe Controlador
        "processoRegistrarUsuario",                                 # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/UsuariosSiteController.php",   # Endereço de inclusão do arquivo controlador respectivo
        'site.us.ProcRegs.post'                                     # Nome da rota.
    );

    # Login Sistema -----------------------------------------------------------

     //obter tela inicial registro usuario
     $objProcessamentoRotas->definirRota_GET(
        "/login",                                                       # Rota HTTP
        "AutenticacaoSiteController",                                   # Nome Classe Controlador
        "telaLogin",                                                    # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AutenticacaoSiteController.php",   # Endereço de inclusão do arquivo controlador respectivo
        'rota.site.login'                                               # Nome da rota.
    );

    //Processo de login usuário
    $objProcessamentoRotas->definirRota_POST(
        "/login",                                                       # Rota HTTP
        "AutenticacaoSiteController",                                   # Nome Classe Controlador
        "processoLogin",                                                # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AutenticacaoSiteController.php"    # Endereço de inclusão do arquivo controlador respectivo
    );


    # Logout do sistema -----------------------------------------------------------

    //Processo de finalizar sessão
    $objProcessamentoRotas->definirRota_GET(
        "/logout",                                                      # Rota HTTP
        "AutenticacaoSiteController",                                   # Nome Classe Controlador
        "logout",                                                       # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AutenticacaoSiteController.php"    # Endereço de inclusão do arquivo controlador respectivo
    );

    # Area Usuário Site -----------------------------------------------------------

    //obter tela inicial de edição de cadastro
    $objProcessamentoRotas->definirRota_GET(
        "/areaUsuario/editarCad",                                       # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "telaEditarCadastro",                                            # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.site.areaUs.editCad'                                      # Nome da rota.
    );

    //Ação editar cadastro
    $objProcessamentoRotas->definirRota_PUT(
        "/areaUsuario/editarCad",                                       # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "processoEditarCadastro",                                       # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );

    //Excluir minha conta.
    $objProcessamentoRotas->definirRota_GET(
        "/areaUsuario/excluirConta",                                    # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "telaExcluirMinConta",                                          # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.site.areaUs.excMinConta'                                      # Nome da rota.
    );

    //Processo de exclusão da conta.
    $objProcessamentoRotas->definirRota_DELETE(
        "/areaUsuario/excluirConta",                                    # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "processoExcluirMinConta",                                      # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );








    //Tela alterar minha senha
    $objProcessamentoRotas->definirRota_GET(
        "/areaUsuario/alterarSenha",                                    # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "telaAlterarSenha",                                             # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.site.areaUs.altSenha'                                     # Nome da rota.
    );

    //Processo alterar minha senha
    $objProcessamentoRotas->definirRota_PUT(
        "/areaUsuario/alterarSenha",                                    # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "processoAlterarSenha",                                         # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
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