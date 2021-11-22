<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosAdmin.php");
require(__DIR_MODELS__."/PermissoesUsuariosACL.php");
require(__DIR_MODELS__."/UsuariosAdmin.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

#utilitários de permissões
use Sistema\PermissoesACL\ACL_PERM;

class ManipulacaoPermissoesUsAdmController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objAuth; //Controle de sessão
    private $objTrabalho;
    private $objUsuariosAdmin;
    #-----------------------------------------------------

    # Atributos específicos
    private $CFG_idViewPadraoTrabalho = "admin.usuarios.editPermsAc"; //id da view padrão de trabalho deste controlador

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

        $this->objTrabalho = new PermissoesUsuariosACL( DriverConexaoDB::getObjDB() );

        $this->objUsuariosAdmin = new UsuariosAdmin( DriverConexaoDB::getObjDB() );

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

    # Específicos -----------------------------------

    //Recebe uma string concatenada de valores e a converte em um array
    private function _processarStrPermsParaArray(string $strCatPerms){

        $maxTamStrProc = 8192; //Tamanhomáximo aceito da string ($strCatPerms) a ser processado

        //Em caso de string vazia
        if($strCatPerms == "")
            return [];


        if(strlen($strCatPerms) > 8192)
            return false;
            
        $expPerms = explode(",", $strCatPerms);

        //Um array vazio
        if(empty($expPerms)){
            return [];
        }

        //Validando códigos recebidos
        foreach ($expPerms as $key => $perm) {

            //Validando estrutura do código.
            if(!ACL_PERM::validarCodigo($perm)){
                return false;
            }
            
        }

        //Retornando a explosão dos parâmetros
        return $expPerms;

    }

    //Converte uma array permisões simples em uma array composto. Que contem a chave código. 'valor' => ['codigo'] => 'valor'
    private function _converterArrayPermsSimples_ParaComposto(array $array) : array{

        if(empty($array)){
            return [];
        }

        //Array final de retorno.
        $arrayRet = [];

        foreach ($array as $elem) 
            $arrayRet[]['codigo'] = $elem;
        
        
        return $arrayRet;

    }

    #-----------------------------------------------

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


    # Editar Permissões de acesso do Usuário ------------------
     /**
     * # Tela de Edição de Permissões de acesso do Usuário
     *
     * @return void
     */
    public function telaEditarPermissoesAcesso() : void{
        
        #Id view específica deste método
        $strIdViewEspecMetodo = $this->CFG_idViewPadraoTrabalho;

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
            
            //obtendo dados do usuário selecionado.
            $arrDadosDB = $this->objUsuariosAdmin->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosDB)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
            }

            //obtendo um array de todas as permissões disponíveis no sistema
            $arrTodasPerms = $this->objTrabalho->obterTodasPermissoes();


            //obtendo um array de permissões do usuário em si
            $arrPermsUs = $this->objTrabalho->obterPermissoesUsuario($arrDadosDB['id']);

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Admin / Editar Permissões Usuário",
            'auth' =>          $arrInfsUsLogado

        ];

        //Passando dados
        $arrayReq = [
            'id' =>             $arrDadosDB['id'],
            'usuario' =>        $arrDadosDB['usuario'],
            'basePermsACL' =>   $arrDadosDB['basePermsACL']
        ];
        
        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> $arrayReq,
            'arrTodasPerms' => $arrTodasPerms,
            'arrPermsUs' =>    $arrPermsUs //Array de permissões do usuário.
        ];
 
        Views::abrir($strIdViewEspecMetodo, $arrayArgs);

    }
    /**
     * Processo de edição de Permissões de acesso do Usuário
     *
     * @return void
     */
    public function processoPermissoesAcesso() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = $this->CFG_idViewPadraoTrabalho;

        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();


        //Obtendo e tratando parâmetro da rota.
        $id = $this->getValorParmViaRota(0);
        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }
        
        //Obter dados do usuário--
        try {
            
            //obtendo dados do usuário selecionado.
            $arrDadosDB = $this->objUsuariosAdmin->getDadosCadastrais($id);

            #Em caso de erro
            if(empty($arrDadosDB)){
                #Finalizando
                $this->_emitirErroUsNaoEncontrado_EXIT();
            }

            //obtendo um array de todas as permissões disponíveis no sistema
            $arrTodasPerms = $this->objTrabalho->obterTodasPermissoes();


            //obtendo um array de permissões do usuário em si
            $arrPermsUs = $this->objTrabalho->obterPermissoesUsuario($arrDadosDB['id']);

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }
        //------------------------

        $arrayArgs = []; //Início do escopo da variável

        //Argumentos padrões
        $arrayArgs = [
            'tituloPagina' => _NOME_SIS_." - Admin / Editar Permissões Usuário",
            'auth' =>          $arrInfsUsLogado
        ];

        //Obtendo dados da requisição
        $arrayReq = [
            'id' =>      $arrDadosDB['id'],
            'usuario' => $arrDadosDB['usuario'],
            'strCatPerms' => $this->getValorParmRequest("strCatPerms") ?? "",
            'basePermsACL' => $this->getValorParmRequest("regBase") ?? ""
        ];

        //Array de permissões processadas vindas da requisição
        $arrPersReqProc = $this->_processarStrPermsParaArray($arrayReq['strCatPerms']);

        //Em caso de erro no processamento das permissões vindas do processamento.
        if($arrPersReqProc === false || ($arrayReq['basePermsACL'] != 'permitir' && $arrayReq['basePermsACL'] != 'negar')){
            
            //Emitindo erro -------------------------------------------------------
            
            $arrayReq['basePermsACL'] = $arrDadosDB['basePermsACL'];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Falha na operação! Não foi possível processar os valores enviados. Tente mais tarde por gentileza.",
                'parms'=> $arrayReq,
                'arrTodasPerms' => $arrTodasPerms,
                'arrPermsUs' =>    $arrPermsUs //Array de permissões do usuário.
            ];

            //Emitindo o erro.
            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
            exit;

            //------------------------------------------------------------------------
        }

        //Inciando o processo de fato realizar a operação final
        try {
            
            //Tentando realizar operação
            if($arrayReq['basePermsACL'] === 'negar'){
                $this->objTrabalho->definirPermissoesUsuario_basePadraoNEGAR($arrDadosDB['id'], $arrPersReqProc);
            } else {
                $this->objTrabalho->definirPermissoesUsuario_basePadraoPERMITIR($arrDadosDB['id'], $arrPersReqProc);
            }

            //print_r();

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "As permissões foram atualizadas com sucesso!",
                'parms'=> $arrayReq,
                'arrTodasPerms' => $arrTodasPerms,
                'arrPermsUs' =>    $this->_converterArrayPermsSimples_ParaComposto($arrPersReqProc) //Array de permissões da requisição.
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);


            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorre uma falha interna na operação! Tente mais tarde por gentileza. #DB0001",
                'parms'=> $arrayReq,
                'arrTodasPerms' => $arrTodasPerms,
                'arrPermsUs' =>    $this->_converterArrayPermsSimples_ParaComposto($arrPersReqProc) //Array de permissões da requisição.
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
            

        } catch (\Exception $e) { //Erro no procedimento.

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq,
                'arrTodasPerms' => $arrTodasPerms,
                'arrPermsUs' =>    $this->_converterArrayPermsSimples_ParaComposto($arrPersReqProc) //Array de permissões da requisição.
            ];

            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        }        

    }

}

?>