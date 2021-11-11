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
    Views::definir("site.index", __DIR_VIEWS__."/site/index.view.php");     
        #Layouts home
        Views::definir("site.layout.cabecalho", __DIR_VIEWS__."/site/layout/cabecalho.view.php");
        Views::definir("site.layout.rodape", __DIR_VIEWS__."/site/layout/rodape.view.php");  
        
    #Definição Posts
    #Visualizar um post
    Views::definir("site.posts.post", __DIR_VIEWS__."/site/posts/post.view.php");     
    
    

    #Definição de erros.
    Views::definir("errosGerais.ErroDB", __DIR_VIEWS__."/errosGerais/erroDB.view.php"); 


//-------------------------------------------------------------------------------------------------
    //Finalizando definições. Desta forma evita que as definições de views possam ser alteradas em tempo de execução
    Views::finalizarModificacoes();

} catch(VwException $e){
    die("Falha na definição das Views");
}

?>