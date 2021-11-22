<?php

use Sistema\DB\Exceptions\DBException;

use Sistema\PermissoesACL;
use Sistema\PermissoesACL\ACL_PERM;

//Esta classe necessita de métodos da classe TratamentoCaracteres
#require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class PermissoesUsuariosACL {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.
    
    /**
     * Método definição de configurações específicas da classe
     *
     * @return void
     */
    private function _definirConfigsEspecificas(){
        //
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
     * Exlcuir do banco de dados um determinado usuário
     *
     * @param integer $idUsuario
     * @return boolean
     * @throws DBException : Em caso de erro de banco de dados
     */
    private function _excluirPermissoesUsuarioBancoDados(int $idUsuario) : bool { #throw DBException

        $this->_verificarObjDB();

        $strSql = "
            DELETE
            FROM
                "._TAB_PermsUsAdm_."
            WHERE
                idUsuario = ?
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('i', $idUsuario);
        
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
     * Editando a definição de basePermsACL no banco de dados.
     *
     * @param integer $idUsuario
     * @param string $basePermsACL
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     */
    private function _editar_basePermsACL_UsuarioBancoDados(int $idUsuario, string $basePermsACL) : void{ #throw DBException
        
        $this->_verificarObjDB();

            $strSql = "
            UPDATE
                "._TAB_UsAdmin_."
            SET
                basePermsACL = ?,
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
        $objStmt->bind_param('si', $basePermsACL, $idUsuario);

        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }
        
        $objStmt->close();
    }

    /**
     * efinir permissões usuário
     *
     * @param string $modo
     * @param integer $idUsuario
     * @param array $arrayPermissoes
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    private function _definirPermissoesUsuario(string $modo, int $idUsuario, array $arrayPermissoes) { # throw DBException, \Exception

        //Verificando a entrada do usuário
        if($idUsuario == 0)
            throw new \Exception("Erro! Não foi possível localizar o usuário destinado", 1001);


        //Iniciando operações
        if(!@$this->objMysqli->autocommit(false)){
            throw new DBException("Desculpe! Ocorreu uma falha interna ao acessar o banco de dados. #85886", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Tratamento de transação
        try {
            
            //Verificando se existem permissões enviadas para serem cadastradas
            if(count($arrayPermissoes) > 0){

                //Baixar permissões disponíveis no sistema.
                $arrayPermsSis = $this->obterTodasPermissoes(true);

                //Verificando a veracidade dos códigos do array enviado
                foreach ($arrayPermissoes AS $codPerm) {

                    if(!in_array($codPerm, $arrayPermsSis, true)){
                        throw new \Exception("Falha na operação! Existe uma ou mais permissões que não estão disponíveis no sistema! Recarregue a página e tente novamente.", 1001);
                    }
                }

                //Limpando permissões atuais do usuário.
                $this->_excluirPermissoesUsuarioBancoDados($idUsuario);

                //Veriáveis de inserção
                $idUsuario_insert = $idUsuario; //Não muda
                $codigo_insert;


                //Inserindo registros de permissões.
                $strSql = "

                    INSERT INTO
                        "._TAB_PermsUsAdm_."
                            (idUsuario,codigo)
                        VALUES
                            (?,?)
                ";

                //Tentando preparar a consulta.
                $objStmt = $this->objMysqli->prepare($strSql);

                //Caso não consiga
                if(!$objStmt){
                    throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
                }

                //Setando parâmetros
                $objStmt->bind_param('is', $idUsuario_insert, $codigo_insert);
                
                //Inserindo no banco permissão por permissão
                foreach ($arrayPermissoes as $codPerm) {
                    
                    //Setando dados para o statement
                    $codigo_insert = $codPerm;

                    if(!$objStmt->execute()){
                        throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
                    }

                }

                $objStmt->close();
                
            } else {
                //Limpando permissões atuais do usuário.
                $this->_excluirPermissoesUsuarioBancoDados($idUsuario);
            }

            //Atualizando a basePermsACL nas definições do usuário
            $this->_editar_basePermsACL_UsuarioBancoDados($idUsuario, $modo);

        } catch (DBException $e) { //Capturando para fazer rollback
          
            //Desfazendo operações
            @$this->objMysqli->rollback();

            //Encaminhando o erro.
            throw $e;
            
        } catch (\Exception $e) { //Capturando para fazer rollback

            //Desfazendo operações
            @$this->objMysqli->rollback();

            //Encaminhando o erro.
            throw $e;
        }

        //Por fim, comitando operações.
        if(!@$this->objMysqli->commit()){
            throw new DBException("Desculpe! Ocorreu uma falha interna ao acessar o banco de dados. #85886", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

    }

    /**
     * Construtor
     *
     * @param [mysqli] $objMysqli
     */
    function __construct($objMysqli){

        $this->objMysqli = $objMysqli;

        #Configurações específicas
        $this->_definirConfigsEspecificas();
    }

    /**
     * Obtém todas as permissões cadastradas no sistema
     *
     * @param boolean $modoSimplificado : TRUE = Consulta simplificada, FALSE = Consulta composta
     * @return array
     * @throws DBException : Em caso de erro de banco de dados
     */
    public function obterTodasPermissoes(bool $modoSimplificado = false) : array{ # throw DBException;
       
        //Verificação do objeto do banco de dados
        $this->_verificarObjDB();
        
        $strSql = "
            SELECT
                codigo,
                descricao
            FROM
                "._TAB_Permissoes_."
        ";

        //Array final de retorno
        $arrayRetornoFinal = [];

        $result = @ $this->objMysqli->query($strSql);
        if($result){

            //Modo normal e detalhado
            if(!$modoSimplificado){

                $arrayRetornoFinal = $result->fetch_all(MYSQLI_ASSOC);

            } else { //Modo simplificado. Um array de códigos.

                while($row = $result->fetch_assoc()){

                    $arrayRetornoFinal[] = $row['codigo'];

                }
            }
            

        } else { //Encaminhando erro.
            throw new DBException("Desculpe! Ocorreu uma falha interna ao acessar o banco de dados. #85886", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        return $arrayRetornoFinal;
    }

    /**
     * Obtém as permissões específicas de um determinado usuário.
     *
     * @param integer $idUsuario
     * @return array
     * @throws DBException : Em caso de erro de banco de dados
     */
    public function obterPermissoesUsuario(int $idUsuario) : array {

         //Verificação do objeto do banco de dados
         $this->_verificarObjDB();
        
         $strSql = "
            SELECT
                codigo
            FROM
                "._TAB_PermsUsAdm_."
            WHERE
                idUsuario = ?
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('i', $idUsuario);

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
        
        $arrayRetorno = $objResult->fetch_all(MYSQLI_ASSOC);
        //Fechando statemment
        $objStmt->close();

        return $arrayRetorno;
        
    }
 
    /**
     * Definir permissões usuário. Base padrão PERMTIR
     *
     * @param integer $idUsuario
     * @param array $arrayPermissoes
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    public function definirPermissoesUsuario_basePadraoPERMITIR(int $idUsuario, array $arrayPermissoes){ # throw DBException, \Exception
        $this->_definirPermissoesUsuario('permitir', $idUsuario, $arrayPermissoes);
    }
    /**
     * Definir permissões usuário. Base padrão PERMTIR
     *
     * @param integer $idUsuario
     * @param array $arrayPermissoes
     * @return void
     * @throws DBException : Em caso de erro de banco de dados
     * @throws \Exception : Em caso de erro de procedimento
     */
    public function definirPermissoesUsuario_basePadraoNEGAR(int $idUsuario, array $arrayPermissoes){ # throw DBException, \Exception
        $this->_definirPermissoesUsuario('negar', $idUsuario, $arrayPermissoes);
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