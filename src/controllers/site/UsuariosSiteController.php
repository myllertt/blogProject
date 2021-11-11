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

        $results = [
            'procAtv' => false, //Indica quando o processo esta sendo realizado
            'sts' => null,
            'msg' => "",
            'parms'=> $arrayParmsFormNulos
        ];
        
        Views::abrir("site.us.registrar", $results);

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

            //Enviando mensagem de sucesso!
            $results = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => true,
                'msgE' => "Seu usuário foi cadastrado com sucesso!",
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.registrar");
        
        } catch(DBException $e){ //Em caso de erro de banco de dados.
            
            //$e->debug();
            //Views::abrir("errosGerais.ErroDB");

            $results = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msgE' => "Desculpe! Ocorre uma falha interna na operação! Tente mais tarde por gentileza. #DB0001",
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.registrar", $results);
            

        } catch (\Exception $e) { //Erro no procedimento.

            $results = [
                'procAtv' => true, //Indica quando o processo esta sendo realizado
                'sts' => false,
                'msgE' => $e->getMessage(),
                'parms'=> $arrayReq
            ];

            Views::abrir("site.us.registrar", $results);
        }        

    }

    /*
    public function getPost(){

        $id = $this->getValorParmViaRota(0);

        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }            

        try {
            
            $arrayDadosPost = $this->objPost->obterPostAtivo( $id );
            
            $results = (object) [
                'haRegistro' => !empty($arrayDadosPost) ?? false,
                'reg' => $arrayDadosPost, //Registros
            ];
            
            //Chamando view  de post
            Views::abrir("site.posts.post", ['results' => $results]);
        

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir("errosGerais.ErroDB");
        }
        
    }
    */


}

?>