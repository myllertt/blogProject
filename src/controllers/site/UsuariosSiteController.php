<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/UsuariosSite.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class UsuariosSiteController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objTrabalho;
    #-----------------------------------------------------

    private function _instanciaObjetos(){

        $this->objTrabalho = new UsuariosSite( DriverConexaoDB::getObjDB() );

    }

    
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();
    }

    # Área de registro ------------------------
    public function exibirTelaRegistro(){

        //O formulário neste estado ficará em brando
        $arrayParmsFormNulos = [
            'usuario' => "",
            'nome' => "",
            'sobrenome' => "",
            'genero' => "",
            'email' => "",
            'senha' => ""
        ];

        //Argumentos padrões do sistema.            
        $arrayArgs = [

            'tituloPagina' => _NOME_SIS_." - Registro Novo Usuário",

        ];

        $arrayArgs['results'] = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> $arrayParmsFormNulos
        ];
        
        Views::abrir("site.us.registrar", $arrayArgs);

    }
    public function processoRegistrarUsuario(){

        //Obtendo e armazenando parâmetros da requisição.
        $arrayReq = [

            'usuario' => $this->getValorParmRequest("usu") ?? "",
            'nome' => $this->getValorParmRequest("nme") ?? "",
            'sobrenome' => $this->getValorParmRequest("sno") ?? "",
            'genero' => $this->getValorParmRequest("gen") ?? "",
            'email' => $this->getValorParmRequest("eml") ?? "",
            'senha' => $this->getValorParmRequest("sen") ?? ""

        ];

        try {

            //Tentando registrar usuário
            $this->objTrabalho->registrarUsuario($arrayReq['usuario'], $arrayReq['nome'], $arrayReq['sobrenome'], $arrayReq['genero'], $arrayReq['email'], $arrayReq['senha']);

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Registro Novo Usuário",

            ];

            //Enviando mensagem de sucesso!
            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msg' => "Seu usuário foi cadastrado com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.registrar", $arrayArgs);
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir("errosGerais.ErroDB");

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Registro Novo Usuário",

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => "Desculpe! Ocorre uma falha interna na operação! Tente mais tarde por gentileza. #DB0001",
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.registrar", $arrayArgs);
            

        } catch (\Exception $e) { //Erro no procedimento.

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Registro Novo Usuário",

            ];

            $arrayArgs['results'] = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msg' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.registrar", $arrayArgs);
        }        

    }

}

?>