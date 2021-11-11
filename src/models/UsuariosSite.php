<?php

use Sistema\DB\Exceptions\DBException;

//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class UsuariosSite {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.

    private function _definirConfigsEspecificas(){
    }

    private function _verificarObjDB(){
        if(!$this->objMysqli){
            throw new DBException("Falha ao iniciar a conexão com o banco de dados");            
        }
    }

    private function _consultarSeUsuarioExiste(string $usuario) : bool{
        
        $this->_verificarObjDB();

            $strSql = "
            SELECT
                COUNT(id) as qtd
            FROM
                "._TAB_UsSite_."
            WHERE
                usuario = ?
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('s', $usuario);

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
            return false;
        }
        
        $arrayRetorno = $objResult->fetch_assoc();
        //Fechando statemment
        $objStmt->close();

        if($arrayRetorno['qtd'] > 0){
            return true; //o usuário existe
        }

        //Não existe
        return false;

    }

    private function _validarDadosCadastraisUsuario(string $nome, string $sobrenome, string $genero, string $email){ #throw DBException

        #nome----------
        if($nome == "")
            throw new \Exception("Informe o nome do usuário", 1006);

        $len = strlen($nome);

        if($len < 5)
            throw new \Exception("Erro! o usuário do é muito curto!", 1007);

        if($len > 32)
            throw new \Exception("Erro! o usuário do é muito longo!", 1008);

            
        #sobrenome----------

        $len = strlen($sobrenome);

        if($len > 64)
            throw new \Exception("Erro! o sobrenome do é muito longo!", 1009);

        #Genero------------

        $arrTipoGeneros = ['M', 'F', 'NM'];
        if(!in_array($genero, $arrTipoGeneros, true))
            throw new \Exception("Erro! o preenchimento do campo gênero é inválido!", 1010);

        #E-mail------------
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new \Exception("Erro! o e-mail é inválido!", 1010);

        #Validar Senha

    }

    function __construct(mysqli $objMysqli){
        $this->objMysqli = $objMysqli;

        $this->_definirConfigsEspecificas();
    }
    
    public function registrarUsuario(string $usuario, string $nome, string $sobrenome, string $genero, string $email, string $senha){ #throw DBException, \Exception

        #usuario----------
        if($usuario == "")
            throw new \Exception("Informe o usuário!", 1001);

        if(!TratamentoCaracteres::verifCaracteresPadraoUsuario($usuario))
            throw new \Exception("Erro! O usuário possui caracteres inválidos ou é inválido!", 1002);

        $len = strlen($usuario);

        if($len < 5)
            throw new \Exception("Erro! o usuário do é muito curto!", 1003);

        if($len > 32)
            throw new \Exception("Erro! o usuário do é muito longo!", 1004);


        //Convertendo usuário para minúsculo.
        $usuario = strtolower( $usuario );

        //Verificando se o usuário existe.
        if($this->_consultarSeUsuarioExiste($usuario))
            throw new \Exception("Erro! o usuário (".$usuario.") já está registrado no sistema! Por gentileza escolha outro!", 1005);

        //Validando dados cadastrais comuns
        $this->_validarDadosCadastraisUsuario($nome, $sobrenome, $genero, $email);

        //Validar senha
            
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