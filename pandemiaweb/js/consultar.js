$("#buttonBuscar").on('click', function () {
    var x = $("#buscarPessoa").serializeObject();
    var url = "../../pandemiarest/Pandemia.php/pessoa?" + $.param({ 'data': x });
    ajaxGet(url);
});