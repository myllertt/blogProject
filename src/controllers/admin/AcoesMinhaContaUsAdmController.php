<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosAdmin.php");
require(__DIR_MODELS__."/UsuariosAdmin.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class AcoesMinhaContaUsAdmController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objAuth; //Controle de sessão
    private $objTrabalho; //Trabalho com usuarios
    #-----------------------------------------------------

    # Atributos específicos
    private $CFG_idViewPadraoTrabalho = ""; //id da view padrão de trabalho deste controlador

    /**
     * Verifica se o sessão do usuário esta essencialmente ativa e operacional
     *
     * @return void
     */
    private function _verificarSessaoAtivaDB_AutoRedEExit() : void{
        
        try {
            
            //Verificando sessão do usuário.
            if(!$this->objAuth->checkSeUsuarioEstaAutenticadoDB()){
                header("Location: ".\Sistema\Rotas::gerarLink(_ROTA_ADMIN_LOGIN_));
                exit;
            }  

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }
        
    }

    /**
     * Instancia de objetos pertinentes à classe
     *
     * @return void
     */
    private function _instanciaObjetos() : void{

        $this->objAuth = new AuthUsuariosAdmin( DriverConexaoDB::getObjDB() );

        $this->objTrabalho = new UsuariosAdmin( DriverConexaoDB::getObjDB() );

    }

    /**
     * Emissão de erro inesperado
     *
     * @return void
     */
    private function _emitirViewErroInesperado_EXIT() : void{
        
        //Emitindo erro inesperado
        Views::abrir(_ID_VIEW_GERAL_ERROINESPERADO_);
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
     * # Tela de Edição do Cadastro Usuário
     *
     * @return void
     */
    public function telaEditarCadastro() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.minhaConta.editarCadastro";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();
     
        //Obter dados do usuário
        try {
            
            $arrDadosUs = $this->objTrabalho->getDadosCadastrais($arrInfsUsLogado['id']);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirViewErroInesperado_EXIT();
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Editar Cadastro",
            'auth' =>          $arrInfsUsLogado

        ];

        //Passando dados em branco
        $arrayReq = [
            'nome' =>       $arrDadosUs['nome'],
            'status' =>     $arrDadosUs['status'],
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

 
        Views::abrir($strIdViewEspecMetodo, $arrayArgs);

    }
    /**
     * Processo de edição do cadastro
     *
     * @return void
     */
    public function processoEditarCadastro() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.minhaConta.editarCadastro";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo dados da requisição
        $arrayReq = [
            'status' => $this->getValorParmRequest("sts") ?? "",
            'nome' => $this->getValorParmRequest("nme") ?? "",
            'sobrenome' => $this->getValorParmRequest("sno") ?? "",
            'genero' => $this->getValorParmRequest("gen") ?? "",
            'email' => $this->getValorParmRequest("eml") ?? "",
        ];

        try {

            //Tentando realizar operação
            $this->objTrabalho->editarDadosCadastrais($arrInfsUsLogado['id'], $arrayReq['status'], $arrayReq['nome'], $arrayReq['sobrenome'], $arrayReq['genero'], $arrayReq['email']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Cadastro",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Os dados foram atualizados com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Cadastro",
                'auth' =>          $arrInfsUsLogado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorre uma falha interna na operação! Tente mais tarde por gentileza. #DB0001",
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
            

        } catch (\Exception $e) { //Erro no procedimento.

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Cadastro",
                'auth' =>          $arrInfsUsLogado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        }        

    }
    

    /**
     * # Tela de Exclusão de Conta
     *
     * @return void
     */
    public function telaExcluirConta(): void{ #ok

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.minhaConta.excluirConta";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();
     
        //Passando dados em branco
        $arrayReq = [
        ];

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Excluir Conta",
            'auth' =>          $arrInfsUsLogado

        ];
        
        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> []
        ];

 
        Views::abrir($strIdViewEspecMetodo, $arrayArgs);

    }
    /**
     * Processo de Exclusão de Conta
     *
     * @return void
     */
    public function processoExcluirConta() : void{ #ok

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.minhaConta.excluirConta";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

    
        //Obter dados do usuário
        try {
            
            //Removendo usuário
            $this->objTrabalho->excluirUsuario($arrInfsUsLogado['id']);

            //Destruindo apenas da memória, pois o usuário em sí já foi removido 
            $this->objAuth->destruirSessaoApenasMemoriaSis();

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Conta",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Seu usuário foi excluído com sucesso!", //$e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
            
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Conta",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorreu uma falha interna! Tente mais tarde por gentileza. #DB0001", //$e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        } catch (\Exception $e) { //Erro no procedimento.

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Conta",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        }  

    }


    /**
     * # Tela Alterar Minha Senha
     *
     * @return void
     */
    public function telaAlterarSenha() : void{ #ok

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.minhaConta.alterarSenha";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();
     
        //Passando dados em branco
        $arrayReq = [
        ];

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Alterar Senha",
            'auth' =>          $arrInfsUsLogado #Informações do usuário logado

        ];
        
        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> []
        ];

 
        Views::abrir($strIdViewEspecMetodo, $arrayArgs);

    }
    /**
     * # Tela de Exclusão de Conta
     *
     * @return void
     */
    public function processoAlterarSenha(){ #ok

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.minhaConta.alterarSenha";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo dados da requisição
        $arrayReq = [
            'hashSenhaAtual' => $this->getValorParmRequest("sea") ?? "",
            'novaSenha' => $this->getValorParmRequest("nse") ?? "",
        ];

        try {
            
            //Alterando a senha
            $this->objTrabalho->alterarSenha($arrInfsUsLogado['id'], $arrayReq['hashSenhaAtual'], $arrayReq['novaSenha']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Alterar Senha",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Sua senha foi alterada com sucesso!", //$e->getMessage(),
                'parms'=> []
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
            
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Alterar Senha",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorreu uma falha interna! Tente mais tarde por gentileza. #DB0001", //$e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        } catch (\Exception $e) { //Erro no procedimento.

            $arrayReq = [
            ];

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Alterar Senha",
                'auth' =>          $arrInfsUsLogado #Informações do usuário logado

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        }

    }

    

    

}

?>