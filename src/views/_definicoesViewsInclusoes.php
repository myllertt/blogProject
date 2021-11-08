<?php

use Sistema\Views\VwException; 
use Sistema\Views\Views; 

/*
    Script responsável por associar os Id de chamadas das views com os seus arquivos respectivos presentes no diretório
*/

try {

    # Iniciando definições
    #Views::definir("idView", "LocalArquivo");

//CONFIGURAR AQUI----------------------------------------------------------------------------------------------

    #Pagina inicial
    Views::definir("home.index", __DIR_VIEWS__."/home/index.view.php");    


//-------------------------------------------------------------------------------------------------
    //Finalizando definições. Desta forma evita que as definições de views possam ser alteradas em tempo de execução
    Views::finalizarModificacoes();

} catch(VwException $e){
    die("Falha na definição das Views");
}

?>