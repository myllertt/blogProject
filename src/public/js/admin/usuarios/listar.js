function confirmarExclusao(url){
    if(confirm("Você confirma realmente a exclusão deste usuário?"))
        window.location = url+"?_method=DELETE";
}