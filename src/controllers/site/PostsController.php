<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/Posts.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 

class PostsController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objPost;
    #-----------------------------------------------------

    # Atributos específicos
    private $CFG_idViewPadraoTrabalho = ""; //id da view padrão de trabalho deste controlador

    /**
     * Instancia de objetos pertinentes à classe
     *
     * @return void
     */
    private function _instanciaObjetos(){

        $this->objPost = new Posts( DriverConexaoDB::getObjDB() );

    }

    /**
     * Construtor
     *
     * @param [\Sistema\ProcessamentoRotas\Request] $objRequest
     */
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();
    }

    /**
     * Método inicial da rota post
     *
     * @return void
     */
    public function inicio() : void{

        header("Location: /home");

        exit;

    }

    /**
     * Tele de exibição de um POST específico
     *
     * @return void
     */
    public function getPost(): void{

        #Id view específica deste método
        $strIdViewEspecMetodo = "site.posts.post";

        $id = $this->getValorParmViaRota(0);

        if(isset($id)){
            $id = (int) $id;
        } else {            
            $id = 0;
        }            
        
        try {
            
            $arrayDadosPost = $this->objPost->obterPostAtivo( $id );

            if(!empty($arrayDadosPost)){
                $tituloPagina = _NOME_SIS_." - ".$arrayDadosPost['titulo'];
            } else {
                $tituloPagina = _NOME_SIS_." - Postagem não encontrada";
            }

            //Argumentos padrões do sistema.            
            $arrayArgs = [

                'tituloPagina' => $tituloPagina,

            ];
            
            $arrayArgs['results'] = (object) [
                'haRegistro' => !empty($arrayDadosPost) ?? false,
                'reg' => $arrayDadosPost, //Registros
            ];
            
            //Chamando view  de post
            Views::abrir($strIdViewEspecMetodo, $arrayArgs);
        

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);
        }
        
    }


}

?>