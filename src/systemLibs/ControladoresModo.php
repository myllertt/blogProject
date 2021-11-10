<?php

use Sistema\ProcessamentoRotas\Request;

class Controlador {

    //Objeto responsável por transferir os parâmetros úteis da rota para o controlador.
    protected $objRequestRota;

    function __construct(Request $objRequestRota){
        $this->objRequestRota = $objRequestRota;
    }

    //Obtém o argumento passado no momento que a rota esta sendo definida inicialmente
    public function getArgRawControlador(){
        return $this->objRequestRota->argRawControlador;
    }

    #---- Parâmetros via rota --------

    //Obtém o valor do parâmetro enviado pelo cliente através da rota (request).
    public function getValorParmViaRota(int $index){

        if(!array_key_exists($index, $this->objRequestRota->argsViaRota))
            return null;

        return $this->objRequestRota->argsViaRota[ $index ]['val'];
    }

    //Obtém o nome do parâmetro enviado pelo cliente através do link da rota.
    public function getNomeParmViaRota(int $index){

        if(!array_key_exists($index, $this->objRequestRota->argsViaRota))
            return null;

        return $this->objRequestRota->argsViaRota[ $index ]['tag'];
    }

    #---- Parâmetros via link --------
    public function getValorParmViaLink(string $index){

        if(isset($this->objRequestRota->argsViaLink[ $index ]))
            return $this->objRequestRota->argsViaLink[ $index ];

        return null;
    }

    #---- Parâmetros via link --------
    public function getValorParmViaBody(string $index){

        if(isset($this->objRequestRota->argsViaBody[ $index ]))
            return $this->objRequestRota->argsViaBody[ $index ];

        return null;
    }

    //De uma forma geral tentará obter o valor de uma requisição. Seja no Link ou no Body. Dando Prioridade ao Body
    public function getValorParmRequest(string $index){

        //Retornará ou um outro, mas dando prioridade aos parâmetros no body

        return $this->getValorParmViaBody($index) ?? $this->getValorParmViaLink($index);
    
    }

    //Retorno o link requisitado.
    public function getRotaRequest() : string{
        return $this->objRequestRota->strRotaReq;
    }

}

?>