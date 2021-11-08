<?php

use Sistema\ProcessamentoRotas\Request;

class Controlador {

    //Objeto responsável por transferir os parâmetros úteis da rota para o controlador.
    protected $objRequestRota;

    function __construct(Request $objRequestRota){
        $this->objRequestRota = $objRequestRota;
    }

}

?>