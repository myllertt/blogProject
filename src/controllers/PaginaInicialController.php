<?php

use Sistema\Views\Views; 

class PaginaInicialController extends Controlador{

    
    function __construct($objRequest){
        parent::__construct($objRequest);
    }

    public function inicio(){

        //Teste passagem de argumentos para a view
        $testeArgumentoView = [1,2,3,4];

        //Chamando view 
        Views::abrir("home.index", ['testeArgumentoView' => $testeArgumentoView]);
        
    }


}

?>