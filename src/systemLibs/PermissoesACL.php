<?php

namespace Sistema {
    
    class PermissoesACL {

        //Array com o objeto de permissões
        private $arrObjsACL_PERMS;
        private $regraPadrao; //Regra padrão para o calculo de permissões. 1 = Permitir, 0 = negar

        private $stsConfigurado; //Para saber o estado do objeto perante às suas configurações.  true = Configurado, false = em aberto.


        function __construct(){
            
            //Iniciando
            $this->arrObjsACL_PERMS = [];

            //Configurações iniciais
            $this->regraPadrao = 1;

            $this->stsConfigurado = false;
        }

        //Verificando o estado da permissão. true = permitido, false = não permitido, null = não foi possível verificar
        public function verificarEstadoPermissao(PermissoesACL\ACL_PERM $obj){

            //Caso os dados de alimentação sejam inválidos
            if(!$obj->validarDados())
                return null;
            
            //Caso a regra padrão seja permitir
            if($this->regraPadrao == 1){
                if(isset($this->arrObjsACL_PERMS[ $obj->getCodigo() ])){
                    return true; //Permitido
                }
            } else { //Caso a regra padrão seja negar
                if(!isset($this->arrObjsACL_PERMS[ $obj->getCodigo() ])){
                    return true; //Permitido
                }
            }

            return false;

        }



        
        //Adicionar objeto ao array geral
        public function addObj_ACL_PERM(PermissoesACL\ACL_PERM $obj) : bool{

            //Certificando que o objeto esteja em aberto
            if($this->stsConfigurado)
                return false;

            if(!$obj->validarDados())
                return false;

            
            //Inserindo objeto no array geral.
            $this->arrObjsACL_PERMS[ $obj->getCodigo() ] = $obj;

            return true;
        }

        //Define a regra padrão como permitir
        public function definirRegraPadComo_permtir() : void{

            //Certificando que o objeto esteja em aberto
            if($this->stsConfigurado)
                return;

            $this->regraPadrao = 1;
        }

        //Define a regra padrão como negar
        public function definirRegraPadComo_negar() : void{

            //Certificando que o objeto esteja em aberto
            if($this->stsConfigurado)
                return;

            $this->regraPadrao = 0;
        }

        public function finalizarConfigs() : void{
            $this->stsConfigurado = true;
        }

    }

}

namespace Sistema\PermissoesACL {
    

    class ACL_PERM {

        private $codigo;
        private $descricao;

        //Verifica a cadeia de caractéres do código da permissão
        private function _verificarCadeiaCarac(string $str) : bool{

            //Aceita apenas letras(Maiu Minu), numeros, Caracteres (./-_)
            if (preg_match('/^[a-z0-9._\-\/]+$/i', $str)) {
                return true;
            } else {
                return false;
            }

        }

        function __construct(string $codigo, string $descricao = ""){
            $this->codigo = $codigo;
            $this->descricao = $descricao;
        }

        //Realiza a validação dos dados inseridos neste objeto.
        public function validarDados() : bool{
        
            if($this->codigo == "")
                return false;

            if(strlen($this->codigo > 64))
                return false;

            //Verificando cadeia de caracteres
            if(!$this->_verificarCadeiaCarac($this->codigo))
                return false;


            return true;
        }

        //Método público e estatico para a validação do código.
        public static function validarCodigo(string $codigo) : bool{
            return self::_verificarCadeiaCarac($codigo);
        }

        //Método mágico.
        /**
         * Método mágico de chamada do objeto na forma de string
         *
         * @return string
         */
        public function __toString() : string{
            return $this->codigo;        
        }
        
        #GETTERS
        /**
         * Obtenção do código do objeto
         *
         * @return string
         */
        public function getCodigo() : string{
            return $this->codigo;
        }
        /**
         * Obtenção da descrição do objeto
         *
         * @return string
         */
        public function getDescricao() : string{
            return $this->descricao;
        }
  
    }

}




?>