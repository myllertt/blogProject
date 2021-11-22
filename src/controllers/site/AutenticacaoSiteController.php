<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosSite.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class AutenticacaoSiteController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objTrabalho;
    #-----------------------------------------------------

    # Atributos específicos
    private $CFG_idViewPadraoTrabalho = "site.us.login"; //id da view padrão de trabalho deste controlador

    /**
     * Caso perceba alguma sessão ativa do tipo, a página será redirecionada
     *
     * @return void
     */
    private function _autoRedSessaoAtiva_AutoExit() : void{

        //Realizando direc. caso encontre sessão ativa.
        if($this->objTrabalho->checkSeExisteSessaoAtivaTipica()){
            header("Location: ".\Sistema\Rotas::gerarLink(_ROTA_SITE_AREAUS_));
            exit;
        }
        
    }

    /**
     * Instancia de objetos pertinentes à classe
     *
     * @return void
     */
    private function _instanciaObjetos(){

        $this->objTrabalho = new AuthUsuariosSite( DriverConexaoDB::getObjDB() );

    }

    /**
     * Construtor
     *
     * @param [\Sistema\ProcessamentoRotas\Request] $objRequest
     */
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();
    }

    # Área de Login ------------------------
    /**
     * Exibição de tela de login
     *
     * @return void
     */
    public function telaLogin() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = $this->CFG_idViewPadraoTrabalho;


        //Verificando se já não existe sessão ativa
        $this->_autoRedSessaoAtiva_AutoExit();

        //Passando dados em branco
        $arrayReq = [
            'usuario' => "",
        ];

        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Login",

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
     * Processo de login
     *
     * @return void
     */
    public function processoLogin() : void{

        #Id view específica deste método
        $strIdViewEspecMetodo = $this->CFG_idViewPadraoTrabalho;

        //Obtendo e armazenando parâmetros da requisição.
        $arrayReq = [
            'usuario' => $this->getValorParmRequest("usu") ?? "",
            'senha' => $this->getValorParmRequest("sen") ?? ""
        ];

        try {

            //Efetuando login
            $this->objTrabalho->efetuarLogin($arrayReq['usuario'], $arrayReq['senha']);

            //Argumentos padrões do sistema.
            /*
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Login",

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "",
                'parms'=> $arrayReq
            ];
            */
            //Login bem sussedido. Redirecionando para a próxima pagina.
            header("Location: ".\Sistema\Rotas::gerarLink(_ROTA_SITE_AREAUS_));

            exit;
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir(_ID_VIEW_GERAL_ERRODB_);

            //Elimiando esta informação
            unset($arrayReq['senha']);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Login",

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorre uma falha interna na operação! Tente mais tarde por gentileza. #DB0001",
                'parms'=> $arrayReq
            ];
            
            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
            

        } catch (\Exception $e) { //Erro no procedimento.

            //Elimiando esta informação
            unset($arrayReq['senha']);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Login",

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

    #Logout de usuário --------------------
    /**
     * Processo de logout
     *
     * @return void
     */
    public function logout() : void{

         
        try {
            
            //Finalizando sessão
            $this->objTrabalho->realizarLogout();

            //Redirecionando para página de login
            header("Location: ".\Sistema\Rotas::gerarLink(_ROTA_SITE_LOGIN_));
          

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            $e->debug();

            # Lançando erro geral de banco de dados
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);    

            exit;
        }

    }

}

?>