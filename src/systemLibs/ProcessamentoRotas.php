<?php

namespace Sistema{

    
    class ProcessamentoRotas {

        private $arrayRotasConfigs;

        private function _encaminharErroDefinicoes(String $msg) : void{
            throw new \Exception( get_class($this).": Falha na definição das rotas: \n Inf(".$msg.")" );
        }

        private function _verificarSeEstruturaRotaEValida(string $rota) : bool{

            //Aceita apenas letras(Maiu Minu), numeros, Caracteres (./-_{})
            if (preg_match('/^[a-z0-9._\-\/.{}]+$/i', $rota)) {
                return true;
            } else {
                return false;
            }

        }

        /**
         * Função responsável por processar o parâmetro de uma rota individualmente e descobrir se ele é um parâmetro normal ou dinâmico. 
         *
         * @param String $parm
         * @return true|string|false - True: Condição de sucesso de um parâmetro normal, string: Condição de sucesso de um parâmetro dinâmica, e este caso será retornado o nome do parâmetro. false: Neste caso indica um erro ao processar o parâmetro
         */
        private function _processarParmRotaIndividualmente(String $parm){

            /*
                Ao chegar neste ponto subentende que os caractéres básicos de um parâmetro já foram validados pelas funções anteriores
            */

            if($parm == "")
                return false;
                
            //Detectando Parametro normal
            if($parm[0] != "{"){ 

                $keyAsci = ord($parm[0]);

                //Parâmetro válildo só começa pode começar de (a-z, A-Z, _)
                if(($keyAsci >= 65 && $keyAsci <= 90) || ($keyAsci >= 97 && $keyAsci <= 122) || $keyAsci == 95){
                    return true;
                }

            } else {//Detectando Parâmetro Dinâmico

                $lenParm = strlen($parm);

                // O mínimo para que se forme um parâmetro dinâmico é que se contenha pelo menos 3 carc. Ex: {p}
                if($lenParm < 3)
                    return false;

                //Obtendo a primeira letra da variável
                $keyAsci = ord($parm[1]);

                //Parâmetro válildo só começa pode começar de (a-z, A-Z, _)
                if(($keyAsci >= 65 && $keyAsci <= 90) || ($keyAsci >= 97 && $keyAsci <= 122) || $keyAsci == 95){
                    
                    $ultimoChar = $parm[ $lenParm-1 ];

                    //Um parâmetro variável só pode terminar com }
                    if($ultimoChar == "}"){
                        
                        //Recortando variável para ser retornada o que também indica um sucesso
                        return substr($parm, 1, $lenParm-2);

                    }

                }

                return false;

            }
            
        }

        private function _processarParametrosRota(String $rota) {
            
            if(strlen($rota) <= 0)
                return false;
            
            //Toda rota precisa começar com "/" indicando a raiz
            if($rota[0] != '/')
                return false;

            //Removendo o primeiro caracter para que o método explode fique correto.
            $rota = substr($rota, 1); 

            //Array de retorno padrão raiz
            $arrFinalRetorno = [
                'strRota' => $rota,
                'arrPrmOrdem' => [] //Array que representa a ordem e tipo dos parâmetros da rota.
            ];

            //Significa que foi encontrada uma rota raiz (/)
            if($rota == ""){
                $arrFinalRetorno['strRota'] = '/';
                return $arrFinalRetorno;
            }
                
            //Quebrando os parâmetros da rota.
            $expParametros = explode("/", $rota);
            if(empty($expParametros)){
                return false;
            }
            

            //Auxiliar do processamento de parâmetros
            $procParm;

            foreach ($expParametros as $parametro) {

                //Detecção de duplicidade de barras(//)
                if($parametro == ""){
                    return false;       
                }
                
                $procParm = $this->_processarParmRotaIndividualmente($parametro);

                //Erro detectado no processamento individual
                if($procParm === false)
                    return false;

                //Verificando se é um parâmetro normal
                if($procParm === true){

                    $arrFinalRetorno['arrPrmOrdem'][] = [
                        'nm'    => $parametro, //Nome do parâmetro
                        't'     => 'n' //Tipo: Normal
                    ];

                } else { //Verificando se é um parâmetro dinâmico
                    $arrFinalRetorno['arrPrmOrdem'][] = [
                        'nm'    => $procParm, //Nome do parâmetro
                        't'     => 'd' //Tipo: Dinâmico
                    ];
                }
            }

            return $arrFinalRetorno;

        }

        public function _definirMetodo(string $metodo, String $rota, String $nomeClasseControlador, String $nomeMetodoChamadoInicial, String $linkInclusao = null) : void {

            //A rota não pode ser em braco
            if($rota == "")
               $this->_encaminharErroDefinicoes("A definição da rota está nula. #5343");
            
            //Verificando preliminarmente a consistência da rota.
            if(!$this->_verificarSeEstruturaRotaEValida($rota))
                $this->_encaminharErroDefinicoes("A definição da rota (".$rota.") é inválida. #12452");
            
    
            //Processando parâmetros internos dentro da rota.
            $retOper = $this->_processarParametrosRota($rota);

            print_r($retOper);

            echo $rota;

        }

        public function definirMetodoGET(String $rota, String $nomeClasseControlador, String $nomeMetodoChamadoInicial, String $linkInclusao = null) : void {

            $this->_definirMetodo("GET", $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $linkInclusao);

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