<?php

//classe singleton

namespace Sistema\Views {

    class Views {

        //Array de views associações de seus arquivos respectivos
        private static $arrayArqInclusoes = [];

        //Considera o momento em que as definições de VIEWS podem ser feitas. Evitando que configurações de view sejam modificadas secundáriamente
        private static $stsModifAtivas = true; 

        //Padronização de erro.
        private static function throwErro(string $nomeMetodoAtual, string $msgErro, int $codErro = null) : void { //Throw VwException
            throw new VwException($nomeMetodoAtual." ==> ".$msgErro, $codErro);
        }

        //Métodos de definições ----------------------------------------

        public static function definir(string $idView, string $strCaminhoInclusao) : void{ //Throw VwException
            
            //Verificando se a janela de modificações ainda esta aberta
            if(!self::$stsModifAtivas)            
                self::throwErro(__METHOD__, "Operação não permitida!", 1001);

            if($idView == "")
                self::throwErro(__METHOD__, "A idView é inválida!", 1002);

            if(strlen($idView) > 128)
                self::throwErro(__METHOD__, "A idView é muito longa!", 1529);


            //Evitando duplicidade de idView
            if(isset(self::$arrayArqInclusoes[ $idView ]))
                self::throwErro(__METHOD__, "Erro já existe uma idView configurada para (".$idView.")", 9852);
            

            //Verificando o caminho de inclusão.
            if($strCaminhoInclusao == "")
                self::throwErro(__METHOD__, "Defina um caminho de inclusão", 2984);
            
                
            //Finalmente inserindo configuração no array
            self::$arrayArqInclusoes[ $idView ] = [
                'id' =>         $idView,
                'linkInc' =>    $strCaminhoInclusao
            ];

        }

        //Utilizado quando se quer finalizar as modificações
        public static function finalizarModificacoes() : void{
            self::$stsModifAtivas = false;
        } 

        //Métodos de execuções -----------------------------------------

        public static function abrir(string $idView, array $arrayArgs = null){

            //Conferindo a existencia do ID VIEW
            if(!isset(self::$arrayArqInclusoes[ $idView ])){
                self::throwErro(__METHOD__, "Erro! a idView (".$idView.") não existe!", 5597);
            }

            //Extraindo variáveis dos argumentos e as tornando disponíveis no escopo da view.
            if($arrayArgs !== null){
                extract($arrayArgs, EXTR_SKIP); //Neste caso se colidir com alguma variável a mesma não será passada a diante
            }
            
            //Tentando incluir o arquivo de view        
            if(!@include_once(self::$arrayArqInclusoes[ $idView ]['linkInc'])){
                self::throwErro(__METHOD__, "Erro! na idView (".$idView.") não foi localizado o caminho de inclusão (".self::$arrayArqInclusoes[ $idView ]['linkInc'].")", 5597);
            }
          

        }

    }

    //Necessário para diferenciar os erros específicos dos demais erros
    class VwException extends \Exception{
	
        public function __construct(string $msgPublica=null, int $codigo=null, $previous = null) {	
            
            parent::__construct($msgPublica, $codigo, $previous);
            
        }
    }
}



?>