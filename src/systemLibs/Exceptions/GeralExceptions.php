<?php
/*
  De uma forma geral, todas as exceções estaram disponíveis neste arquivo
*/

# Exceção das Views
namespace Sistema\Views\Exceptions {

    //Necessário para diferenciar os erros específicos dos demais erros
    class VwException extends \Exception{
	
        public function __construct(string $msgPublica=null, int $codigo=null, $previous = null) {	
            
            parent::__construct($msgPublica, $codigo, $previous);
            
        }
    }

}


# Exceção do Processamento de rotas
namespace Sistema\ProcessamentoRotas\Exceptions{

    //Necessário para diferenciar os erros específicos dos demais erros
    class PcRException extends \Exception{
	
        public function __construct(string $msgPublica=null, int $codigo=null, $previous = null) {	
            
            parent::__construct($msgPublica, $codigo, $previous);
            
        }
    }

}


# Exceção do Processamento de rotas
namespace Sistema\DB\Exceptions{

    //Necessário para diferenciar os erros específicos dos demais erros
    class DBException extends \Exception{

        private $privateMessage; //Mensagem privada.
	
        public function __construct(string $message=null, int $code=null, string $privateMessage=null, $previous = null) {	
            
            parent::__construct($message, $code, $previous);

            $this->privateMessage = $privateMessage;
            
        }

        //Obter a msg privada. Que normalmente é o próprio retorno de erro do db.
        public function getPrivateMessage(){
            return $this->privateMessage;
        }

        public function debug(){ //Utilizado para debugar o erro.
            echo "==DEBUG(".get_class($this).")============<br>";
            echo "==<strong>Msg Publica:</strong> (".$this->getMessage().")<br><br>";
            echo "==<strong>Msg Privada:</strong> (".$this->getPrivateMessage().")<br><br>";
            echo "==<strong>Código:</strong> (".$this->getCode().")<br>";
            echo "=========================================";
        }
    }


}


?>