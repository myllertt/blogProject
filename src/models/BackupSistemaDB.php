<?php


//Esta classe necessita de métodos da classe TratamentoCaracteres
require_once(__DIR_SYSLIBS__."/"."TratamentoCaracteres.php");
require_once(__DIR_CONFIGS__."/"."DBConeConfigs.php");

class BackupSistemaDB {

    private $arrayCFGSEsp; //Array de configurações específicas.

    private $stsBack; //0 = Não inciado, 1 = feito, 2 = Falha

    private $strCacheNomeArqGerado; //Caso o arquivo tenha sido gerado.
    
    /**
     * Função de configurações.
     *
     * @return void
     */
    private function _definirConfigsEspecificas(){
        
    }

    /**
     * Remove arquivos temporários caso assim existam
     *
     * @return void
     */
    private function _removerArqsTempCasoExistam() : void {

        if($this->stsBack == 1 && $this->strCacheNomeArqGerado !== null){

            if(@file_exists(__DIR_TMP_DUMP__."/".$this->strCacheNomeArqGerado))
                //Removendo
                @unlink(__DIR_TMP_DUMP__."/".$this->strCacheNomeArqGerado); 
            
        }

    }

    /**
     * Gerar caractéres aleatórios
     *
     * @param integer $qtd : Quantidade de carac. a serem gerados
     * @return string
     */
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

    /**
     * Verifica as condições do diretório.
     *
     * @return void
     * @throws \Exception : Em caso de erro de procedimento
     */
    private function _verificarCondicoesDiretorio(){ //throw \Exception

        //Verificando diretório tmp de trabalho
        if(!@is_dir(__DIR_TMP_DUMP__))
            throw new \Exception("O diretório (".__DIR_TMP_DUMP__.") não existe ou não tem permissões de acesso.");    
     
        //Verificando se o diretório tem permissões de escrita.
        if(!@is_writable(__DIR_TMP_DUMP__)){
            throw new \Exception("O sistema não possui permissões de escrita no diretório (".__DIR_TMP_DUMP__.").");
        }
    }

    /**
     * Realização do processo de geração do arquivo interno de backup e compactação
     *
     * @return void
     * @throws \Exception : Em caso de erro de procedimento
     */
    private function _processoMysqldump(){ //throw \Exception

        $date = new DateTime();

        //Gerando corpo do nome
        $corpoNomeArq = $date->format('d-m-Y___H_i_s')."____".$this->_gerarCharsAlea(6);

        //Nome arquivo sql geraldo.
        $nomeArq = $corpoNomeArq.".sql";

        $dirCompletoArqSqlTmp = __DIR_TMP_DUMP__."/".$nomeArq;

        //Neste momento o arquivo não pode existir no diretório
        if(@file_exists($dirCompletoArqSqlTmp)){
            throw new \Exception("Erro inesperado. Tente novamente em instantes. #238293"); 
        }

        //Montando comando
        $cmd = "/usr/bin/mysqldump -u".DBConeConfigs::$user." -h ".DBConeConfigs::$host." -p".DBConeConfigs::$password." ".DBConeConfigs::$DBName." > ".$dirCompletoArqSqlTmp;

        #Variáveis úteis
        $saidaDados;
        $retornoComando;

        //função que tenta remover arquivo temporário sql que foi criado
        $func_removeTMPBackSql = function () use ($dirCompletoArqSqlTmp) {

            //Removendo arquivo temp. caso ele exista.
            if(@file_exists($dirCompletoArqSqlTmp))
                //Removendo
                @unlink($dirCompletoArqSqlTmp); 
      

        };

        //Executando comando
        exec($cmd, $saidaDados, $retornoComando);

        //Verificando se o comando foi realizado
        if($retornoComando != 0){

            //Removendo arquivo temp. caso ele exista.
            $func_removeTMPBackSql();

            throw new \Exception("Erro inesperado. Base de dados indisponível. #9182502");
        }


        //Compactando arquivo -----------

        $nomeArqComp = $corpoNomeArq.".tar.gz";

        //Neste momento o arquivo não pode existir no diretório
        if(@file_exists(__DIR_TMP_DUMP__."/".$nomeArqComp)){ //

            //Removendo arquivo temp. caso ele exista.
            $func_removeTMPBackSql();

            throw new \Exception("Erro inesperado. Tente novamente em instantes. #848181"); 
        }

        #Montando comando de comptactação
        $cmd = "tar -czf ".__DIR_TMP_DUMP__."/".$nomeArqComp." ".$dirCompletoArqSqlTmp;

        //Executando comando
        exec($cmd, $saidaDados, $retornoComando);

        //Em caso de falha na compactação
        if($retornoComando != 0){

            //Removendo arquivo temp. caso ele exista.
            if(@file_exists(__DIR_TMP_DUMP__."/".$nomeArqComp))
                //Removendo
                @unlink(__DIR_TMP_DUMP__."/".$nomeArqComp); 

            //Removendo arquivo temp SQL. caso ele exista.
            $func_removeTMPBackSql();

            throw new \Exception("Erro inesperado. (Falha na compactação). Tente novamente em instantes. #78181");
        }

        //Removendo arquivo temp SQL. caso ele exista.
        $func_removeTMPBackSql();

        //Atualizando variável de cache com o caminho do arquivo gerado.
        $this->strCacheNomeArqGerado = $nomeArqComp;

        //Ao chegar até aqui significa que esta tudo certo.

    }

    /**
     * Construtor
     */
    function __construct(){

        #Configurações específicas
        $this->_definirConfigsEspecificas();

        $this->stsBack = 0;

        $this->strCacheNomeArqGerado = null;

    }

    /**
     * Função de gerar o backup
     *
     * @return void
     */
    public function gerarBackup() : void { //throw \Exception

        $this->_removerArqsTempCasoExistam();
        
        try {
            
            //reiniciando atributos.
            $this->strCacheNomeArqGerado = null;
            //--
            
            //Verificando condições
            $this->_verificarCondicoesDiretorio();

            //Gerando backup internamente
            $this->_processoMysqldump();

            $this->stsBack = 1;

        } catch (\Exception $e) {
            //Setando status de erro.
            $this->stsBack = 2;

            throw new Exception("Falha na operação! Detalhes: ".$e->getMessage(), 100010001);
            
        }
    }

    /**
     * Gerando o header de download do arquivo. Funciona apenas se o backup já tiver sido gerado
     *
     * @return boolean
     */
    public function gerarHeaderDownloadBackup() : bool{
        
        //Só funcionará se o processo tiver sido realizado.
        if($this->stsBack != 1){
            return false;
        }

        //Se por algum motivo o arquivo não exista
        if(!@file_exists(__DIR_TMP_DUMP__."/".$this->strCacheNomeArqGerado))
            return false;
        

        //Gerando headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $this->strCacheNomeArqGerado); 
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize(__DIR_TMP_DUMP__."/".$this->strCacheNomeArqGerado));

        return true;
    }

    /**
     * Descarrega os dados do arquivo. Funciona apenas se o backup já tiver sido gerado
     *
     * @return boolean
     */
    public function descarregarDadosBackup() : bool{

        //Só funcionará se o processo tiver sido realizado.
        if($this->stsBack != 1){
            return false;
        }

        //Se por algum motivo o arquivo não exista
        if(!@file_exists(__DIR_TMP_DUMP__."/".$this->strCacheNomeArqGerado))
            return false;

        //Descarregando arquivo
        readfile(__DIR_TMP_DUMP__."/".$this->strCacheNomeArqGerado); //Absolute URL

        return true;
    }
    

    /**
     * DESTRUTOR: Se encarregará de remover o arquivo temporário caso tenha sido gerado
     */
    function __destruct(){

        $this->_removerArqsTempCasoExistam();

    }

}

?>