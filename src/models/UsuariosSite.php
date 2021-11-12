<?php

use Sistema\DB\Exceptions\DBException;

//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class UsuariosSite {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.

    private function _definirConfigsEspecificas(){
        $this->arrayCFGSEsp['MIN_CARACS_SENHA'] = 8; //Mínimo de caracteres para o campo senha.
        $this->arrayCFGSEsp['MAX_CARACS_SENHA'] = 64; //Máximo de caracteres para o campo senha.
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

        if($len < 3)
            throw new \Exception("Erro! o usuário é muito curto!", 1007);

        if($len > 32)
            throw new \Exception("Erro! o usuário é muito longo!", 1008);

            
        #sobrenome----------

        $len = strlen($sobrenome);

        if($len > 64)
            throw new \Exception("Erro! o sobrenome é muito longo!", 1009);

        #Genero------------

        $arrTipoGeneros = ['M', 'F', 'NM'];
        if(!in_array($genero, $arrTipoGeneros, true))
            throw new \Exception("Erro! o preenchimento do campo gênero é inválido!", 1010);

        #E-mail------------
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            throw new \Exception("Erro! o e-mail é inválido!", 1010);

        #Validar Senha

    }

    private function _validarCampoSenha(string $string) : void { #throw \Exception
        
        $lenString = mb_strlen($string);

        if($lenString  == 0){
            throw new \Exception("Erro! Informe o campo senha.", 4444);
        }

        if($lenString < $this->arrayCFGSEsp['MIN_CARACS_SENHA']){
            throw new \Exception("Erro! O campo senha tem que ter pelo menos ".$this->arrayCFGSEsp['MIN_CARACS_SENHA']." caracteres!", 2222);
        }

        if( $lenString > $this->arrayCFGSEsp['MAX_CARACS_SENHA']){
            throw new \Exception("Erro! O campo senha é muito longo!", 2221);
        }

        if(TratamentoCaracteres::verifSeTemCaracNaoPermitidosCampoSenha($string)){
            throw new \Exception("Erro! O campo senha possui caracteres inválidos", 2224);
        }

        
    }

    //Gera um hash da senha de acordo com o algoritmo predefinido
    private function _gerarHashSenha(string $string) : string{
        
        //Gera a inversão de algoritmo sha512
        return strrev(hash("sha512", $string));

    }

    //Cadastrar usuário no banco de dados.
    private function _inserirNovoUsuarioBancoDados(array $arrayDados) : void{ #throw DBException

        /*
            'usuario'
            'hashSenha'
            'nome'
            'sobrenome'
            'email'
            'genero'          
        */

        $this->_verificarObjDB();

        $strSql = "

            INSERT INTO 
                "._TAB_UsSite_."
                    (usuario,hashSenha,nome,sobrenome,email,genero)
                VALUES
                    (?,?,?,?,?,?)
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('ssssss', $arrayDados['usuario'], $arrayDados['hashSenha'], $arrayDados['nome'], $arrayDados['sobrenome'], $arrayDados['email'], $arrayDados['genero']);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }
    }

    function __construct(mysqli $objMysqli){
        $this->objMysqli = $objMysqli;

        $this->_definirConfigsEspecificas();
    }
    
    //Processo de registro do usuário
    public function registrarUsuario(string $usuario, string $nome, string $sobrenome, string $genero, string $email, string $senha) : void{ #throw DBException, \Exception

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
        $this->_validarCampoSenha($senha);

        //Gerando array com dados finais
        $arrayDadosInsert = [
            'usuario'       => $usuario,
            'hashSenha'     => $this->_gerarHashSenha($senha),
            'nome'          => trim( TratamentoCaracteres::rmvCharTags($nome)),
            'sobrenome'     => trim( TratamentoCaracteres::rmvCharTags($sobrenome)),
            'email'         => $email,
            'genero'        => $genero          
        ];

        //Finalmente inserindo dados
        $this->_inserirNovoUsuarioBancoDados($arrayDadosInsert);
            
    }

    public function getDadosCadastrais(int $idUsuario) : array{ #throw DBException

           
        $this->_verificarObjDB();

            $strSql = "
            SELECT
                id,usuario,nome,sobrenome,email,genero
            FROM
                "._TAB_UsSite_."
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
        
        $arrayRetorno = $objResult->fetch_assoc();
        //Fechando statemment
        $objStmt->close();

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