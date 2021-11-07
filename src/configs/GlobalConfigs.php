<?php



class GBCFGS{

    public static $nomeDirLibsSis = "systemLibs"; //Nome do diretório de bibliotecas do sistema.
    public static $nomeDirRotas = "routes"; //Nome do diretório de rotas
    public static $nomeDirControladores = "controllers"; //Nome do diretório de controladores

}

define("__DIR_RAIZ__", __DIR__."/.."); //Por padrão o "/../" volta o diretório "public"
define("__DIR_CONTROLADORES__", __DIR_RAIZ__."/".GBCFGS::$nomeDirControladores); //Por padrão o "/../" volta o diretório "public"

?>