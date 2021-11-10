<?php

namespace Sistema{
    
    class ProcessamentoRotas {

        private $arrayRotasConfigs;

        private $CFGS;

        private function _setConfiguracoes(){
            $this->CFGS = [];

            $this->CFGS['mtdsAceitos'] = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH'];  
            $this->CFGS['mtdsALL'] = "ALL"; //Tipo de método que envolve todos

        }

        private function _encaminharErroDefinicoes(string $msg) : void{
            # Sistema\ProcessamentoRotas\Exceptions\PcRException
            throw new ProcessamentoRotas\Exceptions\PcRException( get_class($this).": Falha na definição das rotas: \n Inf(".$msg.")" );
        }

        private function _verificarEstrutuParamDinamicoRota(string $parametro) : bool{

            //Aceita apenas letras(Maiu Minu), numeros, Caracteres (_{})
            if (preg_match('/^[a-z0-9_{}]+$/i', $parametro)) {
                return true;
            } else {
                return false;
            }
        
        }

        private function _verificarSeEstruturaRotaEValida(string $rota) : bool{

            //Aceita apenas letras(Maiu Minu), numeros, Caracteres (./-_{})
            if (preg_match('/^[a-z0-9._\-\/{}]+$/i', $rota)) {
                return true;
            } else {
                return false;
            }

        }

        private function _detectarDuplicidadeEmRotasDefinidas(string $metodo, string $strRotaCMP) : bool{
            
            if(isset($this->arrayRotasConfigs[$metodo]) && isset($this->arrayRotasConfigs[ $metodo ][ $strRotaCMP ]))
                return true;

            return false;
        }

        /**
         * Método responsável por processar o parâmetro de uma rota individualmente e descobrir se ele é um parâmetro normal(/rota) ou dinâmico ({id}) 
         *
         * @param string $parm
         * @return true|string|false - True: Condição de sucesso de um parâmetro normal, string: Condição de sucesso de um parâmetro dinâmica, e este caso será retornado o nome do parâmetro. false: Neste caso indica um erro ao processar o parâmetro
         */
        private function _processarParmRotaIndividualmente(string $parm){

            /*
                Ao chegar neste ponto subentende que os caractéres básicos de um parâmetro já foram validados pelas funções anteriores
            */

            if($parm == "")
                return false;
                
            //Detectando Parametro normal
            if($parm[0] != "{"){ 

                $keyAsci = ord($parm[0]);

                //Parâmetro válildo só começa pode começar de (a-z, A-Z, 0-9, _)
                if(($keyAsci >= 65 && $keyAsci <= 90) || ($keyAsci >= 97 && $keyAsci <= 122) || ($keyAsci >= 48 && $keyAsci <= 57) || $keyAsci == 95){
                    return true;
                }

                return false;

            } else {//Detectando Parâmetro Dinâmico

                //Checando estrutura caracteres parâmetro dinâmico
                if(!$this->_verificarEstrutuParamDinamicoRota($parm))
                    return false;

                $lenParm = strlen($parm);

                // O mínimo para que se forme um parâmetro dinâmico é que se contenha pelo menos 3 carc. Ex: {p}
                if($lenParm < 3)
                    return false;

                //Obtendo a primeira letra da variável
                $keyAsci = ord($parm[1]);

                //Parâmetro válildo só começa pode começar de (a-z, A-Z, _)
                if(($keyAsci >= 65 && $keyAsci <= 90) || ($keyAsci >= 97 && $keyAsci <= 122) || $keyAsci == 95){
                    
                    $ultimoChar = $parm[ $lenParm-1 ];

                    //Um parâmetro variável só pode terminar com }
                    if($ultimoChar == "}"){
                        
                        //Recortando variável para ser retornada o que também indica um sucesso
                        return substr($parm, 1, $lenParm-2);

                    }

                }

                return false;

            }
            
        }

        private function _processarParametrosRota(string $rota) {
            
            if(strlen($rota) <= 0)
                return false;
            
            //Toda rota precisa começar com "/" indicando a raiz
            if($rota[0] != '/')
                return false;

            //Removendo o primeiro caracter para que o método explode fique correto.
            $rota = substr($rota, 1); 

            //Array de retorno padrão raiz
            $arrFinalRetorno = [
                'strRota' => $rota,
                'strRotaCMP' => "/", //string facilitadora de comparação
                'arrPrmOrdem' => [], //Array que representa a ordem e tipo dos parâmetros da rota.
                'arrPrmDinam' => [] //Array de facilitaçao dos parâmetros dinâmicos esperados
            ];

            //Significa que foi encontrada uma rota raiz (/)
            if($rota == ""){
                $arrFinalRetorno['strRota'] = '/';
                return $arrFinalRetorno;
            }
                
            //Quebrando os parâmetros da rota.
            $expParametros = explode("/", $rota);
            if(empty($expParametros)){
                return false;
            }
            

            //Auxiliar do processamento de parâmetros
            $procParm;

            $strCMP = "/";

            //Contador de parâmetro
            $cntPrms = 0;

            foreach ($expParametros as $parametro) {

                //Detecção de duplicidade de barras(//)
                if($parametro == ""){
                    return false;       
                }
                
                $procParm = $this->_processarParmRotaIndividualmente($parametro);

                //Erro detectado no processamento individual
                if($procParm === false)
                    return false;

                //Verificando se é um parâmetro normal
                if($procParm === true){

                    $arrFinalRetorno['arrPrmOrdem'][] = [
                        'nm'    => $parametro, //Nome do parâmetro
                        't'     => 'n' //Tipo: Normal
                    ];

                    $strCMP .= $parametro."/"; //Concatenando str de comparação

                } else { //Verificando se é um parâmetro dinâmico
                    $arrFinalRetorno['arrPrmOrdem'][] = [
                        'nm'    => $procParm, //Nome do parâmetro
                        't'     => 'd' //Tipo: Dinâmico
                    ];

                    $strCMP .= "*/"; //Concatenando str de comparação

                    //Armazenando facilitador de parâmetro dinâmico
                    $arrFinalRetorno['arrPrmDinam'][ $cntPrms ] = $procParm;
                }

                $cntPrms++;
            }

            //Removendo a última barra "/"
            $strCMP = substr($strCMP, 0, -1);

            //Atualizando string de comparação
            $arrFinalRetorno['strRotaCMP'] = $strCMP;

            return $arrFinalRetorno;

        }

        private function _definirRotaMetodo(string $metodo, string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial, $argumento, string $linkInclusao = null) : void {
            
            //A rota não pode ser em braco
            if($rota == "")
               $this->_encaminharErroDefinicoes("A definição da rota está nula. #5343");
            
            //Verificando preliminarmente a consistência da rota.
            if(!$this->_verificarSeEstruturaRotaEValida($rota))
                $this->_encaminharErroDefinicoes("A definição da rota (".$rota.") é inválida. #12452");
            
    
            //Processando parâmetros internos dentro da rota.
            $retOperProcRota = $this->_processarParametrosRota($rota);

            if($retOperProcRota === false)
                $this->_encaminharErroDefinicoes("A definição da rota (".$rota.") é inválida. #8481");

            //Verificando se existe duplicidade de rota            
            if($this->_detectarDuplicidadeEmRotasDefinidas($metodo, $retOperProcRota['strRotaCMP']))
                $this->_encaminharErroDefinicoes("Erro a rota (".$rota.") está duplicada #151818");



            //Anexando dados do controlador.
            $retOperProcRota['nmClsContr'] =    $nomeClasseControlador; //Nome da classe do contralador
            $retOperProcRota['mtdIniCall'] =    $nomeMetodoChamadoInicial; //Nome do método incial de ataque
            $retOperProcRota['linkInc'] =       $linkInclusao; //Endereço de inclusão script
            $retOperProcRota['arg'] =           $argumento; //Argumento passado
            


            //Armazenando estrutura rrocessada no array principal de trabalho
            $this->arrayRotasConfigs[ $metodo ][ $retOperProcRota['strRotaCMP'] ] = $retOperProcRota;
            
        }

        private function _obterRotaRequisitada() : string{

            
            if(isset($_SERVER['REDIRECT_URL'])){

                $rota = $_SERVER['REDIRECT_URL'];
                $len = strlen($rota);
              
                //Removendo barra do final caso assim possua
                if($rota[$len-1] == "/"){
                    $rota = substr($rota, 0, -1);
                }

                return $rota;

            } else {
                //Caso não possua redirecionamento fica entendido que foi acessado a raiz do sistema
                return "/";
            }

        }

        private function _compararArrayReqComArrayRota(array &$arrayReqExp, array &$arrayRotaExp){
            
            $cntParms = 0;

            $arrayRetFinal = [];
            
            foreach ($arrayRotaExp as $elemRota) {
                
                //O argumento da requisição tem que existir
                if(!isset($arrayReqExp[$cntParms]))
                    return false;

                //Descobrindo se o argumento respectivo trabalhado é uma do tipo dinâmico ou normal
                if($elemRota['t'] == 'n'){
                    if($elemRota['nm'] !== $arrayReqExp[$cntParms]){
                        return false; //Não bate com a estrutura da rota.
                    }
                } else {
                    $arrayRetFinal[ $cntParms ] = $arrayReqExp[$cntParms];
                }

                $cntParms++;
            }
            
            //Evitando que a requisição enviada seja maior do que a rota definida
            if(isset($arrayReqExp[$cntParms])){
                return false;
            }

            return $arrayRetFinal;

        }

        private function _localizarRotaNoArrayDefinicoes(string $metodo, string $rota){
            
            //Se não enviar nada então ouve não precisa prosseguir
            if($rota == "")
                return false;

            //Por definição deste processo a rota deve começar com "/"
            if($rota[0] != "/")
                return false;
            
            //Removendo primeiro carac para regularizar o explode
            $rota = substr($rota, 1);

            //Evitando um array de string vazia após o explode
            if(strlen($rota) > 0){
                //Explosão de rota.
                $explRotaReq = explode("/", $rota);
            } else {
                $explRotaReq = [];
            }            

            //==== Buscando rota em um método específico configurado ================
            
            //Auxiliar de resultado.
            $auxRes;

            if(isset($this->arrayRotasConfigs[$metodo])){
                
                foreach ($this->arrayRotasConfigs[$metodo] as $key => $elemRota) {

                    $auxRes = $this->_compararArrayReqComArrayRota($explRotaReq, $this->arrayRotasConfigs[$metodo][$key]['arrPrmOrdem']);

                    if($auxRes !== false){
                        
                        //Enviando retorno com o achado
                        return [
                            'mtd' => $metodo,   # Método
                            'key' => $key,      # Chave do array
                            'vDnm' => $auxRes   # Valores capturados dinâmicamente se assim tiver
                        ];

                        break;
                    }

                }

            }

            //==== Buscando rota em definições que aceitam todos os métodos ================

            if(isset($this->arrayRotasConfigs[ $this->CFGS['mtdsALL'] ])){
                
                foreach ($this->arrayRotasConfigs[ $this->CFGS['mtdsALL'] ] as $key => $elemRota) {

                    $auxRes = $this->_compararArrayReqComArrayRota($explRotaReq, $this->arrayRotasConfigs[$this->CFGS['mtdsALL']][$key]['arrPrmOrdem']);

                    if($auxRes !== false){             

                        //Enviando retorno com o achado
                        return [
                            'mtd' => $this->CFGS['mtdsALL'],    # Método
                            'key' => $key,                      # Chave do array
                            'vDnm' => $auxRes                   # Valores capturados dinâmicamente se assim tiver
                        ];

                        break;
                    }

                }

            }

            return false;
        }

        private function _acionarControlador(array $arrayInfsRota){
            
            //Muito improvável de ocorrer
            if(!isset($this->arrayRotasConfigs[ $arrayInfsRota['mtd'] ]) || !isset($this->arrayRotasConfigs[ $arrayInfsRota['mtd'] ] [$arrayInfsRota['key']]))
                throw new ProcessamentoRotas\Exceptions\PcRException("Falha interna!", 1010);
            
            $arrayRota = &$this->arrayRotasConfigs[ $arrayInfsRota['mtd'] ] [$arrayInfsRota['key']];
            
            //Verificando se é necessário incluir algum arquivo de controlador.
            if($arrayRota['linkInc']){
                if(!@include_once($arrayRota['linkInc'])){
                    throw new ProcessamentoRotas\Exceptions\PcRException("Falha interna! - Link de inclusão inválido", 2020);
                }
            }
            
            $classNameContld = $arrayRota['nmClsContr'];

            //Verificando se classe configurada para respectivo controlador existe
            if(!class_exists($classNameContld)){
                throw new ProcessamentoRotas\Exceptions\PcRException("Falha interna! - A classe ".$arrayRota['nmClsContr']." não foi encontrada", 5977);
            }
            
            /**
             * Função utilizada para formatar os parâmetros dinâmicos
             */
            $formatarParDinamicos = function($arrayValoresDinamicos) use ($arrayRota) : array{
                
                //Array de retorno final
                $arrRetFinal = [];
                
                foreach ($arrayValoresDinamicos as $key => $valor) {
                    
                    //Verificando se o indice existe na estrutura de rotas definidas
                    if(array_key_exists($key, $arrayRota['arrPrmDinam'])){

                        $arrRetFinal[] = [
                            'tag' => $arrayRota['arrPrmDinam'][ $key ],
                            'val' => $valor
                        ];

                    }

                }
                
                return $arrRetFinal;
            };

            //Instanciando e configurando objeto Request
            $objRequest = new \Sistema\ProcessamentoRotas\Request();

            $objRequest->metodo =       $arrayInfsRota['mtd'];
            $objRequest->strLinkReq =   $arrayRota['strRota'];
            $objRequest->argsLink =     $formatarParDinamicos($arrayInfsRota['vDnm']); //configurar
            $objRequest->argsMetodos =  ""; //configurar
            $objRequest->argRotaRAW =   $arrayRota['arg'];
            #---------------
            
            
            //Instanciando classe respectiva. Neste caso a chamando dinâmicamente
            $objClasseContld = new $classNameContld( $objRequest );

            //Nome método de ataque
            $nomeMetdoAtaque = $arrayRota['mtdIniCall'];

            if($nomeMetdoAtaque){
                
                //Verificando se o método de ataque existe na classe instanciada
                if(method_exists(get_class($objClasseContld), $nomeMetdoAtaque)){

                    //Invocando método de ataque
                    $objClasseContld->$nomeMetdoAtaque();

                } else {
                    throw new ProcessamentoRotas\Exceptions\PcRException("Falha interna! - A classe ".$arrayRota['nmClsContr']." não possui o método de ataque ".$arrayRota['mtdIniCall']."()", 25158);
                }
                    
            }
            //
           
        }

        function __construct(){
            $this->_setConfiguracoes();
        }

        //----------Métodos Públicos -----------------------------------------------

        # Métodos para configurações de rotas.
        public function definirRota_GET(string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial, $argumento, string $linkInclusao = null) : void {

            $this->_definirRotaMetodo("GET", $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $argumento, $linkInclusao);

        }

        public function definirRota_POST(string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial, $argumento, string $linkInclusao = null) : void {

            $this->_definirRotaMetodo("POST", $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $argumento, $linkInclusao);

        }

        public function definirRota_PUT(string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial, $argumento, string $linkInclusao = null) : void {

            $this->_definirRotaMetodo("PUT", $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $argumento,  $linkInclusao);

        }

        public function definirRota_DELETE(string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial = null, $argumento = null, string $linkInclusao = null) : void {

            $this->_definirRotaMetodo("DELETE", $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $argumento,  $linkInclusao);

        }

        public function definirRota_PATCH(string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial = null, $argumento = null, string $linkInclusao = null) : void {

            $this->_definirRotaMetodo("PATCH", $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $argumento,  $linkInclusao);

        }

        public function definirRota_TODOS(string $rota, string $nomeClasseControlador, string $nomeMetodoChamadoInicial = null, $argumento = null, string $linkInclusao = null) : void {

            $this->_definirRotaMetodo($this->CFGS['mtdsALL'], $rota, $nomeClasseControlador, $nomeMetodoChamadoInicial, $argumento,  $linkInclusao);

        }
        //------------------------------------

        public function iniciarProcessamento(){
    
            //Obtendo método da requisição
            $metodoRequest = $_SERVER['REQUEST_METHOD'];

            if(!in_array($metodoRequest, $this->CFGS['mtdsAceitos'], true)){
                http_response_code(400);
                exit;
            }

            //Obtendo rota requisitada
            $rotaRequisitada = $this->_obterRotaRequisitada();

            //Buscando rota no sistema
            $retArrInfLocRota = $this->_localizarRotaNoArrayDefinicoes($metodoRequest, $rotaRequisitada);

            //Rota não encontrada
            if($retArrInfLocRota === false){ 
                http_response_code(404);
                exit;
            }

            try {
                
                //Acionando controlador responsável
                $this->_acionarControlador($retArrInfLocRota);       

            } catch (ProcessamentoRotas\Exceptions\PcRException $e) {
                http_response_code(500);
                exit;
            }
                    

        }

    }

}

namespace Sistema\ProcessamentoRotas{

    
    class Request {
        
        public $metodo;         //Método que foi realizado a requisição. Ex: GET,POST,...
        public $strLinkReq;     //String que contem a requisição
        public $argsLink;       //Argumentos passado pelo link
        public $argsMetodos;    //Argumentos passados pelo método.
        public $argRotaRAW;     //Argumento passado de forma sem ser padrão a rota;

    }
    
}

?>