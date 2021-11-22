<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosAdmin.php");
require(__DIR_MODELS__."/UsuariosAdmin.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class UsuariosAdminController extends Controlador{

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
     * Emissão de erro do tipo usuário não encontrado
     *
     * @return void
     */
    private function _emitirErroUsNaoEncontrado_EXIT(): void {
        
        //Emitindo erro inesperado
        Views::abrir('admin.usuarios.erroUsNaoEnc');
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
        $strIdViewEspecMetodo = "admin.usuarios.listar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        try {

            //Obtendo listas de usuários
            $arrayResultados = $this->objTrabalho->getUsuarios();


            $qtdRegs = count($arrayResultados);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' =>   _NOME_SIS_." - Admin / Lista de Usuários",
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
                'haRegistros' =>    $qtdRegs > 0 ?? false,
                'qtdRegs' =>        $qtdRegs,
                'regs' =>           $arrayResultados, //Registros
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);
        }
        
    }

    # Área de cadastro ------------------------
    public function telaCadastro(){

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.cadastrar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //O formulário neste estado ficará em brando
        $arrayParmsFormNulos = [
            'status' => "1",
            'usuario' => "",
            'nome' => "",
            'sobrenome' => "",
            'genero' => "",
            'email' => "",
            'senha' => ""
        ];

        //Argumentos padrões do sistema.            
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Cadastrar Usuario",
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
    public function processoCadastro(){

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.cadastrar";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e armazenando parâmetros da requisição.
        $arrayReq = [

            'status' => $this->getValorParmRequest("sts") ?? "",
            'usuario' => $this->getValorParmRequest("usu") ?? "",
            'nome' => $this->getValorParmRequest("nme") ?? "",
            'sobrenome' => $this->getValorParmRequest("sno") ?? "",
            'genero' => $this->getValorParmRequest("gen") ?? "",
            'email' => $this->getValorParmRequest("eml") ?? "",
            'senha' => $this->getValorParmRequest("sen") ?? ""

        ];

        try {

            //Tentando registrar usuário
            $this->objTrabalho->cadastrarUsuario($arrayReq['status'], $arrayReq['usuario'], $arrayReq['nome'], $arrayReq['sobrenome'], $arrayReq['genero'], $arrayReq['email'], $arrayReq['senha']);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Cadastrar Usuario",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Seu usuário foi cadastrado com sucesso! Por gentileza configure as permissões do mesmo na Sequência.",
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Cadastrar Usuario",

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

                'tituloPagina' => _NOME_SIS_." - Admin / Cadastrar Usuario",

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

    # Editar Cadastro Usuário ------------------
     /**
     * # Tela de Edição do Cadastro Usuário
     *
     * @return void
     */
    public function telaEditarCadUs() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.editCadUs";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();


        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }
     
        //Obter dados do usuário
        try {
            
            $arrDadosUs = $this->objTrabalho->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Editar Cadastro Usuario",
            'auth' =>          $arrInfsUsLogado

        ];

        //Passando dados em branco
        $arrayReq = [
            'id' =>         $arrDadosUs['id'],
            'usuario' =>    $arrDadosUs['usuario'],
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
     * Processo de edição do cadastro do usuário
     *
     * @return void
     */
    public function processoEditarCadUs() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.editCadUs";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }

        //Tentando obter os dados cadastrais do usuário.
        try {

            //Consultando dados do usuário a ser alterado.
            $arrDadosUs = $this->objTrabalho->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
            }
        
        } catch(DBException $e){ //Err

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }

        //Obtendo dados da requisição
        $arrayReq = [
            'id' => $arrDadosUs['id'],
            'usuario' => $arrDadosUs['usuario'],
            'status' => $this->getValorParmRequest("sts") ?? "",
            'nome' => $this->getValorParmRequest("nme") ?? "",
            'sobrenome' => $this->getValorParmRequest("sno") ?? "",
            'genero' => $this->getValorParmRequest("gen") ?? "",
            'email' => $this->getValorParmRequest("eml") ?? "",
        ];

        try {
            
            //Tentando realizar operação
            $this->objTrabalho->editarDadosCadastrais($arrDadosUs['id'], $arrayReq['status'], $arrayReq['nome'], $arrayReq['sobrenome'], $arrayReq['genero'], $arrayReq['email']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Editar Cadastro Usuário",
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
    

    # Excluir usuário ------------------
    //Processo de exclusão de usuário
    public function processoExcluirUs() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.excluir";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }


        //Tentando obter os dados cadastrais do usuário.
        try {

            //Consultando dados do usuário a ser alterado.
            $arrDadosUs = $this->objTrabalho->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
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
            $this->objTrabalho->excluirUsuario($arrDadosUs['id']);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Excluir Usuário",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "O usuário (".$arrDadosUs['usuario'].") foi excluído com sucesso!",
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



    # Redefinição de senha do usuário ------------------
     /**
     * # Tela de Redefinição de senha do usuário
     *
     * @return void
     */
    public function telaRedefinirSenhaUs() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.redefSenha";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();


        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }
     
        //Obter dados do usuário-----
        try {
            
            $arrDadosUs = $this->objTrabalho->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
            }

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }
        //------------------------------

        //Impedindo que o usuário tente alterar a própria senha por esta via. Este é uma questão de segurança.
        if($arrInfsUsLogado['id'] == $arrDadosUs['id']){
            header("Location: ".\Sistema\Rotas::gerarLink('rota.admin.minhaConta.altSenha'));
            exit;
        }

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Redefinir Senha Usuário",
            'auth' =>          $arrInfsUsLogado

        ];

        //Passando dados em branco
        $arrayReq = [
            'usuario' =>    $arrDadosUs['usuario']
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
     * Processo de Redefinição de senha do usuário
     *
     * @return void
     */
    public function processoRedefinirSenhaUs() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.usuarios.redefSenha";

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }

        //Tentando obter os dados cadastrais do usuário ---
        try {

            //Consultando dados do usuário a ser alterado.
            $arrDadosUs = $this->objTrabalho->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosUs)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
            }
        
        } catch(DBException $e){ //Err

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }
        //---------------

        //Impedindo que o usuário tente alterar a própria senha por esta via. Este é uma questão de segurança.
        if($arrInfsUsLogado['id'] == $arrDadosUs['id']){
            header("Location: ".\Sistema\Rotas::gerarLink('rota.admin.minhaConta.altSenha'));
            exit;
        }

        //Obtendo a nova senha direto do parâmetro da requisição
        $novaSenha = $this->getValorParmRequest("nse") ?? "";

        //Gerando dados para o encaminhamento para a view
        $arrayReq = [
            'usuario' => $arrDadosUs['usuario']
        ];

        try {
            
            //Tentando realizar operação
            $this->objTrabalho->redefinirSenha($arrDadosUs['id'], $novaSenha);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Redefinir Senha Usuário",
                'auth' =>          $arrInfsUsLogado

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "A senha do usuário foi redefinida com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Admin / Redefinir Senha Usuário",
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

                'tituloPagina' => _NOME_SIS_." - Admin / Redefinir Senha Usuário",
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