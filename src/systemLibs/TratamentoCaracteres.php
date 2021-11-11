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


    //Verifica os caracteres padrão de um usuário
    public static function verifCaracteresPadraoUsuario(string $string) : bool{

        if($string == "")
            return false;

        //Um usuário tem que começar com letra.
        $codAsci = ord($string[0]);
        if(!(($codAsci >= 65 && $codAsci <= 90) || ($codAsci >= 97 && $codAsci <= 122)))
            return false;

        //Aceita apenas letras(Maiu Minu), numeros, Caracteres (_)
        if (preg_match('/^[a-z0-9_]+$/i', $string)) {
            return true;
        } else {
            return false;
        }
    
    }

    //Se encontrar caractéres não permitidos irá retornar true.
    public static function verifSeTemCaracNaoPermitidosCampoSenha($string) : bool{
        
        $arrNp = [' ', "\\", "\n", "\r", "\t"];
        if(str_replace($arrNp, "", $string) != $string)
            return true;

        return false;
    }

    //Remover carateres de passíveis tags html
    public static function rmvCharTags($string) : string{
        
        $arrChr = ['<', '>', "\\"];
        return str_replace($arrChr, "", $string);
    }


}


?>