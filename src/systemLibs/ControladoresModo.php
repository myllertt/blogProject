<?php

use Sistema\ProcessamentoRotas\Request;

class Controlador {

    //Objeto responsável por transferir os parâmetros úteis da rota para o controlador.
    protected $objRequestRota;

    function __construct(Request $objRequestRota){
        $this->objRequestRota = $objRequestRota;
    }

    //Obtém o argumento passado no momento que a rota esta sendo definida
    public function getArgPassadoDefinicaoRota(){
        return $this->objRequestRota->argRotaRAW;
    }

    //Obtém o valor do parâmetro enviado pelo cliente através do link da rota.
    public function getValorParmDinamicoLinkRota(int $index){

        if(!array_key_exists($index, $this->objRequestRota->argsLink))
            return null;

        return $this->objRequestRota->argsLink[ $index ]['val'];
    }

    //Obtém o nome do parâmetro enviado pelo cliente através do link da rota.
    public function getNomeParmDinamicoLinkRota(int $index){

        if(!array_key_exists($index, $this->objRequestRota->argsLink))
            return null;

        return $this->objRequestRota->argsLink[ $index ]['tag'];
    }

}

?>