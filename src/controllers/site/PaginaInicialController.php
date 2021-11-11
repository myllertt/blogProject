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

    public function redirecionarHome(){
        
        header("Location: /home");

        exit;
    }

    public function inicio(){

        $paginaAtual = $this->getValorParmViaRota(0);

        if(!isset($paginaAtual)){
            $paginaAtual = 1; //Página 1
        } else {
            $paginaAtual = (int) $paginaAtual;
        }            


        try {

            $arrayPosts = $this->objPost->obterResumoPostsAtivos( $paginaAtual );

            $results = (object) [
                'haRegistros' => !empty($arrayPosts['regs']) ?? false,
                'regs' => $arrayPosts['regs'], //Registros
                'pagina' => $arrayPosts['pagina'] //Registros
            ];
            
            //Chamando view 
            Views::abrir("site.index", ['results' => $results]);

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir("errosGerais.ErroDB");
        }

    }


}

?>