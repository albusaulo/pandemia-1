$(document).ready(function () {
	var header = $("#header");
	if (header.length) {
		$.ajax({
			'url': '_header.html',
			'async': false,
			'global': false,
			'success': function (data) {
				$("#header").html(data);
			}
		});
	}
})

$.fn.serializeObject = function () {
	var o = {};
	var a = this.serializeArray();
	$.each(a, function () {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

function ajaxPost(url) {
	loadStart();
	$.ajax({
		url: url,
		type: 'POST',
		success: function (response) {
			$('#listarTabela').html("");
			$('#mensagem').html('<div class="alert alert-success" role="alert">' + response["Mensagem"] + '</div>');
		},
		error: function (request, status, error) {
			$('#mensagem').html('<div class="alert alert-danger" role="alert">' + request.responseText + '</div>');
		},
		complete: function () {
			loadStop();
		}
	});
}

function ajaxGet(url) {
	loadStart();
	$.ajax({
		url: url,
		type: 'GET',
		success: function (response) {
			$('#mensagem').html('');
			if (response['status'] == 'pendente') {
				$('#listarTabela').html(
					$('<table>', { 'id': '#', 'class': 'table table-bordered' }).append(
						$('<thead>').append(
							$('<tr>').append(
								$('<th>', { 'scope': 'col' }).text('Id'),
								$('<th>', { 'scope': 'col' }).text('Nome'),
								$('<th>', { 'scope': 'col' }).text('Cpf'),
								$('<th>', { 'scope': 'col' }).text('Tipo'),
								$('<th>', { 'scope': 'col' }).text('Status'),
								$('<th>', { 'scope': 'col' }).text('Solicitação'),
								$('<th>', { 'scope': 'col' }).text('Dia Vacina'),
								$('<th>', { 'scope': 'col' }).text('Ação')
							)
						),
						$('<tbody>').append(
							$('<tr>', {'scope':'row'}).append(
								$('<td>').text(response['id']),
								$('<td>').text(response['name']),
								$('<td>').text(response['cpf']),
								$('<td>').text(response['type_job']),
								$('<td>').text(response['status']),
								$('<td>').text(response['created_at']),
								$('<td>').text(response['dayD']),
								$('<td>').append(
									$('<button>', {'type': 'button', 'id': 'buttonRecebido', 'class': 'btn btn-success btn-sm', 'dataId': '' + response['id']}).text('Recebido'),
									'<br><br>',
									$('<button>', {'type': 'button', 'id': 'buttonCancelar', 'class': 'btn btn-danger btn-sm', 'style': 'margin-top:5', 'dataId': '' + response['id']}).text('Cancelar'),
								)
							)
						),
					)
				);
			} else {
				$('#listarTabela').html('');
				$('#mensagem').html('<div class="alert alert-success" role="alert"> Sua Solicitação possui o status de ' +response["status"] + '</div>');
			}
		},
		error: function (request, status, error) {
			$('#mensagem').html('<div class="alert alert-danger" role="alert">' + request.responseText + '</div>');
		},
		complete: function () {
			loadStop();
		}
	});
}


function loadStart() {
	$(document).find('#modalContainer').addClass('modal-container');
	$(document).find('#loading').addClass('spinner');
}

function loadStop() {
	$(document).find('#modalContainer').removeClass('modal-container');
	$(document).find('#loading').removeClass('spinner');
}

$(document).on('click', '#buttonRecebido', function (event) {
	var data = [
		$(this).attr('dataId'),
		'recebido'
	];

	if(confirm("tem certeza?")){
		var url = "../../pandemiarest/Pandemia.php/pessoa/id?" + $.param({'data': data});
		ajaxPost(url);
	};
});

$(document).on('click', '#buttonCancelar', function (event) {
	var data = [
		$(this).attr('dataId'),
		'cancelado'
	];

	if(confirm("tem certeza?")){
		var url = "../../pandemiarest/Pandemia.php/pessoa/id?" + $.param({'data': data});
		ajaxPost(url);
	};
});

function loadingGrafico(response) {
	var pendentes = parseInt(response['pendentes']);
	var recebidos = parseInt(response['recebidos']);
	var cancelados = parseInt(response['cancelados']);
	google.charts.load("current", { packages: ['corechart'] });
	google.charts.setOnLoadCallback(drawChart);
	function drawChart() {
		var data = google.visualization.arrayToDataTable([
			["Element", "Density", { role: "style" }],
			["Pendentes", pendentes, "#6c757d"],
			["Recebidos", recebidos, "#007bff"],
			["Cancelados", cancelados, "red"]
		]);

		var view = new google.visualization.DataView(data);
		view.setColumns([0, 1,
			{
				calc: "stringify",
				sourceColumn: 1,
				type: "string",
				role: "annotation"
			},
			2]);
		var total = pendentes + recebidos + cancelados;
		var options = {
			title: "Total: " + total + " solicitações",
			width: 600,
			height: 500,
			bar: { groupWidth: "95%" },
			legend: { position: "none" },
		};
		var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
		chart.draw(view, options);
	}
}

function ajaxRelatorios(url) {
	$.ajax({
		url: url,
		type: 'GET',
		success: function (response) {
			$('#listarTabela').html(
				$('<table>', { 'id': 'tabelaEscola', 'class': 'table table-bordered' }).append(
					$('<thead>').append(
						$('<tr>').append(
							$('<th>', { 'scope': 'col' }).text('Pendentes'),
							$('<th>', { 'scope': 'col' }).text('Recebidos'),
							$('<th>', { 'scope': 'col' }).text('Cancelados')
						)
					),
                    $('<tbody>').append(
                        $('<tr>', {'scope':'row'}).append(
                            $('<td>').text(response['pendentes']),
                            $('<td>').text(response['recebidos']),
                            $('<td>').text(response['cancelados'])
                        )
					),
				)
			);
			loadingGrafico(response);
		},
		error: function (request, status, error) {
			$('#mensagem').html('<div class="alert alert-danger" role="alert">' + request.responseText + '</div>');
		},
		complete: function () {
			loadStop();
		}
	});
}