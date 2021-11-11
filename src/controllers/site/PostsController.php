<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/Posts.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class PostsController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objPost;
    #-----------------------------------------------------

    private function _instanciaObjetos(){

        $this->objPost = new Posts( DriverConexaoDB::getObjDB() );

    }

    
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();
    }

    public function inicio(){

        header("Location: /home");

        exit;

    }

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


}

?>