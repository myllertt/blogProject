<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/UsuariosSite.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class UsuariosSiteController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objTrabalho;
    #-----------------------------------------------------

    private function _instanciaObjetos(){

        $this->objTrabalho = new UsuariosSite( DriverConexaoDB::getObjDB() );

    }

    
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();
    }

    public function exibirTelaRegistro(){

        echo "Ação registrar usuário<br>";


        try {
            
            $this->objTrabalho->registrarUsuario("myller2", "Lucas", "Silva Costa", "M", "exemplo@exemplo.com", "1264894");

            exit;
            Views::abrir("site.us.registrar");

        } catch (\Exception $e) { //Erro no procedimento.
            
           echo $e->getMessage();

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            $e->debug();
            //Views::abrir("errosGerais.ErroDB");

            #Acionando view de erro geral do sistema.
            
        }
        

    }

    public function processoRegistrarUsuario(){

        echo "Processo registrar usuário";

        Views::abrir("site.us.registrar");

    }

    /*
    public function getPost(){

        $id = $this->getValorParmViaRota(0);

        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }            

        try {
            
            $arrayDadosPost = $this->objPost->obterPostAtivo( $id );
            
            $results = (object) [
                'haRegistro' => !empty($arrayDadosPost) ?? false,
                'reg' => $arrayDadosPost, //Registros
            ];
            
            //Chamando view  de post
            Views::abrir("site.posts.post", ['results' => $results]);
        

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir("errosGerais.ErroDB");
        }
        
    }
    */


}

?>