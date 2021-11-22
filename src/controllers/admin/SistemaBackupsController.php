<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/AuthUsuariosAdmin.php");
require(__DIR_MODELS__."/BackupSistemaDB.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class SistemaBackupsController extends Controlador{

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

        $this->objTrabalho = new BackupSistemaDB();

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

        //Verificação das permissões ACL
        $this->_verificarPermissoesACL_AutoRedEExit();
    }

    /**
     * Acesso à tela de backups
     *
     * @return void
     */
    public function telaBackup() : void {

        #Id view específica deste método
        $strIdViewEspecMetodo = "admin.sistema.backups";


        //Obtendo dados do usuário logado
        $arrInfsUsLogado = $this->objAuth->getArrayCacheDadosUsuarioLogado();

        
        //Argumentos padrões do sistema.
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Área Administrativa / Sistema / Backups",
            'auth' =>          $arrInfsUsLogado

        ];

    
        Views::abrir($strIdViewEspecMetodo, $arrayArgs);

    }   

    /**
     * Processo de download do backup
     *
     * @return void
     */
    public function processoBackupDownload() : void{

        try {
            
            //Gerando backup
            $this->objTrabalho->gerarBackup();

            //Gerando header que chama o download
            if($this->objTrabalho->gerarHeaderDownloadBackup()){

                //Descarregando dados do arquivo
                $this->objTrabalho->descarregarDadosBackup();
                
            } else {
                echo "Falha na operação! #Header - 423948";
            }

            exit;

        } catch (\Exception $e) {

            //Por motivos de segurança somente apresentará erros com código (100010001) certificado que é um erro da classe em si.
            if($e->getCode() == 100010001){
                echo $e->getMessage();
            } else {
                echo "Falha na operação! Erro desconhecido!";
            }
        }
    }
}

?>