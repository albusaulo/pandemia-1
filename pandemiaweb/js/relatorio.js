$("#vacinas").on('click', function () {
    $("#listarTabela").html("");
    var x = 'vacina';
    var url = "../../pandemiarest/Pandemia.php/pessoa/relatorios?" + $.param({'data': x});
    ajaxRelatorios(url);
});

$("#remedios").on('click', function () {
    $("#listarTabela").html("");
    var x = 'remedio';
    var url = "../../pandemiarest/Pandemia.php/pessoa/relatorios?" + $.param({'data': x});
    ajaxRelatorios(url);
});


