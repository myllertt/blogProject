<?php

# Inclusão models úteis para este controlador ------------------
require(__DIR_MODELS__."/Posts.php");
//--------------------------------------------------------------

use Sistema\Views\Views;
use Sistema\DB\Exceptions\DBException; 
use Sistema\Rotas;

class PaginaInicialController extends Controlador{

    # --Objetos de models quando necessário instanciar ---
    private $objPost;
    #-----------------------------------------------------

    # Atributos específicos
    private $CFG_idViewPadraoTrabalho = "site.index"; //id da view padrão de trabalho deste controlador

    private function _instanciaObjetos(){

        $this->objPost = new Posts( DriverConexaoDB::getObjDB() );

    }

    
    function __construct($objRequest){
        parent::__construct($objRequest);

        $this->_instanciaObjetos();
    }

    public function redirecionarHome(){
    
        header("Location: ".Rotas::gerarLink("site.home"));

        exit;
    }

    public function inicio(){

        #Id view específica deste método
        $strIdViewEspecMetodo = $this->CFG_idViewPadraoTrabalho;

        $paginaAtual = $this->getValorParmViaRota(0);

        if(!isset($paginaAtual)){
            $paginaAtual = 1; //Página 1
        } else {
            $paginaAtual = (int) $paginaAtual;
        }            

        try {

            $arrayPosts = $this->objPost->obterResumoPostsAtivos( $paginaAtual );

            //Argumentos padrões do sistema.
            $arrayArgs = [

                'tituloPagina' => _NOME_SIS_." - Bem-vindo(a)"
            ];

            $arrayArgs['results'] = (object) [
                'haRegistros' => !empty($arrayPosts['regs']) ?? false,
                'regs' => $arrayPosts['regs'], //Registros
                'pagina' => $arrayPosts['pagina'] //Registros
            ];
            
            //Chamando view 
            Views::abrir($strIdViewEspecMetodo, $arrayArgs);

        } catch(DBException $e){ //Em caso de erro de banco de dados.
            //$e->debug();

            #Acionando view de erro geral do sistema.
            Views::abrir(_ID_VIEW_GERAL_ERRODB_);
        }

    }

    /**
     * Este método tem o objetivo de apenas informar se o sistema esta operando normalmente. Mas especificadamento o processamento de rotas.
     *
     * @return void
     */
    public function informativoTesteSistema() : void{

        echo "O sistema está funcinando normalmente!<br>";
        echo "Teste de conexão com o banco de dados: ";

        $objMysqli = DriverConexaoDB::getObjDB();
        if($objMysqli !== false){
            echo "[OK]";
        } else {
            echo "[FALHA]";
        }

    }

}

?>