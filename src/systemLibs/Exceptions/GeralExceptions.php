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

?>