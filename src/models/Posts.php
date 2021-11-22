<?php

use Sistema\DB\Exceptions\DBException;

//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class Posts {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.

    /**
     * Método definição de configurações específicas da classe
     *
     * @return void
     */
    private function _definirConfigsEspecificas(){
        $this->arrayCFGSEsp['MAX_RES_PAG'] = 2; //Máximo de POSTS por página home
        $this->arrayCFGSEsp['MAX_CARC_PREVIA_POSTS'] = 128; //Máximo de caracs para a prévia dos posts
        $this->arrayCFGSEsp['MAX_AVANC_PAGS'] = 50; //Máximo avanço páginas.

        #admin
        $this->arrayCFGSEsp['MAX_POSTS_PAG_ADM'] = 10; //Máximo de posts mostrados por lista administrativa
        $this->arrayCFGSEsp['MAX_AVANC_PAG_ADM'] = 100; //Máximo de avanço de páginas por lista administrativa

        $this->arrayCFGSEsp['MAX_CARC_CONTEUDO_POST'] = 16384; // (16384 = 16kb) Quantidade máxima de caracteres que o conteúdo de uma postagem pode ter.
        $this->arrayCFGSEsp['MAX_CARC_TITULO_POST'] = 128; //Quantidade máxima de caracteres que o título de uma postagem pode ter.
    }

    /**
     * Verifica se objeto da classe $this->objMysqli existe
     *
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     */
    private function _verificarObjDB(){
        if(!$this->objMysqli){
            throw new DBException("Falha ao iniciar a conexão com o banco de dados");            
        }
    }

    /**
     * Realiza a validação dos dados fundamentais de uma postagem
     *
     * @param string $status
     * @param string $titulo
     * @param string $conteudo
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    private function _validarDadosPostagem(string $status, string $titulo, string $conteudo) : void{ #throw \Exception

        #status----------
        $arrSts = ['1', '0']; //Array de possíveis valores para status
        if(!in_array($status, $arrSts, true))
            throw new \Exception("Erro! O campo status é inválido", 1100);


        #título----------
        if($titulo == "")
            throw new \Exception("Informe o título da postagem", 1006);

        $len = strlen($titulo);

        if($len < 3)
            throw new \Exception("Erro! O título da postagem é curto!", 1007);

        if($len > $this->arrayCFGSEsp['MAX_CARC_TITULO_POST'])
            throw new \Exception("Erro! O título da postagem é muito longo!", 1008);


        #conteúdo----------
        if($conteudo == "")
            throw new \Exception("Informe o conteúdo da postagem", 1006);

        $len = strlen($conteudo);

        if($len < 3)
            throw new \Exception("Erro! O conteúdo da postagem é curto!", 1007);

        if($len > $this->arrayCFGSEsp['MAX_CARC_CONTEUDO_POST'])
            throw new \Exception("Erro! O conteúdo da postagem é muito longo!", 1008);

    }

    /**
     * Insere uma nova postagem no banco de dados.
     *
     * @param array $arrayDados
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     */
    private function _inserirPostagemBancoDados(array $arrayDados) : void{ #throw DBException

        /*
            status,
            titulo,
            conteudo,
            idUsuario,        
        */

        $this->_verificarObjDB();

        $strSql = "

            INSERT INTO
                "._TAB_Posts_."
                    (status,titulo,conteudo,idUsuario)
                VALUES
                    (?,?,?,?)
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('sssi', $arrayDados['status'], $arrayDados['titulo'], $arrayDados['conteudo'], $arrayDados['idUsuario']);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }
    }

    /**
     * Atualiza os dados dados de uma postagem no banco de dados.
     *
     * @param integer $idPostagem
     * @param array $arrayDados
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     */
    private function _atualizarDadosPostagemBancoDados(int $idPostagem, array $arrayDados) : void{ #throw DBException

        /*
            'status'
            'titulo'
            'conteudo'     
        */

        $this->_verificarObjDB();

        $strSql = "

            UPDATE
                "._TAB_Posts_."
            SET
                status = ?,
                titulo = ?,
                conteudo = ?,
                dataAt = now()
            WHERE
                id = ?
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('sssi', $arrayDados['status'], $arrayDados['titulo'], $arrayDados['conteudo'],  $idPostagem);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }

    }

    /**
     * Exlcuir do banco de dados uma determinada postagem
     *
     * @param integer $idPostagem
     * @return boolean
     * @throws DBException : Em caso de erro de banco de dados
     */
    private function _excluirPostagemBancoDados(int $idPostagem) : bool { #throw DBException

        $this->_verificarObjDB();

        $strSql = "
            DELETE
            FROM
                "._TAB_Posts_."
            WHERE
                id = ?
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('i', $idPostagem);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }

        //Verificando se os registros foram afetados
        if($this->objMysqli->affected_rows > 0){
            
            $objStmt->close();
            return true;

        } else {
            $objStmt->close();
            return false;
        }

    }

    /**
     * Construtor
     *
     * @param [mysqli] $objMysqli
     */
    function __construct($objMysqli){
        $this->objMysqli = $objMysqli;

        $this->_definirConfigsEspecificas();
    }

    /**
     * Obtém um resumo dos posts ativos
     *
     * @param integer $pagina : Número da página
     * @return array
     * @throws DBException : Em caso de erro de banco de dados
     */
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

    /**
     * Obtém os dados completos de um post ativo
     *
     * @param integer $id
     * @return array
     * @throws DBException : Em caso de erro de banco de dados
     */
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

    #Metodos de características administrativas ---------------

    /**
     * Método utilizado para realizar uma postagem
     *
     * @param integer $idUsuario
     * @param string $status
     * @param string $titulo
     * @param string $conteudo
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    public function criarPostagem(int $idUsuario, string $status = "1", string $titulo, string $conteudo) : void { #throw DBException, \Exception
    
        //Conferindo id do usuário
        if($idUsuario == 0)
            throw new \Exception("Erro! Usuário não localizado!", 1001);

        //Validando dados da postagem
        $this->_validarDadosPostagem($status, $titulo, $conteudo);

        //Gerando array de dados
        $arrayDados = [
            
            'idUsuario' =>  $idUsuario,
            'status' =>     $status,
            'titulo' =>     trim($titulo),
            'conteudo' =>   trim($conteudo)
            
        ];

        //Inserindo o registro no banco de dados.
        $this->_inserirPostagemBancoDados($arrayDados);

    }

    /**
     * Método utilizado para editar uma determinada postagem
     *
     * @param integer $idPostagem
     * @param string $status
     * @param string $titulo
     * @param string $conteudo
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    public function editarPostagem(int $idPostagem, string $status, string $titulo, string $conteudo) : void { #throw DBException, \Exception
    
        //Conferindo id do usuário
        if($idPostagem == 0)
            throw new \Exception("Erro! A postagem não foi localizada!", 1001);

        //Validando dados da postagem
        $this->_validarDadosPostagem($status, $titulo, $conteudo);

        //Gerando array de dados
        $arrayDados = [
            
            'status' =>     $status,
            'titulo' =>     trim($titulo),
            'conteudo' =>   trim($conteudo)
            
        ];

        //Inserindo o registro no banco de dados.
        $this->_atualizarDadosPostagemBancoDados($idPostagem, $arrayDados);

    }

    /**
     * obtem lista de posts, seja eles ativos ou não.
     *
     * @param integer $pagina: Página escolhida
     * @return array
     * @throws DBException : Em caso de erro de banco de dados
     */
    public function obterListaPaginadaPosts(int $pagina = 1) : array{ # throw DBException;

        //Verificando a paginação.
        if($pagina < 1){
            $pagina = 1;
        } else {

            //Limite de avanço de páginas
            if($pagina > $this->arrayCFGSEsp['MAX_AVANC_PAG_ADM'])
                $pagina = $this->arrayCFGSEsp['MAX_AVANC_PAG_ADM'];
        }

        //Cursos da demarcação da página
        $cursorPagina = ($pagina -1) * $this->arrayCFGSEsp['MAX_POSTS_PAG_ADM'];
            

        //Verificação do objeto do banco de dados
        $this->_verificarObjDB();
        
        $strSql = "
            SELECT
                P.id,
                P.titulo,
                P.status,
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
                P.idUsuario = USAD.id
            ORDER BY
                dataCad DESC
            LIMIT ".$cursorPagina.", ".$this->arrayCFGSEsp['MAX_POSTS_PAG_ADM']."
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

                //Gerando nomenclatura do status
                if($row['status'] == "1"){
                    $row['nomeStatus'] = "ativado";
                } else {
                    $row['nomeStatus'] = "desativado";
                }

                $arrayRetornoFinal[] = $row;

            }

            
        } else { //Encaminhando erro.
            throw new DBException("Desculpe! Ocorreu uma falha interna ao acessar o banco de dados. #834834", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Retorno final
        return [
            'pagina' => $pagina,
            'regs' => $arrayRetornoFinal
        ];

    }

    /**
     * Processo de obter dados de uma postagem
     *
     * @param integer $id
     * @return array
     * @throws DBException : Em caso de erro de banco de dados
     */
    public function getDadosPostagem(int $id) : array{ #throw DBException

           
        $this->_verificarObjDB();

        $strSql = "
            SELECT
                id,status,titulo,conteudo
            FROM
                "._TAB_Posts_."
            WHERE
                id = ?
            LIMIT 1
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

        return $arrayRetorno;

    }

    /**
     * Processo de exclusão de postagem
     *
     * @param integer $idPostagem
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    public function excluirPostagem(int $idPostagem) : void{ #throw DBException, \Exception

        //Conferindo id do usuário
        if($idPostagem == 0)
            throw new \Exception("Erro! A postagem não foi localizada!", 1001);

        //Finalmente inserindo dados
        if(!$this->_excluirPostagemBancoDados($idPostagem))
            throw new \Exception("Desculpe! Não foi possível realizar esta operação! Tente mais tarde", 5168);

    }
    
    
    #GETTERS
    /**
     * Obtém um objeto do tipo mysqli
     *
     * @return [mysqli]
     */

    public function getObjMysqli(){
        return $this->objMysqli;
    }
    
    #SETTERS
    /**
     * Edita o objeto do tipo mysqli
     *
     * @param [mysqli] $objMysqli
     * @return void
     */
    public function setObjMysqli($objMysqli){
        $this->objMysqli = $objMysqli;
    }
    

}

?>