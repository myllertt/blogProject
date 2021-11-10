<?php

use Sistema\DB\Exceptions\DBException;

class Posts {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.

    private function _definirConfigsEspecificas(){
        $this->arrayCFGSEsp['MAX_RES_PAG'] = 2; //Máximo de POSTS por página home
        $this->arrayCFGSEsp['MAX_CARC_PREVIA_POSTS'] = 128; //Máximo de caracs para a prévia dos posts
    }

    private function _verificarObjDB(){
        if(!$this->objMysqli){
            throw new DBException("Falha ao iniciar a conexão com o banco de dados");            
        }
    }

    function __construct(mysqli $objMysqli){
        $this->objMysqli = $objMysqli;

        $this->_definirConfigsEspecificas();
    }


    public function obterResumoPostsAtivos() : array{ # throw DBException;

        //Verificação do objeto do banco de dados
        $this->_verificarObjDB();
        
        $strSql = "
            SELECT
                P.id,
                P.titulo
                SUBSTR(P.conteudo, 1, ".$this->arrayCFGSEsp['MAX_CARC_PREVIA_POSTS'].") as previa,
                P.dataCad,
                P.dataAt
                USAD.id AS idUs
                USAD.nome AS nomeUS,
                USAD.sobrenome AS sobreNomeUS
            FROM
                "._TAB_Posts_." P
                "._TAB_UsAdmin_." USAD
            WHERE
                P.status = '1'
                AND P.idUsuario = USAD.id
        ";

        //Array final de retorno
        $arrayRetornoFinal = [];

        $result = @ $this->objMysqli->query($strSql);
        if($result){
            
            while($row = $result->fetch_assoc()){
                print_r($row);

                //Coletando elementos
                $arrayRetornoFinal[] = $row;

            }

            

        } else { //Encaminhando erro.
            throw new DBException("Desculpe! Ocorreu uma falha interna ao acessar o banco de dados. #834834", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        return $arrayRetornoFinal;

    }

    
    
    #GETTERS
    public function getObjMysqli(){
        return $this->objMysqli;
    }
    
    #SETTERS
    public function setObjMysqli($objMysqli){
        $this->objMysqli = $objMysqli;
    }
    

}

?>