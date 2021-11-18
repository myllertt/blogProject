<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosSite.php");
require(__DIR_MODELS__."/UsuariosSite.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class AreaUsuarioLogadoController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objAuth; //Controle de sessão
    private $objUsuariosSite; //Trabalho com usuarios
    #-----------------------------------------------------

    /**
     * Verifica se o sessão do usuário esta essencialmente ativa e operacional
     *
     * @return void
     */
    private function _verificarSessaoAtivaDB_AutoRedEExit() : void{
        
        try {
            
            //Verificando sessão do usuário.
            if(!$this->objAuth->checkSeUsuarioEstaAutenticadoDB()){
                header("Location: ".\Sistema\Rotas::gerarLink('rota.site.login'));
                exit;
            }  

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir("errosGerais.ErroDB");    

            exit;
        }
        
    }

    /**
     * Instancia de objetos pertinentes à classe
     *
     * @return void
     */
    private function _instanciaObjetos() : void{

        $this->objAuth = new AuthUsuariosSite( DriverConexaoDB::getObjDB() );

        $this->objUsuariosSite = new UsuariosSite( DriverConexaoDB::getObjDB() );

    }

    /**
     * Emissão de erro inesperado
     *
     * @return void
     */
    private function _emitirViewErroInesperado_EXIT() : void{
        
        //Emitindo erro inesperado
        Views::abrir("errosGerais.ErroInesperado");
        exit;

    }

    /**
     * Construtor
     *
     * @param [\Sistema\ProcessamentoRotas\Request] $objRequest
     */
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();

        //Certifica de que tudo que chegue nesta classe precise estar logado, independente do método acionado
        $this->_verificarSessaoAtivaDB_AutoRedEExit();
    }

    /**
     * Acesso à área padrão do usuário.
     *
     * @return void
     */
    public function index() : void {

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        
        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Área do usuário",
            'auth' =>          $arrInfsUsLogado

        ];

    
        Views::abrir("site.us.areaUsuario", $arrayArgs);

    }

    /**
     * # Tela de Edição do Cadastro Usuário
     *
     * @return void
     */
    public function telaEditarCadastro() : void{

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();
     
        //Obter dados do usuário
        try {
            
            $arrDadosUs = $this->objUsuariosSite->getDadosCadastrais($arrInfsUsLogado['id']);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirViewErroInesperado_EXIT();
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir("errosGerais.ErroDB");    

            exit;
        }

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Editar Cadastro",
            'auth' =>          $arrInfsUsLogado

        ];

        //Passando dados em branco
        $arrayReq = [
            'nome' =>       $arrDadosUs['nome'],
            'sobrenome' =>  $arrDadosUs['sobrenome'],
            'genero' =>     $arrDadosUs['genero'],
            'email' =>      $arrDadosUs['email'],
        ];
        
        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> $arrayReq
        ];

 
        Views::abrir("site.us.areaUsuario.editCad", $arrayArgs);

    }
    /**
     * Processo de edição do cadastro
     *
     * @return void
     */
    public function processoEditarCadastro() : void{

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo dados da requisição
        $arrayReq = [
            'nome' => $this->getValorParmRequest("nme") ?? "",
            'sobrenome' => $this->getValorParmRequest("sno") ?? "",
            'genero' => $this->getValorParmRequest("gen") ?? "",
            'email' => $this->getValorParmRequest("eml") ?? "",
        ];

        try {

            //Tentando realizar operação
            $this->objUsuariosSite->editarDadosCadastrais($arrInfsUsLogado['id'], $arrayReq['nome'], $arrayReq['sobrenome'], $arrayReq['genero'], $arrayReq['email']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Editar Cadastro",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Os dados foram atualizados com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.editCad", $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir("errosGerais.ErroDB");

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Editar Cadastro",
                'auth' =>          $arrInfsUsLogado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorre uma falha interna na operação! Tente mais tarde por gentileza. #DB0001",
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.editCad", $arrayArgs);
            

        } catch (\Exception $e) { //Erro no procedimento.

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Editar Cadastro",
                'auth' =>          $arrInfsUsLogado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.editCad", $arrayArgs);
        }        

    }
    

    /**
     * # Tela de Exclusão de Conta
     *
     * @return void
     */
    public function telaExcluirMinConta(): void{

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();
     
        //Passando dados em branco
        $arrayReq = [
        ];

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Excluir Minha Conta",
            'auth' =>          $arrInfsUsLogado

        ];
        
        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> []
        ];

 
        Views::abrir("site.us.areaUsuario.excMinConta", $arrayArgs);

    }
    /**
     * Processo de Exclusão de Conta
     *
     * @return void
     */
    public function processoExcluirMinConta() : void{

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

    
        //Obter dados do usuário
        try {
            
            //Removendo usuário
            $this->objUsuariosSite->excluirUsuario($arrInfsUsLogado['id']);

            //Destruindo apenas da memória, pois o usuário em sí já foi removido 
            $this->objAuth->destruirSessaoApenasMemoriaSis();

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Excluir Minha Conta",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Seu usuário foi excluído com sucesso!", //$e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.excMinConta", $arrayArgs);
            
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Excluir Minha Conta",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorreu uma falha interna! Tente mais tarde por gentileza. #DB0001", //$e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.excMinConta", $arrayArgs);

        } catch (\Exception $e) { //Erro no procedimento.

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Excluir Minha Conta",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.excMinConta", $arrayArgs);

        }  

    }


    /**
     * # Tela Alterar Minha Senha
     *
     * @return void
     */
    public function telaAlterarSenha() : void{

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();
     
        //Passando dados em branco
        $arrayReq = [
        ];

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Alterar Minha Senha",
            'auth' =>          $arrInfsUsLogado #Informações do usuário logado

        ];
        
        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> []
        ];

 
        Views::abrir("site.us.areaUsuario.altSenha", $arrayArgs);

    }
    /**
     * # Tela de Exclusão de Conta
     *
     * @return void
     */
    public function processoAlterarSenha(){

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo dados da requisição
        $arrayReq = [
            'hashSenhaAtual' => $this->getValorParmRequest("sea") ?? "",
            'novaSenha' => $this->getValorParmRequest("nse") ?? "",
        ];

        try {
            
            //Alterando a senha
            $this->objUsuariosSite->alterarSenha($arrInfsUsLogado['id'], $arrayReq['hashSenhaAtual'], $arrayReq['novaSenha']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Alterar Minha Senha",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Sua senha foi alterada com sucesso!", //$e->getMessage(),
                'parms'=> []
            ];

            Views::abrir("site.us.areaUsuario.altSenha", $arrayArgs);
            
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Alterar Minha Senha",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorreu uma falha interna! Tente mais tarde por gentileza. #DB0001", //$e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.altSenha", $arrayArgs);

        } catch (\Exception $e) { //Erro no procedimento.

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Alterar Minha Senha",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.areaUsuario.altSenha", $arrayArgs);

        }

    }

    

    

}

?>