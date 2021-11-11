<?php

# Classe utilitária com métodos para manipulação de caracteres.

class TratamentoCaracteres {

    //Converte um formato de data USA (date) em formato (Data) Brasileiro.
    public static function dataSimplesUSA_DataSimplesBR(string $dataSimplesUSA) : string{ # AAAA-MM-DD => MM/DD/AAAA
		
        if(strlen($dataSimplesUSA) != 10)
            return "?";

        return substr($dataSimplesUSA, 8,2)."/".substr($dataSimplesUSA, 5,2)."/".substr($dataSimplesUSA, 0,4);
	
    }

    //Converte um formato de dataTime USA (dateTime) em formato (data e hora) Brasileiro.
    public static function dateTimeUSA_DataHoraBR(string $dateTime) : string{ # AAAA-MM-DD 00:00:00 => DD/MM/AAAA 00:00:00
		
        if(strlen($dateTime) != 19)
            return "?";

        return substr($dateTime, 8,2)."/".substr($dateTime, 5,2)."/".substr($dateTime, 0,4)." ".substr($dateTime, 11, 19);
	
    }

    public static function removerHoraDataTime(string $dataTime) : string{
        return substr($dataTime, 0, 10);
    }

    public static function gerarAbreviacaoNome(string $nome, string $sobrenome) : string {
        
        if($sobrenome != ""){
            $expSobren = explode(" ", $sobrenome);
            if(count($expSobren) > 0){
                return $nome." ".$expSobren[0];
            }
        }

        return $nome;
    }


}


?>