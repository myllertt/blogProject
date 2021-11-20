function confirmarExclusao(url){
    if(confirm("Você confirma realmente a exclusão desta postagem?"))
        window.location = url+"?_method=DELETE";
}