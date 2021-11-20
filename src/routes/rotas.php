<?php

//Incluindo classes fundamentais
require __DIR_RAIZ__ . "/".GBCFGS::$nomeDirLibsSis. "/ProcessamentoRotas.php";
//------------------

use Sistema\ProcessamentoRotas;
use Sistema\Rotas;
use Sistema\ProcessamentoRotas\Exceptions\PcRException; 
use Sistema\Views\Exceptions\VwException; 

# Instância do processamento de rotas
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
        __DIR_CONTROLADORES__."/site/PaginaInicialController.php",   # Endereço de inclusão do arquivo controlador respectivo,
        "site.home.pagina"
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
        __DIR_CONTROLADORES__."/site/AutenticacaoSiteController.php",   # Endereço de inclusão do arquivo controlador respectivo
        'rota.site.logout'                                               # Nome da rota.
    );




    # Area Usuário Site ====================================================================

    //Área Padrão do usuário.
    $objProcessamentoRotas->definirRota_TODOS(
        "/areaUsuario",                                                 # Rota HTTP
        "AreaUsuarioLogadoController",                                  # Nome Classe Controlador
        "index",                                                        # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/site/AreaUsuarioLogadoController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.site.areaUs'                                      # Nome da rota.
    );

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




    #Área administrativa ====================================================================

    //Raiz admin
    $objProcessamentoRotas->definirRota_TODOS(
        "/admin",                                                       # Rota HTTP
        "HomeAdminController",                                          # Nome Classe Controlador
        "index",                                                        # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/HomeAdminController.php",         # Endereço de inclusão do arquivo controlador respectivo,
        'rota.admin.home'                                               # Nome da rota.
    );

    //Login Admin
    $objProcessamentoRotas->definirRota_GET(
        "/admin/login",                                                 # Rota HTTP
        "AutenticacaoAdminController",                                  # Nome Classe Controlador
        "telaLogin",                                                    # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AutenticacaoAdminController.php", # Endereço de inclusão do arquivo controlador respectivo,
        'rota.admin.login'                                              # Nome da rota.
    );

    //Login Admin - processo 
    $objProcessamentoRotas->definirRota_POST(
        "/admin/login",                                                 # Rota HTTP
        "AutenticacaoAdminController",                                  # Nome Classe Controlador
        "processoLogin",                                                # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AutenticacaoAdminController.php"  # Endereço de inclusão do arquivo controlador respectivo
    );

    //Processo de finalizar sessão usuário admin
    $objProcessamentoRotas->definirRota_GET(
        "/admin/logout",                                                    # Rota HTTP
        "AutenticacaoAdminController",                                      # Nome Classe Controlador
        "logout",                                                           # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AutenticacaoAdminController.php",     # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.logout'                                                 # Nome da rota.
    );

    
    //Ações Minha Conta ---------------------------------------------

    //Tela alterar minha senha
    $objProcessamentoRotas->definirRota_GET(
        "/admin/minhaConta/alterarSenha",                                   # Rota HTTP
        "AcoesMinhaContaUsAdmController",                                   # Nome Classe Controlador
        "telaAlterarSenha",                                                 # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AcoesMinhaContaUsAdmController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.minhaConta.altSenha'                                         # Nome da rota.
    );

    //Processo alterar minha senha
    $objProcessamentoRotas->definirRota_PUT(
        "/admin/minhaConta/alterarSenha",                                   # Rota HTTP
        "AcoesMinhaContaUsAdmController",                                   # Nome Classe Controlador
        "processoAlterarSenha",                                             # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AcoesMinhaContaUsAdmController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );


    //Tela Excluir Conta
    $objProcessamentoRotas->definirRota_GET(
        "/admin/minhaConta/excluirConta",                                   # Rota HTTP
        "AcoesMinhaContaUsAdmController",                                   # Nome Classe Controlador
        "telaExcluirConta",                                                 # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AcoesMinhaContaUsAdmController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.minhaConta.excConta'                                    # Nome da rota.
    );
    //Processo Excluir conta
    $objProcessamentoRotas->definirRota_DELETE(
        "/admin/minhaConta/excluirConta",                                   # Rota HTTP
        "AcoesMinhaContaUsAdmController",                                   # Nome Classe Controlador
        "processoExcluirConta",                                             # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AcoesMinhaContaUsAdmController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );


    //Tela Editar Cadastro
    $objProcessamentoRotas->definirRota_GET(
        "/admin/minhaConta/editarCad",                                       # Rota HTTP
        "AcoesMinhaContaUsAdmController",                                  # Nome Classe Controlador
        "telaEditarCadastro",                                            # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AcoesMinhaContaUsAdmController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.minhaConta.editCad'                                      # Nome da rota.
    );

    //Processo Editar Cadastro
    $objProcessamentoRotas->definirRota_PUT(
        "/admin/minhaConta/editarCad",                                       # Rota HTTP
        "AcoesMinhaContaUsAdmController",                                  # Nome Classe Controlador
        "processoEditarCadastro",                                       # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AcoesMinhaContaUsAdmController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );


    //Ações Usuários Em Geral ---------------------------------------------

    //Tela Listar usuários
    $objProcessamentoRotas->definirRota_GET(
        "/admin/usuarios/listar",                                       # Rota HTTP
        "UsuariosAdminController",                                          # Nome Classe Controlador
        "telaListagem",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.usuarios.listar'                                      # Nome da rota.
    );

    
    //Tela Cadastrar Usuário
    $objProcessamentoRotas->definirRota_GET(
        "/admin/usuarios/cadastrar",                                       # Rota HTTP
        "UsuariosAdminController",                                          # Nome Classe Controlador
        "telaCadastro",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.usuarios.cadastro'                                      # Nome da rota.
    );
    //Processo Cadastrar Usuário
    $objProcessamentoRotas->definirRota_POST(
        "/admin/usuarios/cadastrar",                                        # Rota HTTP
        "UsuariosAdminController",                                   # Nome Classe Controlador
        "processoCadastro",                                           # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );

    //Tela Editar cadastro de usuário específico
    $objProcessamentoRotas->definirRota_GET(
        "/admin/usuarios/editarCad/{id}",                               # Rota HTTP
        "UsuariosAdminController",                                          # Nome Classe Controlador
        "telaEditarCadUs",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.usuarios.editCadUs'                                      # Nome da rota.
    );
    //Processo Editar cadastro de usuário específico
    $objProcessamentoRotas->definirRota_PUT(
        "/admin/usuarios/editarCad/{id}",                                        # Rota HTTP
        "UsuariosAdminController",                                   # Nome Classe Controlador
        "processoEditarCadUs",                                           # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );


    //Processo de exclusão de usuário
    $objProcessamentoRotas->definirRota_DELETE(
        "/admin/usuarios/excluir/{id}",                                        # Rota HTTP
        "UsuariosAdminController",                                   # Nome Classe Controlador
        "processoExcluirUs",                                           # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.usuarios.excluir'                                      # Nome da rota.
    );


    //Tela Redefinir senha usuário
    $objProcessamentoRotas->definirRota_GET(
        "/admin/usuarios/redefSenha/{id}",                               # Rota HTTP
        "UsuariosAdminController",                                          # Nome Classe Controlador
        "telaRedefinirSenhaUs",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.usuarios.redefSenha'                                      # Nome da rota.
    );
    //Processo Redefinir senha usuário
    $objProcessamentoRotas->definirRota_PUT(
        "/admin/usuarios/redefSenha/{id}",                                        # Rota HTTP
        "UsuariosAdminController",                                   # Nome Classe Controlador
        "processoRedefinirSenhaUs",                                           # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/UsuariosAdminController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );


    //Posts ---------------------------------------------

    //Tela Listar usuários
    $objProcessamentoRotas->definirRota_GET(
        "/admin/posts/listar",                                       # Rota HTTP
        "AdminPostsController",                                          # Nome Classe Controlador
        "telaListagem",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AdminPostsController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.posts.listar'                                      # Nome da rota.
    );
    //Tela Listar paginação
    $objProcessamentoRotas->definirRota_GET(
        "/admin/posts/listar/pag/{pagina}",                                       # Rota HTTP
        "AdminPostsController",                                          # Nome Classe Controlador
        "telaListagem",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AdminPostsController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.posts.listar.pagina'                                      # Nome da rota.
    );


    // Tela de Criação de Postagem
    $objProcessamentoRotas->definirRota_GET(
        "/admin/posts/postar",                                       # Rota HTTP
        "AdminPostsController",                                          # Nome Classe Controlador
        "telaCriarPostagem",                                                 # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AdminPostsController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.posts.postar'                                      # Nome da rota.
    );
    // Processo de Criação de Postagem
    $objProcessamentoRotas->definirRota_POST(
        "/admin/posts/postar",                                        # Rota HTTP
        "AdminPostsController",                                   # Nome Classe Controlador
        "processoCriarPostagem",                                           # Nome método inicial de ataque
        null,                                                               # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AdminPostsController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );


    //Tela editar postagem
    $objProcessamentoRotas->definirRota_GET(
        "/admin/posts/editar/{id}",                                       # Rota HTTP
        "AdminPostsController",                                  # Nome Classe Controlador
        "telaEditarPostagem",                                            # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AdminPostsController.php",  # Endereço de inclusão do arquivo controlador respectivo
        'rota.admin.posts.edit.id'                                      # Nome da rota.
    );
    //Processo editar postagem
    $objProcessamentoRotas->definirRota_PUT(
        "/admin/posts/editar/{id}",                                       # Rota HTTP
        "AdminPostsController",                                  # Nome Classe Controlador
        "processoEditarPostagem",                                       # Nome método inicial de ataque
        null,                                                           # Argumento passado 
        __DIR_CONTROLADORES__."/admin/AdminPostsController.php",  # Endereço de inclusão do arquivo controlador respectivo
    );
    

    #========================================================================================


















    #Apenas para um indicador de funcionalidade para o processamento de rotas.
    $objProcessamentoRotas->definirRota_TODOS(
        "/teste_sistema",                                           # Rota HTTP
        "PaginaInicialController",                                  # Nome Classe Controlador
        "informativoTesteSistema",                                  # Nome método inicial de ataque
        null,                                                       # Argumento passado 
        __DIR_CONTROLADORES__."/site/PaginaInicialController.php",  # Endereço de inclusão do arquivo controlador respectivo
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