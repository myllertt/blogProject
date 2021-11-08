<?php

class PaginaInicialController extends Controlador{

    
    function __construct($objRequest){
        parent::__construct($objRequest);
    }

    public function inicio(){
        echo "Teste de controlador da Página Inicial";
    }


}

?>