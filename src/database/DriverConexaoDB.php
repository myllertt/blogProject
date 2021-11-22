<?php

//A classe configs/GlobalConfigs.php precisa estar

//Requerindo classe que contém os parâmetros de conexão.
require_once(__DIR_CONFIGS__."/DBConeConfigs.php");


//Classe utilizada para auxiliar o processo de conexão com o banco de dados de todo o sistema.
class DriverConexaoDB {

    private static $conectado = false; //Certificar se a conexão já foi iniciada.
    private static $objMysqli; //objeto de conexão.

    private static $cacheParmsCon; //Cache de parâmetros de conexão.

    private static $historicoUltMsgErro = ""; //Histórico de última msg de erro;
    private static $historicoUltCodErro = 0; //Histórico de último codigo de erro;

    
    private static function iniciarConexao() : bool{

        self::$conectado = false;

        self::$objMysqli = mysqli_init();

        if(!self::$objMysqli)
            return false;

        //Isenta erros.
        if (!@self::$objMysqli->real_connect(self::$cacheParmsCon['h'].":".self::$cacheParmsCon['p'], self::$cacheParmsCon['us'], self::$cacheParmsCon['ps'], self::$cacheParmsCon['db'])) {
           
            if (self::$objMysqli->connect_errno)
                self::$historicoUltCodErro = self::$objMysqli->connect_errno;       

            if(self::$objMysqli->connect_error)
                self::$historicoUltMsgErro = self::$objMysqli->connect_error;
            
            return false;
        }

        //Limpando registros de erros
        self::$historicoUltCodErro = 0;
        self::$historicoUltMsgErro = "";   

        self::$conectado = true;

        return true;

    }

    //Não significa iniciar conexão,mas sim a classe em si.
    public static function iniciar(): void{
        
        //carregando atributos das configurações do db
        self::$cacheParmsCon = [
            'h' => DBConeConfigs::$host,
            'p' => DBConeConfigs::$port,
            'us' => DBConeConfigs::$user,
            'ps' => DBConeConfigs::$password,
            'db' => DBConeConfigs::$DBName
        ];

        //Zerando atributos críticos das configurações públicas
        /* Ate o momento precisou ser removido pois estava impedido o backup do DB via menu do sistema
        DBConeConfigs::$host = null;
        DBConeConfigs::$port = null;
        DBConeConfigs::$user = null;
        DBConeConfigs::$password = null;
        DBConeConfigs::$DBName = null;
        */
    }


    //Obtém o objeto de conexão do banco de dados, ele tentará conectar caso não esteja conectado.
    public static function getObjDB(){

        if(self::$conectado){
            return self::$objMysqli;
        } else {
            if(self::iniciarConexao()){
                return self::$objMysqli;
            } else {
                return false;
            }
        }

    }

    //Retorna a última msg erro da tentativa de conexão ao banco de dados.
    public static function getMsgDBErro() : string{
        return self::$historicoUltMsgErro;
    }

    //Retorna o último cod erro da tentativa de conexão ao banco de dados.
    public static function getCodDBErro() : int{
        return self::$historicoUltCodErro;
    }

    //Obter nome do banco de dados configurado
    public static function getNomeDB() : string{
        return self::$cacheParmsCon['db'];
    }

}
//Apenas preparando a classe. 
DriverConexaoDB::iniciar();

?>