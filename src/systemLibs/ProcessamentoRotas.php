<?php

namespace Sistema{

    
    class ProcessamentoRotas {

        private $arrayRotasConfigs;

        private function _encaminharErroDefinicoes(String $msg) : void{
            throw new \Exception( get_class($this).": Falha na definição das rotas: \n Inf(".$msg.")" );
        }

        private function _verificarSeEstruturaRotaEValida(string $rota) : bool{

            //Aceita apenas letras(Maiu Minu), numeros, Caracteres (./-_)
            if (preg_match('/^[a-z0-9._\-\/.]+$/i', $rota)) {
                return true;
            } else {
                return false;
            }

        }

        private function _processarParmRotaIndividualmente(String $parm){

            if($parm == "")
                return false;
                
            //Parametro normal
            if($parm[0] != "{"){ 

                $keyAsci = ord($parm[0]);

                //Parâmetro válildo só começa pode começar de (a-z, A-Z, _)
                if(($keyAsci >= 65 && $keyAsci <= 90) || ($keyAsci >= 97 && $keyAsci <= 122) || $keyAsci == 95){

                }

                echo $keyAsci;

            } else {//Parâmetro Dinâmico

            }
            
        }

        private function _processarParametrosRota(String $rota) {

            //Quebrando os parâmetros da rota.
            $expParametros = explode("/", $rota);
            if(empty($expParametros)){
                return false;
            }
            
            //Nestas atribuições a rota precisa de começar no mínimo com uma /, neste caso representado como uma string vazia
            if($expParametros[0] != "")
                return false;

            $procParm;

            foreach ($expParametros as $parametro) {
                
                $procParm = $this->_processarParmRotaIndividualmente($parametro);

                print_r($procParm);

            }

        }

        public function definirMetodoGET(String $rota, String $nomeClasseControlador, String $nomeMetodoChamadoInicial, String $linkInclusao = null) : void {

            //A rota não pode ser em braco
            if($rota == "")
               $this->encaminharErroDefinicoes("A definição da rota está nula. #5343");
            
            //Verificando preliminarmente a consistência da rota.
            if(!$this->_verificarSeEstruturaRotaEValida($rota))
                $this->_encaminharErroDefinicoes("A definição da rota (".$rota.") é inválida. #12452");
            
    

            //Processando parâmetros internos dentro da rota.
            print_r($this->_processarParametrosRota($rota));

        }

    }


}

namespace Sistema\ProcessamentoRotas{

    
    class Request {
        
        public $metodo; 
        public $argsLink; //Argumentos passado pelo link
        public $argsMetodos; //Argumentos passados pelo método.

    }

}


?>