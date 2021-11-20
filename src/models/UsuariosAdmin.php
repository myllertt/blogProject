<?php

use Sistema\DB\Exceptions\DBException;

//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class UsuariosAdmin {

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
                "._TAB_UsAdmin_."
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

    private function _validarDadosCadastraisUsuario(string $status, string $nome, string $sobrenome, string $genero, string $email){ #throw DBException

        #status
        $arrSts = ['1', '0']; //Array de possíveis valores para status
        if(!in_array($status, $arrSts, true))
            throw new \Exception("Erro! O campo status é inválido", 1100);


        #nome----------
        if($nome == "")
            throw new \Exception("Informe o primeiro nome do usuário", 1006);

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

    private function _validarCampoSenha(string $string, string $lab = null) : void { #throw \Exception
        
        $lenString = mb_strlen($string);

        if($lab === NULL){
            $lab = "campo senha";
        }

        if($lenString  == 0){
            throw new \Exception("Erro! Informe o ".$lab."!", 4444);
        }

        if($lenString < $this->arrayCFGSEsp['MIN_CARACS_SENHA']){
            throw new \Exception("Erro! O ".$lab." tem que ter pelo menos ".$this->arrayCFGSEsp['MIN_CARACS_SENHA']." caracteres!", 2222);
        }

        if( $lenString > $this->arrayCFGSEsp['MAX_CARACS_SENHA']){
            throw new \Exception("Erro! O ".$lab." é muito longo!", 2221);
        }

        if(TratamentoCaracteres::verifSeTemCaracNaoPermitidosCampoSenha($string)){
            throw new \Exception("Erro! O ".$lab." possui caracteres inválidos", 2224);
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
            'status'
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
                "._TAB_UsAdmin_."
                    (status,usuario,hashSenha,nome,sobrenome,email,genero)
                VALUES
                    (?,?,?,?,?,?,?)
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('sssssss', $arrayDados['status'], $arrayDados['usuario'], $arrayDados['hashSenha'], $arrayDados['nome'], $arrayDados['sobrenome'], $arrayDados['email'], $arrayDados['genero']);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }
    }

    //Atualiza os dados cadastrais de um determinado usuário
    private function _atualizarDadosCadastraisUsuarioBancoDados(int $idUsuario, array $arrayDados) : void{ #throw DBException

        /*
            'status'
            'nome'
            'sobrenome'
            'email'
            'genero'          
        */

        $this->_verificarObjDB();

        $strSql = "

            UPDATE
                "._TAB_UsAdmin_."
            SET
                status = ?,
                nome = ?,
                sobrenome = ?,
                email = ?,
                genero = ?,
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
        $objStmt->bind_param('sssssi', $arrayDados['status'], $arrayDados['nome'], $arrayDados['sobrenome'], $arrayDados['email'], $arrayDados['genero'],  $idUsuario);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }

    }

    //Atualiza a senha do usuário no banco de dados.
    private function _atualizarSenhaUsuarioBancoDado(int $idUsuario, string $hashSenha) : bool{ #throw DBException

        $this->_verificarObjDB();

        $strSql = "
            UPDATE 
                "._TAB_UsAdmin_."
            SET
                hashSenha = ?
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
        $objStmt->bind_param('si', $hashSenha, $idUsuario);
        
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

    //Exlcuir do banco de dados um determinado usuário
    private function _excluirUsuarioBancoDados(int $idUsuario) : bool { #throw DBException

        $this->_verificarObjDB();

        $strSql = "
            DELETE
            FROM
                "._TAB_UsAdmin_."
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

    private function _getListaUsuarios(array $arrayCampos, int $limite = null) : array{ #throw DBException
           
        $this->_verificarObjDB();

        //Verificando array campos
        if(count($arrayCampos) <= 0)
            throw new DBException("Ocorreu uma falha no DB.", 0, get_class($this).": Não foram informados os parâmetros corretos", null);
        
        //Gerando string de colunas
        $strCampos = implode(",", $arrayCampos);

        //Tratando limite
        if($limite === null){
            $strSQLLimite = "";
        } else {
            $strSQLLimite = "LIMIT ".abs($limite); //Mantendo o número sempre positivo
        }

        //Consulta.
        $strSql = "
            SELECT
                ".$strCampos."
            FROM
                "._TAB_UsAdmin_."
            ".$strSQLLimite."
        ";
        
        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        //$objStmt->bind_param('i', $);

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
        
        //Capturando todo o resultado para ser retornado
        $arrayRetorno = $objResult->fetch_all(MYSQLI_ASSOC);
        //Fechando statemment
        $objStmt->close();

        return $arrayRetorno;

    }

    function __construct($objMysqli){
        $this->objMysqli = $objMysqli;

        $this->_definirConfigsEspecificas();
    }
    
    //Processo de registro do usuário
    public function cadastrarUsuario(string $status = "1", string $usuario, string $nome, string $sobrenome, string $genero, string $email, string $senha) : void{ #throw DBException, \Exception

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
        $this->_validarDadosCadastraisUsuario($status, $nome, $sobrenome, $genero, $email);

        //Validar senha
        $this->_validarCampoSenha($senha);

        //Gerando array com dados finais
        $arrayDadosInsert = [
            'status'        => $status,
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

    //Processo de edição de dados cadastrais
    public function editarDadosCadastrais(int $idUsuario,     string $status, string $nome, string $sobrenome, string $genero, string $email) : void{ #throw DBException, \Exception

        if($idUsuario == 0)
            throw new \Exception("Erro! Usuário não localizado!", 1001);

        //Validando dados cadastrais comuns
        $this->_validarDadosCadastraisUsuario($status, $nome, $sobrenome, $genero, $email);

        //Gerando array com dados finais
        $arrayDadosUpdate = [
            'status'        => $status,
            'nome'          => trim( TratamentoCaracteres::rmvCharTags($nome)),
            'sobrenome'     => trim( TratamentoCaracteres::rmvCharTags($sobrenome)),
            'email'         => $email,
            'genero'        => $genero          
        ];

        //Finalmente atualizando os dados
        $this->_atualizarDadosCadastraisUsuarioBancoDados($idUsuario, $arrayDadosUpdate);

    }

    //Aterar senha do usuário
    public function alterarSenha(int $idUsuario, string $hashSenha_atual, string $novaSenha) : void{ #throw DBException, \Exception

        if($idUsuario == 0)
            throw new \Exception("Erro! Usuário não localizado!", 1001);

        if($hashSenha_atual == "")
            throw new \Exception("Erro! A senha atual informada é inválida!", 1001);


        //Validando o campo (nova senha)
        $this->_validarCampoSenha($novaSenha, "campo (nova senha)");

        //Obter dados cadastrais para comparação da senha atual;
        $resCons = $this->getDadosCadastrais($idUsuario, true);

        //Verificando se a senha atual confere
        if($resCons['hashSenha'] != $hashSenha_atual){
            throw new \Exception("Erro! A senha atual não confere com a registrada no sistema!", 9999);
        }

        $this->_atualizarSenhaUsuarioBancoDado($idUsuario, $this->_gerarHashSenha($novaSenha));

    }

    //Processo de exclusão do usuário
    public function excluirUsuario(int $idUsuario) : void{ #throw DBException, \Exception

        //Finalmente inserindo dados
        if(!$this->_excluirUsuarioBancoDados($idUsuario))
            throw new \Exception("Desculpe! Não foi possível realizar esta operação! Tente mais tarde", 3444);

    }

    //Processo de obter dados cadastrais
    public function getDadosCadastrais(int $idUsuario, bool $incluirHashSenha = false) : array{ #throw DBException

           
        $this->_verificarObjDB();

        if(!$incluirHashSenha){
            $strSql = "
                SELECT
                    id,status,usuario,nome,sobrenome,email,genero
                FROM
                    "._TAB_UsAdmin_."
                WHERE
                    id = ?
                LIMIT 1
            ";
        } else {
            $strSql = "
                SELECT
                    id,status,usuario,hashSenha,nome,sobrenome,email,genero
                FROM
                    "._TAB_UsAdmin_."
                WHERE
                    id = ?
                LIMIT 1
            ";
        }
        

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

    /**
     * Função responsável por retornar uma lista de usuários
     *
     * @param integer|null $limiteRegs 
     * @return array
     */
    public function getUsuarios(int $limiteRegs = null) : array{ #throw DBException

        //Verificando a disposição do limite
        if($limiteRegs === null || $limiteRegs <= 0)
            $limiteRegs = null;

        $arrayCampos = [
            'id',
            'status',
            'usuario',
            'nome',
            'sobrenome',
            'dataCad',
            'dataAt'
        ];

        //Consultando informações
        $arrRet = $this->_getListaUsuarios($arrayCampos, $limiteRegs);

        if(empty($arrRet))
            return []; //Não houve resultados

        //Tratando o array antes de ser enviado
        foreach ($arrRet as $key => $arrUs) {

            //Gerando nome completo
            $arrRet[$key]['nomeComp'] = $arrUs['nome']." ".$arrUs['sobrenome'];

            //Adicionado conversão Brasileira de data. (dataCad)
            $arrRet[$key]['dataHoraCadBR'] = TratamentoCaracteres::dateTimeUSA_DataHoraBR($arrUs['dataCad']);

            //Adicionado conversão Brasileira de dat. (dataAt)
            if($arrRet[$key]['dataAt'] !== NULL){
                $arrRet[$key]['dataHoraAtBR'] = TratamentoCaracteres::dateTimeUSA_DataHoraBR($arrUs['dataAt']);
            } else {
                $arrRet[$key]['dataHoraAtBR'] = null;
            }

            //Gerando nomenclatura do status
            if($arrUs['status'] == "1"){
                $arrRet[$key]['nomeStatus'] = "ativado";
            } else {
                $arrRet[$key]['nomeStatus'] = "desativado";
            }
        }

        //Retornando resultado final
        return $arrRet;
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