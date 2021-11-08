<?php

use Sistema\Views\Views; 

class PaginaInicialController extends Controlador{

    
    function __construct($objRequest){
        parent::__construct($objRequest);
    }

    public function inicio(){
        
        //Chamando view 
        Views::abrir("home.index");
        
    }


}

?>