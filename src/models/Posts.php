<?php

use Sistema\DB\Exceptions\DBException;

//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class Posts {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.

    private function _definirConfigsEspecificas(){
        $this->arrayCFGSEsp['MAX_RES_PAG'] = 2; //Máximo de POSTS por página home
        $this->arrayCFGSEsp['MAX_CARC_PREVIA_POSTS'] = 128; //Máximo de caracs para a prévia dos posts
        $this->arrayCFGSEsp['MAX_AVANC_PAGS'] = 50; //Máximo avanço páginas.
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


    public function obterResumoPostsAtivos(int $pagina = 1) : array{ # throw DBException;

        //Verificando a paginação.
        if($pagina < 1){
            $pagina = 1;
        } else {

            //Limite de avanço de páginas
            if($pagina > $this->arrayCFGSEsp['MAX_AVANC_PAGS'])
                $pagina = $this->arrayCFGSEsp['MAX_AVANC_PAGS'];
        }

        //Cursos da demarcação da página
        $cursorPagina = ($pagina -1) * $this->arrayCFGSEsp['MAX_RES_PAG'];
            

        //Verificação do objeto do banco de dados
        $this->_verificarObjDB();
        
        $strSql = "
            SELECT
                P.id,
                P.titulo,
                SUBSTR(P.conteudo, 1, ".$this->arrayCFGSEsp['MAX_CARC_PREVIA_POSTS'].") as previa,
                P.dataCad,
                P.dataAt,
                USAD.id AS idUs,
                USAD.nome AS nomeUS,
                USAD.sobrenome AS sobreNomeUS
            FROM
                "._TAB_Posts_." P,
                "._TAB_UsAdmin_." USAD
            WHERE
                P.status = '1'
                AND P.idUsuario = USAD.id
            ORDER BY
                dataCad DESC
            LIMIT ".$cursorPagina.", ".$this->arrayCFGSEsp['MAX_RES_PAG']."
        ";

        //Array final de retorno
        $arrayRetornoFinal = [];

        $result = @ $this->objMysqli->query($strSql);
        if($result){
            
            while($row = $result->fetch_assoc()){
                //Coletando elementos

                //Adicionando "..." no final se necessário
                if($this->arrayCFGSEsp['MAX_CARC_PREVIA_POSTS'] == strlen($row['previa']))
                    $row['previa'] .= "...";

                //Adicionado conversão Brasileira. (dataCad)
                if($row['dataCad'] !== NULL){
                    $row['dataCadBR'] = TratamentoCaracteres::dataSimplesUSA_DataSimplesBR( TratamentoCaracteres::removerHoraDataTime($row['dataCad']) ); 
                    $row['dataHoraCadBR'] = TratamentoCaracteres::dateTimeUSA_DataHoraBR($row['dataCad']);
                } else {
                    $row['dataCadBR'] = null;
                    $row['dataHoraCadBR'] = null;
                }

                //Adicionado conversão Brasileira. (dataAt)
                if($row['dataAt'] !== NULL){
                    $row['dataAtBR'] = TratamentoCaracteres::dataSimplesUSA_DataSimplesBR( TratamentoCaracteres::removerHoraDataTime($row['dataAt']) ); 
                    $row['dataHoraAt'] = TratamentoCaracteres::dateTimeUSA_DataHoraBR($row['dataAt']);
                } else {
                    $row['dataAtBR'] = null;
                    $row['dataHoraAt'] = null;
                }

                //Gerando abreviação, nome usuário.
                $row['nomeUSAbrev'] = TratamentoCaracteres::gerarAbreviacaoNome($row['nomeUS'], $row['sobreNomeUS']);

                $arrayRetornoFinal[] = $row;

            }

            

        } else { //Encaminhando erro.
            throw new DBException("Desculpe! Ocorreu uma falha interna ao acessar o banco de dados. #834834", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        return [
            'pagina' => $pagina,
            'regs' => $arrayRetornoFinal
        ];

    }

    public function obterPostAtivo(int $id) : array{ # throw DBException;

        //Verificação do objeto do banco de dados
        $this->_verificarObjDB();
        
        $strSql = "
            SELECT
                P.id,
                P.titulo,
                P.conteudo,
                P.dataCad,
                P.dataAt,
                USAD.id AS idUs,
                USAD.nome AS nomeUS,
                USAD.sobrenome AS sobreNomeUS
            FROM
                "._TAB_Posts_." P,
                "._TAB_UsAdmin_." USAD
            WHERE
                P.status = '1'
                AND P.idUsuario = USAD.id           
                AND P.id = ?
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('i', $id);

        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }

        //Somente operacional em queries que retornam valores.
        $objResult = $objStmt->get_result();
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Caso a consulta não possua resultados
        if($objResult->num_rows == 0){
            //Fechando statemment
            $objStmt->close();
            return [];
        }
        
        $arrayRetorno = $objResult->fetch_assoc();
        //Fechando statemment
        $objStmt->close();

        //Tratando valores antes do retorno ------------------

        //Adicionado conversão Brasileira. (dataCad)
        if($arrayRetorno['dataCad'] !== NULL){
            $arrayRetorno['dataCadBR'] = TratamentoCaracteres::dataSimplesUSA_DataSimplesBR( TratamentoCaracteres::removerHoraDataTime($arrayRetorno['dataCad']) ); 
            $arrayRetorno['dataHoraCadBR'] = TratamentoCaracteres::dateTimeUSA_DataHoraBR($arrayRetorno['dataCad']);
        } else {
            $arrayRetorno['dataCadBR'] = null;
            $arrayRetorno['dataHoraCadBR'] = null;
        }

        //Adicionado conversão Brasileira. (dataAt)
        if($arrayRetorno['dataAt'] !== NULL){
            $arrayRetorno['dataAtBR'] = TratamentoCaracteres::dataSimplesUSA_DataSimplesBR( TratamentoCaracteres::removerHoraDataTime($arrayRetorno['dataAt']) ); 
            $arrayRetorno['dataHoraAt'] = TratamentoCaracteres::dateTimeUSA_DataHoraBR($arrayRetorno['dataAt']);
        } else {
            $arrayRetorno['dataAtBR'] = null;
            $arrayRetorno['dataHoraAt'] = null;
        }

        //Gerando abreviação, nome usuário.
        $arrayRetorno['nomeUSAbrev'] = TratamentoCaracteres::gerarAbreviacaoNome($arrayRetorno['nomeUS'], $arrayRetorno['sobreNomeUS']);
        

        return $arrayRetorno;

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