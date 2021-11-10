<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/Posts.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class PaginaInicialController extends Controlador{

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

        /*
        //Teste passagem de argumentos para a view
        $testeArgumentoView = [1,2,3,4];

        var_dump($this->getValorParmRequest("valor"));

        var_dump($this->getRotaRequest());
        */

        try {

            print_r($this->objPost->obterResumoPostsAtivos());

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            $e->debug();
        }

        exit;

        //Chamando view 
        Views::abrir("home.index", ['testeArgumentoView' => $testeArgumentoView]);
        
    }


}

?>