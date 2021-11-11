<?php


//Configurações gerais
class GBCFGS{

    public static $nomeDirLibsSis =         "systemLibs"; //Nome do diretório de bibliotecas do sistema.
    public static $nomeDirRotas =           "routes"; //Nome do diretório de rotas
    public static $nomeDirControladores =   "controllers"; //Nome do diretório de controladores
    public static $nomeDirViews =           "views"; //Nome do diretório de controladores
    public static $nomeDirConfigs =         "configs"; //Nome do diretório das configurações
    public static $nomeDirDataBase =        "database"; //Nome do diretório das database
    public static $nomeDirModels =          "models"; //Nome do diretório das database

}

class GBCFGS_Views{
    public static $srcArqDefinicoesViews; //Caminho que leva até o arquivo de definições das Views e suas respectivas inclusões.   
}

define("__DIR_RAIZ__", __DIR__."/.."); //Por padrão o "/../" volta o diretório "public"

define("__DIR_CONTROLADORES__", __DIR_RAIZ__."/".GBCFGS::$nomeDirControladores); 
define("__DIR_VIEWS__", __DIR_RAIZ__."/".GBCFGS::$nomeDirViews);
define("__DIR_CONFIGS__", __DIR_RAIZ__."/".GBCFGS::$nomeDirConfigs);
define("__DIR_MODELS__", __DIR_RAIZ__."/".GBCFGS::$nomeDirModels);
define("__DIR_SYSLIBS__", __DIR_RAIZ__."/".GBCFGS::$nomeDirLibsSis);

define("__DIR_Exceptions__", __DIR_RAIZ__."/".GBCFGS::$nomeDirLibsSis."/Exceptions");

//Configurando arquivo geral de definições e suas respectivas inclusões das views
GBCFGS_Views::$srcArqDefinicoesViews = __DIR_VIEWS__."/_definicoesViewsInclusoes.php";


# CONFIGURAÇẼOS DE BANCO DE DADOS --------------------------

# Configurações de nomes das tabelas ----
define("_TAB_Posts_", "Posts"); 
define("_TAB_UsAdmin_", "UsuariosAdmin"); 


?>