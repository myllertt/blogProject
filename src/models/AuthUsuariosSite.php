<?php

use Sistema\DB\Exceptions\DBException;

//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");

class AuthUsuariosSite {

    private $objMysqli;

    private $arrayCFGSEsp; //Array de configurações específicas.

    private $arrCacheDadosUsLogado; //Cache de informações do usuário logado

    private function _definirConfigsEspecificas(){
        $this->arrayCFGSEsp['TIPO_SESSAO'] = 'site'; //Tipo site.
    }

    private function _verificarObjDB(){
        if(!$this->objMysqli){
            throw new DBException("Falha ao iniciar a conexão com o banco de dados");            
        }
    }

    private function _consultarUsuario(string $usuario) : array{
        
        $this->_verificarObjDB();

            $strSql = "
            SELECT
                id,nome,sobrenome,usuario,hashSenha
            FROM
                "._TAB_UsSite_."
            WHERE
                usuario = ?
            LIMIT 1
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
            return []; //Sem resultados
        }
        
        $arrayRetorno = $objResult->fetch_assoc();
        //Fechando statemment
        $objStmt->close();

        return $arrayRetorno;

    }

    //Registra o tempo da última movimentação do usuário na sessão
    private function _updateMovimentacaoUsSessaoDB(int $idUsuario) : void{ #throw DBException

        $this->_verificarObjDB();

            $strSql = "
            UPDATE
                "._TAB_UsSite_."
            SET
                sesAtivaDtUp = now()
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

    }

    //Remove a sessão ativa do usuário no banco de dados.
    private function _removerSessaoAtivaUsuarioNoDB(int $idUsuario) : void{ #throw DBException

        $this->_verificarObjDB();

            $strSql = "
            UPDATE
                "._TAB_UsSite_."
            SET
                sesAtivaCod = null
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

    }

    //Consulta a sessão do usuário no DB
    private function _consultarSessaoDB(int $idUsuario, string $codSessao) : array{
        
        $this->_verificarObjDB();

            $strSql = "
            SELECT
                id,nome,sobrenome,usuario
            FROM
                "._TAB_UsSite_."
            WHERE
                id = ?
                AND sesAtivaCod LIKE BINARY ?
            LIMIT 1
        ";

        //Tentando preparar a consulta.
        $objStmt = $this->objMysqli->prepare($strSql);

        //Caso não consiga
        if(!$objStmt){
            throw new DBException("Ocorreu uma falha no DB.", $this->objMysqli->errno, $this->objMysqli->error, null);
        }

        //Setando parâmetros
        $objStmt->bind_param('is', $idUsuario, $codSessao);

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
            return []; //Sem resultados
        }
        
        $arrayRetorno = $objResult->fetch_assoc();
        //Fechando statemment
        $objStmt->close();

        return $arrayRetorno;

    }

    //Gerar caractéres aleatórios
    private function _gerarCharsAlea(int $qtd){
		
		$pos = "";		
		
		$strDados = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890123456789012345678901234567890";
		$str = ""; 
		
		for($i = 0; $i < $qtd; $i++){
			$pos = rand(0, (strlen($strDados) -1));
			$str .= $strDados{$pos};
		}
		
		return $str;
		
	}

    //Registrando sessão no banco de dados
    private function _registrarNovaSessaoUsuarioDB(int $idUsuario) : string { #throw DBException
        
        //Obtendo novo código
        $codSesAlea = $this->_gerarCharsAlea(32);

        $this->_verificarObjDB();

        $strSql = "

            UPDATE 
                "._TAB_UsSite_."
            SET
                sesAtivaCod = ?,
                sesAtivaDtIni = now(),
                sesAtivaDtUp = null
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
        $objStmt->bind_param('sd', $codSesAlea, $idUsuario);
        
        //Caso não execute
        if(!$objStmt->execute()){
            throw new DBException("Ocorreu uma falha no DB", $objStmt->errno, $objStmt->error, null);
        }

        //Retornando o código da sessão.
        return $codSesAlea;
    }

    //Salvando informações na memória de sessão
    private function _registrarSessaoEmMem(string $codSessao, int $idUsuario, string $nomeUser){

        //Renovando ID sessão
        if(!session_regenerate_id())
            return false;

        //Recuperando Data Atual
        $date = new DateTime();

        #Atualizando parâmetros
        $_SESSION['codSes'] =   $codSessao;
        $_SESSION['idUser'] =   $idUsuario;
        $_SESSION['nomeUser'] = $nomeUser;
        $_SESSION['tipo'] =     $this->arrayCFGSEsp['TIPO_SESSAO']; //tipo site
        $_SESSION['dataIni'] =  $date->format('Y-m-d H:i:s');
        $_SESSION['timeIni'] =  time();

        return true;
    }

    function __construct($objMysqli){

        //Iniciando processo de sessão caso não tenha sido iniciada.
        if(session_status() == 1){
            session_start();
        }         
        
        //Iniciando variável
        $this->arrCacheDadosUsLogado = [
            'sts' => false,
            'id' => null,
            'usuario' => null,
            'nome' => null,
            'sobrenome' => null
        ];

        $this->objMysqli = $objMysqli;

        $this->_definirConfigsEspecificas();
    }
        
    //Realiza o processo de login
    public function efetuarLogin(string $usuario, string $hashSenha) : void{ #throw DBException, \Exception

        if($usuario == "")
            throw new \Exception("Informe o usuário!", 1001);

        if($hashSenha == "")
            throw new \Exception("Informe a senha!", 1002);

        $resConsUs = $this->_consultarUsuario($usuario);

        if(empty($resConsUs))
            throw new \Exception("Erro! Usuário ou senha inválidos!", 1005);

        if(strtolower($usuario) != $resConsUs['usuario'])
            throw new \Exception("Erro! Usuário ou senha inválidos!", 1006);


        if($hashSenha != $resConsUs['hashSenha'])
            throw new \Exception("Erro! Usuário ou senha inválidos!", 1007);

        //Tentando registrar sessão no banco de dados
        $codSessao = $this->_registrarNovaSessaoUsuarioDB($resConsUs['id']);

        //Simplificação nome do usuário.        
        $abrevNomeUs = TratamentoCaracteres::gerarAbreviacaoNome($resConsUs['nome'], $resConsUs['sobrenome']);

        //Atualizando informações na memória
        if(!$this->_registrarSessaoEmMem($codSessao, (int)$resConsUs['id'], $abrevNomeUs))
            throw new \Exception("Desculpe! Ocorreu uma falha no processo de login! Tente mais tarde!", 1009);

        # Se chegou aqui então tudo ocorreu OK
            
    }

    //Destroi sessão apenas da memória do sistema, não entrando causando nenhum efeito do banco de dados
    public function destruirSessaoApenasMemoriaSis() : bool{
        if(!$this->checkSeExisteSessaoAtivaTipica())
            return false;
        
        session_unset();
	    session_destroy();

        return true;
        
    }

    //Realiza logou da sessão ativa do usuário caso tenha
    public function realizarLogout() : bool{

        //Verificando sessão na memoria do sistema
        if(!$this->checkSeUsuarioEstaAutenticadoDB()) #throw DBException
            return false;

        //Removendo sessão ativa do usuário
        $this->_removerSessaoAtivaUsuarioNoDB($_SESSION['idUser']);


        session_unset();
	    session_destroy();

        return true;

    }

    //Verifica se existe sessão ativa do tipo esperado
    public function checkSeExisteSessaoAtivaTipica() : bool{

        if(isset($_SESSION['codSes']) && isset($_SESSION['tipo']) && $_SESSION['tipo'] == $this->arrayCFGSEsp['TIPO_SESSAO'])
            return true;

        return false;
    }

    //Além de verificar a sessão na memória. Também é verificada a integridade da sessão no banco de dados
    public function checkSeUsuarioEstaAutenticadoDB(bool $movimentarSessao=true) : bool { #throw DBException
        
        //Verificando sessão da memória.
        if(!$this->checkSeExisteSessaoAtivaTipica()){
            return false;
        }

        //Consultando sessão usuário no DB
        $resCons = $this->_consultarSessaoDB((int)$_SESSION['idUser'], $_SESSION['codSes']);

        //Sessão inexistente no banco.
        if(empty($resCons)){
            
            //Quebrar sessão
            session_unset();
	        session_destroy();

            return false;
        }

        //Obtendo abreviação nome do DB
        $nomeUserDb = TratamentoCaracteres::gerarAbreviacaoNome($resCons['nome'], $resCons['sobrenome']);
        
        //Atualizando dados da sessão caso necessário
        if($_SESSION['idUser'] != $resCons['id']){
            $_SESSION['idUser'] =   $resCons['id'];
        }     

        if($_SESSION['nomeUser'] != $nomeUserDb){
            $_SESSION['nomeUser'] = $nomeUserDb;
        }
        //------------------------------------------

        $this->arrCacheDadosUsLogado = [
            'sts' => true,
            'id' => $resCons['id'],
            'usuario' => $resCons['usuario'],
            'nome' => $resCons['nome'],
            'sobrenome' => $resCons['sobrenome'],
        ];

        //Atualizando tempo de sessão no banco de dados do usuário.
        if($movimentarSessao){
            $this->_updateMovimentacaoUsSessaoDB($resCons['id']);
        }
        
        return true;
    }

    //Somente será disponível de a sessão já tiver sido verificada. Ex: com o método: checkSeUsuarioEstaAutenticadoDB()
    public function getArrayCacheDadosUsuarioLogado() : array{

        //Ainda não foi realizado o cache
        if(!$this->arrCacheDadosUsLogado['sts'])
            return [];
        
        $arrayRet = $this->arrCacheDadosUsLogado;
        unset($arrayRet['sts']);

        return $arrayRet;
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