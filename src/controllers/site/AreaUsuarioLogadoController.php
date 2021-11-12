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

    //Verifica se o sessão do usuário esta essencialmente ativa e operacional
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

    private function _instanciaObjetos(){

        $this->objAuth = new AuthUsuariosSite( DriverConexaoDB::getObjDB() );

        $this->objUsuariosSite = new UsuariosSite( DriverConexaoDB::getObjDB() );

    }

    //Emissão de erro inesperado
    private function _emitirViewErroInesperado_EXIT(){
        
        //Emitindo erro inesperado
        Views::abrir("errosGerais.ErroInesperado");
        exit;

    }

    
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();

        //Certifica de que tudo que chegue nesta classe precise estar logado, independente do método acionado
        $this->_verificarSessaoAtivaDB_AutoRedEExit();
    }

    # Tela de Edição do Cadastro Usuário ------------------------
    public function telaEditarCadastro(){

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

        //Passando dados em branco
        $arrayReq = [
            'nome' =>       $arrDadosUs['nome'],
            'sobrenome' =>  $arrDadosUs['sobrenome'],
            'genero' =>     $arrDadosUs['genero'],
            'email' =>      $arrDadosUs['email'],
        ];
        
        $results = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> $arrayReq
        ];

 
        Views::abrir("site.us.areaUsuario.editCad", ['auth'=> $arrInfsUsLogado, 'results' => $results]);

    }

    //Processo de edição do cadastro
    public function processoEditarCadastro(){
        echo "aki";
    }

    

}

?>