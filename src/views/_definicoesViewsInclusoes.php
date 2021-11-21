<?php

use Sistema\Views\VwException; 
use Sistema\Views\Views; 

/*
    Script responsável por associar os Id de chamadas das views com os seus arquivos respectivos presentes no diretório
*/

try {

    # Iniciando definições
    #Views::definir("idView", "LocalArquivo");

//CONFIGURAR AQUI----------------------------------------------------------------------------------------------

    #Pagina inicial
    Views::definir("site.index", __DIR_VIEWS__."/site/index.view.php");     
        #Layouts home
        Views::definir("site.layout.cabecalho", __DIR_VIEWS__."/site/layout/cabecalho.view.php");
        Views::definir("site.layout.rodape", __DIR_VIEWS__."/site/layout/rodape.view.php");  
        
    #Definição Posts
    #Visualizar um post
    Views::definir("site.posts.post", __DIR_VIEWS__."/site/posts/post.view.php");    
    
    
    #Definição Usuario
    #Tela de registro do usuário
    Views::definir("site.us.registrar", __DIR_VIEWS__."/site/usuario/registrar.view.php");
    #Tela de login do usuário
    Views::definir("site.us.login", __DIR_VIEWS__."/site/usuario/login.view.php");
    
    #Tela área usuário index
    Views::definir("site.us.areaUsuario", __DIR_VIEWS__."/site/usuario/areaUsuario/index.view.php");
    #Tela área usuário - editar cadastro
    Views::definir("site.us.areaUsuario.editCad", __DIR_VIEWS__."/site/usuario/areaUsuario/editarCadastro.view.php");
    #Tela área usuário - Excluir minha conta.
    Views::definir("site.us.areaUsuario.excMinConta", __DIR_VIEWS__."/site/usuario/areaUsuario/excluirMinhaConta.view.php");
    #Tela área usuário - Alterar minha senha
    Views::definir("site.us.areaUsuario.altSenha", __DIR_VIEWS__."/site/usuario/areaUsuario/alterarSenha.view.php");
    

    # [ÁREA ADMINISTRATIVA] -----------------------
    
    # Login admin - cabeçalho
    Views::definir("admin.layout.cabecalhoLogin", __DIR_VIEWS__."/admin/layout/cabecalhoLogin.view.php");
    # Login admin - rodape
    Views::definir("admin.layout.rodapeLogin", __DIR_VIEWS__."/admin/layout/rodapeLogin.view.php");
    # Login admin - Login corpo
    Views::definir("admin.login", __DIR_VIEWS__."/admin/login.view.php");

    # Index admin - home
    Views::definir("admin.index", __DIR_VIEWS__."/admin/index.view.php");

        #Layouts home
        Views::definir("admin.layout.cabecalho", __DIR_VIEWS__."/admin/layout/cabecalho.view.php");
        Views::definir("admin.layout.rodape", __DIR_VIEWS__."/admin/layout/rodape.view.php");  


    # MINHA CONTA
        //Editar cadastro do usuário
        Views::definir("admin.minhaConta.editarCadastro",   __DIR_VIEWS__."/admin/minhaConta/editarCadastro.view.php");
        //Alterar senha do usuário
        Views::definir("admin.minhaConta.alterarSenha",     __DIR_VIEWS__."/admin/minhaConta/alterarSenha.view.php");
        //Excluir conta do usuário
        Views::definir("admin.minhaConta.excluirConta",     __DIR_VIEWS__."/admin/minhaConta/excluirConta.view.php");

    # USUÁRIOS EM GERAL
        //Listar Usuários
        Views::definir("admin.usuarios.listar",          __DIR_VIEWS__."/admin/usuarios/listarUsuarios.view.php");
        //Cadastrar novo usuário
        Views::definir("admin.usuarios.cadastrar",          __DIR_VIEWS__."/admin/usuarios/cadastrarUsuario.view.php");
        
        //Editar cadastro do usuário.
        Views::definir("admin.usuarios.editCadUs",          __DIR_VIEWS__."/admin/usuarios/editarCadUsuario.view.php");

        //Excluir usuário
        Views::definir("admin.usuarios.excluir",          __DIR_VIEWS__."/admin/usuarios/excluirUsuario.view.php");

        //Redefinir senha do usuário
        Views::definir("admin.usuarios.redefSenha",          __DIR_VIEWS__."/admin/usuarios/redefinirSenhaUsuario.view.php");


        # EMISSÃO ERRO USUÁRIO NÃO ECONTRADO
        Views::definir("admin.usuarios.erroUsNaoEnc",          __DIR_VIEWS__."/admin/usuarios/emitirErroUsNaoEncontrado.view.php");

    # POSTS
        //Listar postagens
        Views::definir("admin.posts.listar",          __DIR_VIEWS__."/admin/posts/listarPosts.view.php");

        //Realizar postagem
        Views::definir("admin.posts.postar",          __DIR_VIEWS__."/admin/posts/postar.view.php");

        //Editar postagem
        Views::definir("admin.posts.editar",          __DIR_VIEWS__."/admin/posts/editarPostagem.view.php");
        
        //Excluir postagem.
        Views::definir("admin.posts.excluir",          __DIR_VIEWS__."/admin/posts/excluirPostagem.view.php");
        


        # EMISSÃO ERRO REGISTRO NÃO ENCONTRADO
        Views::definir("admin.posts.erroRegsNaoEnc",          __DIR_VIEWS__."/admin/posts/emitirErroRegsNaoEncontrado.view.php");

    # ERROS
        Views::definir("admin.erros.semPermissao",          __DIR_VIEWS__."/admin/erros/semPermissao.view.php");

    # ----------------------------------------------


    #Definição de erros gerais.
        #Erro geral de banco de dados
        Views::definir("errosGerais.ErroDB", __DIR_VIEWS__."/errosGerais/erroDB.view.php");
        #Erro inesperado
        Views::definir("errosGerais.ErroInesperado", __DIR_VIEWS__."/errosGerais/erroInesperado.view.php");
     


//-------------------------------------------------------------------------------------------------
    //Finalizando definições. Desta forma evita que as definições de views possam ser alteradas em tempo de execução
    Views::finalizarModificacoes();

} catch(VwException $e){
    die("Falha na definição das Views");
}

?>