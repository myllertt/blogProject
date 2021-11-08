<?php

//classe singleton
class Views {

    //Array de views associações de seus arquivos respectivos
    private static $arrayArqInclusoes = [];

    //Considera o momento em que as definições de VIEWS podem ser feitas. Evitando que configurações de view sejam modificadas secundáriamente
    private static $stsModifAtivas = true; 

    //Padronização de erro.
    private static function throwErro(string $nomeMetodoAtual, string $msgErro, int $codErro = null) : void { //Throw Exception
        throw new Exception(self::class."::".$nomeMetodoAtual." ==> ".$msgErro, $codErro);
    }


    public static function definir(string $idView, string $strCaminhoInclusao) : void{ //Throw Exception
        
        //Verificando se a janela de modificações ainda esta aberta
        if(!self::$stsModifAtivas)            
            self::throwErro(_METHOD__, "Operação não permitida!", 1001);

        if(strlen($idView) > 128)
            self::throwErro(_METHOD__, "A idView é muito longa", 1002);

            
    }

    public static function finalizarModificacoes() : void{
        self::$stsModifAtivas = true;
    } 

}


Views::teste();

?>