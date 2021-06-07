$("#buttonEnviar").on('click', function () {
    var x = $("#cadastroPessoa").serializeObject();
    var url = "../../pandemiarest/Pandemia.php/pessoa?" + $.param({ 'data': x });
    ajaxPost(url);
});