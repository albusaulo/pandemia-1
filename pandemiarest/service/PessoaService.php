<?php

namespace Pandemia\Service;

use Exception;
use Pandemia\Dao\JobDao;
use Pandemia\Dao\PessoaDao;
use Pandemia\Utils\Persistence;
use Pandemia\Utils\Utils;

class PessoaService extends Service
{

	private PessoaDao $pessoaDao;
	private JobDao $jobDao;

	public function __construct()
	{
		$this->pessoaDao = new PessoaDao();
		$this->jobDao = new JobDao();
	}

	/**
	 * Salva Pessoa e Job, transaction so é realizada
	 * completamente, se e somente se, todas as inserções obtiverem success.
	 *
	 * @param array $dados(Pessoa e Job)
	 *
	 * @return String mensagem o dia agendado da vacina
	 */
	public function save(?array $dados)
	{
		try {
			Persistence::transactionBegin();
			$pessoaParsed = $this->parsePessoa($dados);
			$idPessoa = $this->pessoaDao->create($pessoaParsed, false, false);

			$dayD = $this->listQntJob($dados['exampleRadios']);
			$jobParsed = $this->parseJob($dados, $idPessoa, $dayD['dayD']);
			$this->saveJob($jobParsed);

			Persistence::transactionCommit();
			return $dayD['mensagem'];
		} catch (Exception $ex) {
			Persistence::transactionRollback();
			return false;
		}
	}

	public function saveJob(?array $dados)
	{
		return $this->jobDao->create($dados, true, false);
	}

	private function parsePessoa(?array $dados)
	{
		if (empty($dados)) {
			return null;
		}
		unset($dados['exampleRadios']);
		return $dados;
	}

	private function parseJob(?array $dados, int $id, String $dayD)
	{
		if (empty($dados)) {
			return null;
		}
		unset($dados['name']);
		unset($dados['cpf']);

		$dados['type_job'] = $dados['exampleRadios'];
		unset($dados['exampleRadios']);
		$dados['id_person'] = $id;
		$dados['status'] = "pendente";

		$dados['dayD'] = Utils::dateTime($dayD, "d/m/Y", "Y-m-d");

		return $dados;
	}

	public function findByCpf(String $cpf)
	{
		return $this->pessoaDao->readByKeys(['cpf' => $cpf], ['cpf']);
	}

	/**
	 * Calculo data da vacina.
	 *
	 * @param string $type(vacina ou remedios)
	 *
	 * @return array $dados(mensagem a ser exibir na tela para o usuario e o dia da vacina)
	 */
	public function listQntJob(String $type)
	{
		$qntVacinas = $this->jobDao->listJobDaoPendentes($type);

		if ($qntVacinas['count(*)'] <= 4) {
			$dateVacina = date('d/m/Y');
		} else {
			$days = $qntVacinas['count(*)'] / 4;
			$days = round($days, 0);

			$dateVacina = date('d/m/Y', strtotime('+' . $days  . ' days'));
		}

		$dados = [
			'mensagem' => "Existem " . $qntVacinas['count(*)'] . " solicitacões de " . $type . "s no sistema, devido a isto a previsão para realização da sua solicitacão será no dia " . $dateVacina . ".",
			'dayD' => $dateVacina
		];

		return $dados;
	}

	public function listPersonAndJob($cpf)
	{
		$dados = $this->pessoaDao->listPersonAndJobDao($cpf);
		$dadosParsed = $this->getParsedJob($dados);

		return $dadosParsed;
	}

	private function getParsedJob(?array $dados)
	{
		$dados['created_at'] = Utils::dateTime($dados['created_at'], "Y-m-d H:i:s", "d/m/Y H:i:s");
		$dados['dayD'] = Utils::dateTime($dados['dayD'], "Y-m-d", "d/m/Y");

		return $dados;
	}

	public function updateStatusJob(?array $data)
	{
		$dataParsed = $this->updateParseJob($data);

		return $this->jobDao->update($dataParsed, true);
	}

	private function updateParseJob(?array $data)
	{
		$dados['id'] = $data['data'][0];
		unset($data['data'][0]);

		$dados['status'] = $data['data'][1];
		unset($data['data'][1]);

		return $dados;
	}

	/**
	 * Busca dinamicamente, quantitativo de vacina ou remedios.
	 *
	 * @param string $type(vacina ou remedios)
	 *
	 * @return array $dados(quantitativo)
	 */
	public function relatorio(String $type)
	{
		$pendentes = $this->jobDao->listJobDaoPendentes($type);
		$recebidos = $this->jobDao->listJobDaoRecebido($type);
		$cancelados = $this->jobDao->listJobDaoCancelado($type);

		$dados = [
			'pendentes' => $pendentes['count(*)'],
			'recebidos' => $recebidos['count(*)'],
			'cancelados' => $cancelados['count(*)']
		];

		return $dados;
	}
}
