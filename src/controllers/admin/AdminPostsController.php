<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosAdmin.php");
require(__DIR_MODELS__."/Posts.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class AdminPostsController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objAuth; //Controle de sessão
    private $objTrabalho;
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

    //Realiza o processo de verificar as permissões ACL para este conteúdo.
    private function _verificarPermissoesACL_AutoRedEExit() : void{
        
        //obtendo argumento passado pela rota.
        $argRaw = $this->getArgRawControlador();

        if(!$argRaw  || !isset($argRaw['objPerm']))
            return; //Significa que não existe permissões definidas para esta rota.

        try{

            //obtendo objeto já configurado com as permissões do usuário
            $objPermissoesACL = $this->objAuth->getObjPermissoesACL_DeUsuarioLogado();
            
            $resCheck = $objPermissoesACL->verificarEstadoPermissao($argRaw['objPerm']);

            if($resCheck === null){ //Erro inesperado
                $this->_emitirViewErroInesperado_EXIT();
            } else if($resCheck === false){ //Não permitido.
                Views::abrir(_ID_VIEW_ADM_ERRO_SEMPERM_);
                exit;
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
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

        $this->objTrabalho = new Posts( DriverConexaoDB::getObjDB() );

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
     * Emissão de erro do tipo usuário não encontrado
     *
     * @return void
     */
    private function _emitirErroRegsNaoEncontrado_EXIT(): void {
        
        //Emitindo erro inesperado
        Views::abrir('admin.posts.erroRegsNaoEnc');
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

        //Verificação das permissões ACL
        $this->_verificarPermissoesACL_AutoRedEExit();
    }

    //Listar Usuário
    public function telaListagem(){

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.posts.listar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo paginação do parâmetro passado via rota.
        $paginaAtual = $this->getValorParmViaRota(0);

        if(!isset($paginaAtual)){
            $paginaAtual = 1; //Página 1
        } else {
            $paginaAtual = (int) $paginaAtual;
        }     
        //---------------------------------------

        try {

            //Obtendo listas de usuários
            $arrayResultados = $this->objTrabalho->obterListaPaginadaPosts( $paginaAtual );

            //Até então não informa uma quatidade total.
            $qtdRegs = 0;

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' =>   _NOME_SIS_." - Admin / Lista de Postagens",
                'auth' =>           $arrInfsUsLogado
            ];

            //Estrutura de mensagem de retorno caso assim tenha
            $arrayArgs['msgRet'] = [
                'existe' => false,
                'tipo' => 1,            # 1 = Sucesso, 0 = Falha
                'msg' => ""             # Conteúdo da msg
            ];
            
            //Montando estrutura de registros
            $arrayArgs['results'] = (object) [
                'haRegistros' =>    !empty($arrayResultados['regs']) ?? false,
                'qtdRegs' =>        $qtdRegs,
                'regs' =>           $arrayResultados['regs'], //Registros
                'pagina' =>         $arrayResultados['pagina'], //Registros
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);
        }
        
    }

    # Área de Criar Postagem ------------------------
    //Tela para criar de postagem
    public function telaCriarPostagem(){

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.posts.postar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //O formulário neste estado ficará em branco
        $arrayParmsFormNulos = [
            'status' => "1",
            'titulo' => "",
            'conteudo' => "",
        ];

        //Argumentos padrões do sistema.            
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Criar Postagem",
            'auth' =>          $arrInfsUsLogado

        ];

        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> $arrayParmsFormNulos
        ];
        
        Views::abrir($strIdViewEspecMetodo, $arrayArgs);

    }
    //processo de criar postagem
    public function processoCriarPostagem(){

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.posts.postar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e armazenando parâmetros da requisição.
        $arrayReq = [

            'status' => $this->getValorParmRequest("sts") ?? "",
            'titulo' => $this->getValorParmRequest("tit") ?? "",
            'conteudo' => $this->getValorParmRequest("cnt") ?? "",
 
        ];

        try {

            //Tentando realizar operação
            $this->objTrabalho->criarPostagem($arrInfsUsLogado['id'], $arrayReq['status'], $arrayReq['titulo'], $arrayReq['conteudo']);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Criar Postagem",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "A postagem foi criada com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Criar Postagem",

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

                'tituloPagina' => _NOME_SIS_." - Admin / Criar Postagem",

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


    # Editar postagem ------------------
     /**
     * # Tela de Edição do Cadastro Usuário
     *
     * @return void
     */
    public function telaEditarPostagem() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.posts.editar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();


        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }
     
        //Obter dados postagem ------------------
        try {
            
            $arrDadosDB = $this->objTrabalho->getDadosPostagem($id);

            #Em caso de erro
            if(empty($arrDadosDB)){
                #Finalizando
                $this->_emitirErroRegsNaoEncontrado_EXIT();
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }


        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Editar Postagem",
            'auth' =>          $arrInfsUsLogado

        ];
        //--------------------------

        //Passando dados
        $arrayReq = [
            'status' =>     $arrDadosDB['status'],
            'titulo' =>     $arrDadosDB['titulo'],
            'conteudo' =>   $arrDadosDB['conteudo']
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
     * Processo de edição do cadastro do usuário
     *
     * @return void
     */
    public function processoEditarPostagem() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.posts.editar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e tratando parâmetro da rota -----
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }
        
      
        //Obter dados postagem ------------------
        try {
            
            $arrDadosDB = $this->objTrabalho->getDadosPostagem($id);

            #Em caso de erro
            if(empty($arrDadosDB)){
                #Finalizando
                $this->_emitirErroRegsNaoEncontrado_EXIT();
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }


        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Editar Postagem",
            'auth' =>          $arrInfsUsLogado

        ];
        //--------------------------


        //Obtendo dados da requisição
        $arrayReq = [
            'status' => $this->getValorParmRequest("sts") ?? "",
            'titulo' => $this->getValorParmRequest("tit") ?? "",
            'conteudo' => $this->getValorParmRequest("cnt") ?? ""
        ];

        try {
            
            //Tentando realizar operação
            $this->objTrabalho->editarPostagem($arrDadosDB['id'], $arrayReq['status'], $arrayReq['titulo'], $arrayReq['conteudo']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Postagem",
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

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Postagem",
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

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Postagem",
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


    # Excluir Postagem ------------------
    //Processo de exclusão de postagem
    public function processoExcluirPostagem() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.posts.excluir";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }
        
        //Tentando obter os dados do DB
        try {

            //Consultando dados do objeto pertinente no banco dados
            $arrDadosDB = $this->objTrabalho->getDadosPostagem($id);

            #Em caso de erro
            if(empty($arrDadosDB)){
                #Finalizando
                $this->_emitirErroRegsNaoEncontrado_EXIT();
            }
        
        } catch(DBException $e){ //Err

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }

        
        //Obtendo dados da requisição
        $arrayReq = [
        ];

        try {
            
            //Removendo usuário
            $this->objTrabalho->excluirPostagem($arrDadosDB['id']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Postagem",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "A postagem foi excluída com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Usuário",
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

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Usuário",
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


}

?>