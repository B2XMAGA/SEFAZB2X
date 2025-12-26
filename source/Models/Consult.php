<?php

namespace Source\Models;

use Source\Conn\DataLayer;
use Source\Facades\Consult as Search;

class Consult extends DataLayer
{


    public function searchNFCe()
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();


        $fieldsRequired = ['link'];
		$url = $postVars['link'];
		
		if (!filter_var($url, FILTER_VALIDATE_URL)) {
			$this->call(
                '401',
                'Ops',
                '',
                "ops",
                "URL inválida"
            )->back(["count" => 0]);
            return;
		}

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if (in_array('', $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não podem ter campos em branco"
            )->back(["count" => 0]);
            return;
        }
		
		$host = parse_url($url, PHP_URL_HOST);
		
		if($host == 'sat.sef.sc.gov.br'){
			
			try{
				$doc = new \DOMDocument();
				libxml_use_internal_errors(true); // Suprime warnings ao carregar HTML
				$doc->loadHTML(file_get_contents($url));
				libxml_clear_errors();

				$data = [];

				// Captura as informações principais
				$emitenteDiv = $doc->getElementById("conteudo");
				if ($emitenteDiv) {
					$divs = $emitenteDiv->getElementsByTagName("div");
					$emitenteNome = $doc->getElementById("u20");;
					$emitenteCnpjIe = trim($divs[1]->nodeValue ?? '');

					// Capturar CNPJ usando regex
					preg_match('/CNPJ:\s*([\d\.\/-]+)/', $emitenteCnpjIe, $cnpjMatch);
					$docEmitente = $cnpjMatch[1] ?? '';

					// Adicionar ao JSON
					$data['emitente'] = [
						'doc' => $docEmitente,
						'nome' => $emitenteNome->nodeValue,
						'ie' => '', // Ajuste caso a IE seja dinâmica
						'uf' => 'SC' // Ajuste caso o estado seja dinâmico
					];
				}

				// Captura dados da nota fiscal
				$infoGeralDiv = $doc->getElementById("infos");
				if ($infoGeralDiv) {
					$collapsibles = $infoGeralDiv->getElementsByTagName("div");
					foreach ($collapsibles as $div) {
						if (strpos($div->nodeValue, 'Número:') !== false) {
							preg_match('/Número:\s*(\d+)/', $div->nodeValue, $matches);
							$numero = $matches[1] ?? null;

							preg_match('/Série:\s*(\d+)/', $div->nodeValue, $matches);
							$serie = $matches[1] ?? null;

							preg_match('/Emissão:\s*(.+)- Via/', $div->nodeValue, $matches);
							$emissao = $matches[1] ?? null;

							$data['numero'] = $numero;
							$data['serie'] = $serie;
							$data['data_hora_emissao'] = $emissao . '-03:00';
							break;
						}
					}
				}

				// Captura valor total
				$totalNotaDiv = $doc->getElementById("totalNota");
				if ($totalNotaDiv) {
					$totalLabels = $totalNotaDiv->getElementsByTagName("div");
					foreach ($totalLabels as $label) {
						if (strpos($label->nodeValue, 'Valor a pagar R$:') !== false) {
							preg_match('/Valor a pagar R\$:\s*([\d,]+)/', $label->nodeValue, $matches);
							$data['valor_total'] = str_replace(',', '.', $matches[1] ?? '0.00');
							break;
						}
					}
				}

				// Captura itens
				$data['itens'] = [];
				$table = $doc->getElementById("tabResult");
				if ($table) {
					$rows = $table->getElementsByTagName("tr");

					foreach ($rows as $row) {
						$tds = $row->getElementsByTagName("td");
						if ($tds->length > 0) {
							// Capturar os dados relevantes
							$descricao = trim($tds->item(0)->getElementsByTagName("span")->item(0)->nodeValue ?? '');
							$quantidade = trim(str_replace('Qtde.:', '', $tds->item(0)->getElementsByTagName("span")->item(2)->nodeValue ?? ''));
							$unidade = trim(str_replace('UN: ', '', $tds->item(0)->getElementsByTagName("span")->item(3)->nodeValue ?? ''));
							$valor_unitario = trim(str_replace('Vl. Unit.:', '', $tds->item(0)->getElementsByTagName("span")->item(4)->nodeValue ?? ''));
							$valor_total = trim($tds->item(1)->getElementsByTagName("span")->item(0)->nodeValue ?? '');

							// Adicionar ao array de itens
							$data['itens'][] = [
								"descricao" => $descricao,
								"quantidade" => $quantidade,
								"unidade" => $unidade,
								"valor_unitario" => $valor_unitario,
								"valor_total" => $valor_total
							];
						}
					}
				}
			}catch(\Exception $e){
				$this->call(
					'401',
					'Ops',
					'',
					"ops",
					$e->getMessage()
				)->back(["count" => 0]);
				return;
			}
			
			
			$this->call(
				'200',
				'Sucesso',
				'',
				"ok",
				"Operação realizada com sucesso"
			)->back(["return" => $data]);
			return;
		}
		
		if($host == 'www.fazenda.pr.gov.br'){
			try{
				$doc = new \DOMDocument();
				libxml_use_internal_errors(true); // Suprime warnings ao carregar HTML
				$doc->loadHTML(file_get_contents($url));
				libxml_clear_errors();

				$data = [];

				// Captura as informações principais
				$emitenteDiv = $doc->getElementById("conteudo");
				if ($emitenteDiv) {
					$divs = $emitenteDiv->getElementsByTagName("div");
					$emitenteNome = $doc->getElementById("u20");;
					$emitenteCnpjIe = trim($divs[1]->nodeValue ?? '');

					// Capturar CNPJ usando regex
					preg_match('/CNPJ:\s*([\d\.\/-]+)/', $emitenteCnpjIe, $cnpjMatch);
					$docEmitente = $cnpjMatch[1] ?? '';

					// Adicionar ao JSON
					$data['emitente'] = [
						'doc' => $docEmitente,
						'nome' => $emitenteNome->nodeValue,
						'ie' => '', // Ajuste caso a IE seja dinâmica
						'uf' => 'PR' // Ajuste caso o estado seja dinâmico
					];
				}

				// Captura dados da nota fiscal
				$infoGeralDiv = $doc->getElementById("infos");
				if ($infoGeralDiv) {
					$collapsibles = $infoGeralDiv->getElementsByTagName("div");
					foreach ($collapsibles as $div) {
						if (strpos($div->nodeValue, 'Número:') !== false) {
							preg_match('/Número:\s*(\d+)/', $div->nodeValue, $matches);
							$numero = $matches[1] ?? null;

							preg_match('/Série:\s*(\d+)/', $div->nodeValue, $matches);
							$serie = $matches[1] ?? null;

							preg_match('/Emissão:\s*(.+)- Via/', $div->nodeValue, $matches);
							$emissao = $matches[1] ?? null;

							$data['numero'] = $numero;
							$data['serie'] = $serie;
							$data['data_hora_emissao'] = $emissao . '-03:00';
							break;
						}
					}
				}

				// Captura valor total
				$totalNotaDiv = $doc->getElementById("totalNota");
				if ($totalNotaDiv) {
					$totalLabels = $totalNotaDiv->getElementsByTagName("div");
					foreach ($totalLabels as $label) {
						if (strpos($label->nodeValue, 'Valor a pagar R$:') !== false) {
							preg_match('/Valor a pagar R\$:\s*([\d,]+)/', $label->nodeValue, $matches);
							$data['valor_total'] = str_replace(',', '.', $matches[1] ?? '0.00');
							break;
						}
					}
				}

				// Captura itens
				$data['itens'] = [];
				$table = $doc->getElementById("tabResult");
				if ($table) {
					$rows = $table->getElementsByTagName("tr");

					foreach ($rows as $row) {
						$tds = $row->getElementsByTagName("td");
						if ($tds->length > 0) {
							// Capturar os dados relevantes
							$descricao = trim($tds->item(0)->getElementsByTagName("span")->item(0)->nodeValue ?? '');
							$quantidade = trim(str_replace('Qtde.:', '', $tds->item(0)->getElementsByTagName("span")->item(2)->nodeValue ?? ''));
							$unidade = trim(str_replace('UN: ', '', $tds->item(0)->getElementsByTagName("span")->item(3)->nodeValue ?? ''));
							$valor_unitario = trim(str_replace('Vl. Unit.:', '', $tds->item(0)->getElementsByTagName("span")->item(4)->nodeValue ?? ''));
							$valor_total = trim($tds->item(1)->getElementsByTagName("span")->item(0)->nodeValue ?? '');

							// Adicionar ao array de itens
							$data['itens'][] = [
								"descricao" => $descricao,
								"quantidade" => $quantidade,
								"unidade" => $unidade,
								"valor_unitario" => $valor_unitario,
								"valor_total" => $valor_total
							];
						}
					}
				}
			}catch(\Exception $e){
				$this->call(
					'401',
					'Ops',
					'',
					"ops",
					$e->getMessage()
				)->back(["count" => 0]);
				return;
			}
			
			
			$this->call(
				'200',
				'Sucesso',
				'',
				"ok",
				"Operação realizada com sucesso"
			)->back(["return" => $data]);
			return;
		}
		
		if($host == 'www.sefaz.rs.gov.br'){
			try{
				$doc = new \DOMDocument();
				libxml_use_internal_errors(true); // Suprime warnings ao carregar HTML
				$doc->loadHTML(file_get_contents($url));
				libxml_clear_errors();

				$data = [];

				// Captura as informações principais
				$emitenteDiv = $doc->getElementById("conteudo");
				if ($emitenteDiv) {
					$divs = $emitenteDiv->getElementsByTagName("div");
					$emitenteNome = $doc->getElementById("u20");;
					$emitenteCnpjIe = trim($divs[1]->nodeValue ?? '');

					// Capturar CNPJ usando regex
					preg_match('/CNPJ:\s*([\d\.\/-]+)/', $emitenteCnpjIe, $cnpjMatch);
					$docEmitente = $cnpjMatch[1] ?? '';

					// Adicionar ao JSON
					$data['emitente'] = [
						'doc' => $docEmitente,
						'nome' => $emitenteNome->nodeValue,
						'ie' => '', // Ajuste caso a IE seja dinâmica
						'uf' => 'RS' // Ajuste caso o estado seja dinâmico
					];
				}

				// Captura dados da nota fiscal
				$infoGeralDiv = $doc->getElementById("infos");
				if ($infoGeralDiv) {
					$collapsibles = $infoGeralDiv->getElementsByTagName("div");
					foreach ($collapsibles as $div) {
						if (strpos($div->nodeValue, 'Número:') !== false) {
							preg_match('/Número:\s*(\d+)/', $div->nodeValue, $matches);
							$numero = $matches[1] ?? null;

							preg_match('/Série:\s*(\d+)/', $div->nodeValue, $matches);
							$serie = $matches[1] ?? null;

							preg_match('/Emissão:\s*(.+)- Via/', $div->nodeValue, $matches);
							$emissao = $matches[1] ?? null;

							$data['numero'] = $numero;
							$data['serie'] = $serie;
							$data['data_hora_emissao'] = $emissao . '-03:00';
							break;
						}
					}
				}

				// Captura valor total
				$totalNotaDiv = $doc->getElementById("totalNota");
				if ($totalNotaDiv) {
					$totalLabels = $totalNotaDiv->getElementsByTagName("div");
					foreach ($totalLabels as $label) {
						if (strpos($label->nodeValue, 'Valor a pagar R$:') !== false) {
							preg_match('/Valor a pagar R\$:\s*([\d,]+)/', $label->nodeValue, $matches);
							$data['valor_total'] = str_replace(',', '.', $matches[1] ?? '0.00');
							break;
						}
					}
				}

				// Captura itens
				$data['itens'] = [];
				$table = $doc->getElementById("tabResult");
				if ($table) {
					$rows = $table->getElementsByTagName("tr");

					foreach ($rows as $row) {
						$tds = $row->getElementsByTagName("td");
						if ($tds->length > 0) {
							// Capturar os dados relevantes
							$descricao = trim($tds->item(0)->getElementsByTagName("span")->item(0)->nodeValue ?? '');
							$quantidade = trim(str_replace('Qtde.:', '', $tds->item(0)->getElementsByTagName("span")->item(2)->nodeValue ?? ''));
							$unidade = trim(str_replace('UN: ', '', $tds->item(0)->getElementsByTagName("span")->item(3)->nodeValue ?? ''));
							$valor_unitario = trim(str_replace('Vl. Unit.:', '', $tds->item(0)->getElementsByTagName("span")->item(4)->nodeValue ?? ''));
							$valor_total = trim($tds->item(1)->getElementsByTagName("span")->item(0)->nodeValue ?? '');

							// Adicionar ao array de itens
							$data['itens'][] = [
								"descricao" => $descricao,
								"quantidade" => $quantidade,
								"unidade" => $unidade,
								"valor_unitario" => $valor_unitario,
								"valor_total" => $valor_total
							];
						}
					}
				}
			}catch(\Exception $e){
				$this->call(
					'401',
					'Ops',
					'',
					"ops",
					$e->getMessage()
				)->back(["count" => 0]);
				return;
			}
			
			
			$this->call(
				'200',
				'Sucesso',
				'',
				"ok",
				"Operação realizada com sucesso"
			)->back(["return" => $data]);
			return;
		}
		
		if($host == 'app.sefaz.es.gov.br'){
			try{
				$doc = new \DOMDocument();
				libxml_use_internal_errors(true); // Suprime warnings ao carregar HTML
				$doc->loadHTML(file_get_contents($url));
				libxml_clear_errors();

				$data = [];

				// Captura as informações principais
				$emitenteDiv = $doc->getElementById("conteudo");
				if ($emitenteDiv) {
					$divs = $emitenteDiv->getElementsByTagName("div");
					$emitenteNome = $doc->getElementById("u20");;
					$emitenteCnpjIe = trim($divs[1]->nodeValue ?? '');

					// Capturar CNPJ usando regex
					preg_match('/CNPJ:\s*([\d\.\/-]+)/', $emitenteCnpjIe, $cnpjMatch);
					$docEmitente = $cnpjMatch[1] ?? '';

					// Adicionar ao JSON
					$data['emitente'] = [
						'doc' => $docEmitente,
						'nome' => $emitenteNome->nodeValue,
						'ie' => '', // Ajuste caso a IE seja dinâmica
						'uf' => 'ES' // Ajuste caso o estado seja dinâmico
					];
				}

				// Captura dados da nota fiscal
				$infoGeralDiv = $doc->getElementById("infos");
				if ($infoGeralDiv) {
					$collapsibles = $infoGeralDiv->getElementsByTagName("div");
					foreach ($collapsibles as $div) {
						if (strpos($div->nodeValue, 'Número:') !== false) {
							preg_match('/Número:\s*(\d+)/', $div->nodeValue, $matches);
							$numero = $matches[1] ?? null;

							preg_match('/Série:\s*(\d+)/', $div->nodeValue, $matches);
							$serie = $matches[1] ?? null;

							preg_match('/Emissão:\s*(.+)- Via/', $div->nodeValue, $matches);
							$emissao = $matches[1] ?? null;

							$data['numero'] = $numero;
							$data['serie'] = $serie;
							$data['data_hora_emissao'] = $emissao . '-03:00';
							break;
						}
					}
				}

				// Captura valor total
				$totalNotaDiv = $doc->getElementById("totalNota");
				if ($totalNotaDiv) {
					$totalLabels = $totalNotaDiv->getElementsByTagName("div");
					foreach ($totalLabels as $label) {
						if (strpos($label->nodeValue, 'Valor a pagar R$:') !== false) {
							preg_match('/Valor a pagar R\$:\s*([\d,]+)/', $label->nodeValue, $matches);
							$data['valor_total'] = str_replace(',', '.', $matches[1] ?? '0.00');
							break;
						}
					}
				}

				// Captura itens
				$data['itens'] = [];
				$table = $doc->getElementById("tabResult");
				if ($table) {
					$rows = $table->getElementsByTagName("tr");

					foreach ($rows as $row) {
						$tds = $row->getElementsByTagName("td");
						if ($tds->length > 0) {
							// Capturar os dados relevantes
							$descricao = trim($tds->item(0)->getElementsByTagName("span")->item(0)->nodeValue ?? '');
							$quantidade = trim(str_replace('Qtde.:', '', $tds->item(0)->getElementsByTagName("span")->item(2)->nodeValue ?? ''));
							$unidade = trim(str_replace('UN: ', '', $tds->item(0)->getElementsByTagName("span")->item(3)->nodeValue ?? ''));
							$valor_unitario = trim(str_replace('Vl. Unit.:', '', $tds->item(0)->getElementsByTagName("span")->item(4)->nodeValue ?? ''));
							$valor_total = trim($tds->item(1)->getElementsByTagName("span")->item(0)->nodeValue ?? '');

							// Adicionar ao array de itens
							$data['itens'][] = [
								"descricao" => $descricao,
								"quantidade" => $quantidade,
								"unidade" => $unidade,
								"valor_unitario" => $valor_unitario,
								"valor_total" => $valor_total
							];
						}
					}
				}
			}catch(\Exception $e){
				$this->call(
					'401',
					'Ops',
					'',
					"ops",
					$e->getMessage()
				)->back(["count" => 0]);
				return;
			}
			
			
			$this->call(
				'200',
				'Sucesso',
				'',
				"ok",
				"Operação realizada com sucesso"
			)->back(["return" => $data]);
			return;
		}
		
		if($host == 'consultadfe.fazenda.rj.gov.br'){
			try{
				$doc = new \DOMDocument();
				libxml_use_internal_errors(true); // Evita warnings de HTML mal formado
				$doc->loadHTML(mb_convert_encoding(file_get_contents($url), 'HTML-ENTITIES', 'UTF-8'));
				libxml_clear_errors();

				$data = [];

				// Captura as informações do emitente
				$emitenteDiv = $doc->getElementById("conteudo");
				if ($emitenteDiv) {
					$divs = $emitenteDiv->getElementsByTagName("div");
					foreach ($divs as $div) {
						if (strpos($div->nodeValue, 'CNPJ:') !== false) {
							preg_match('/CNPJ:\s*([\d\.\/-]+)/', $div->nodeValue, $cnpjMatch);
							$docEmitente = $cnpjMatch[1] ?? '';
							break;
						}
					}

					$emitenteNome = $doc->getElementById("u20");
					$data['emitente'] = [
						'doc' => $docEmitente,
						'nome' => $emitenteNome ? trim($emitenteNome->nodeValue) : '',
						'ie' => '', // Ajuste caso a IE seja dinâmica
						'uf' => 'SC' // Ajuste caso o estado seja dinâmico
					];
				}

				// Captura dados da nota fiscal
				$infoGeralDiv = $doc->getElementById("infos");
				if ($infoGeralDiv) {
					$divs = $infoGeralDiv->getElementsByTagName("div");
					foreach ($divs as $div) {
						if (strpos($div->nodeValue, 'Número:') !== false) {
							preg_match('/Número:\s*(\d+)/', $div->nodeValue, $matches);
							$numero = $matches[1] ?? null;

							preg_match('/Série:\s*(\d+)/', $div->nodeValue, $matches);
							$serie = $matches[1] ?? null;

							preg_match('/Emissão:\s*(.+)- Via/', $div->nodeValue, $matches);
							$emissao = $matches[1] ?? null;

							$data['numero'] = $numero;
							$data['serie'] = $serie;
							$data['data_hora_emissao'] = $emissao . '-03:00';
							break;
						}
					}
				}

				// Captura valor total
				$totalNotaDiv = $doc->getElementById("totalNota");
				if ($totalNotaDiv) {
					$spans = $totalNotaDiv->getElementsByTagName("span");
					foreach ($spans as $span) {
						if (strpos($span->nodeValue, 'R$') !== false) {
							preg_match('/R\$:\s*([\d,]+)/', $span->nodeValue, $matches);
							$data['valor_total'] = str_replace(',', '.', $matches[1] ?? '0.00');
							break;
						}
					}
				}

				// Captura itens da nota
				$data['itens'] = [];
				$table = $doc->getElementById("tabResult");
				if ($table) {
					$rows = $table->getElementsByTagName("tr");
					foreach ($rows as $row) {
						$tds = $row->getElementsByTagName("td");
						if ($tds->length > 0) {
							$descricao = trim($tds->item(0)->getElementsByTagName("span")->item(0)->textContent ?? '');
							$quantidade = trim(str_replace('Qtde.:', '', $tds->item(0)->getElementsByTagName("span")->item(2)->textContent ?? ''));
							$unidade = trim(str_replace('UN: ', '', $tds->item(0)->getElementsByTagName("span")->item(3)->textContent ?? ''));
							$valor_unitario = trim(str_replace('Vl. Unit.:', '', $tds->item(0)->getElementsByTagName("span")->item(4)->textContent ?? ''));
							$valor_total = trim($tds->item(1)->getElementsByTagName("span")->item(0)->textContent ?? '');

							$data['itens'][] = [
								"descricao" => $descricao,
								"quantidade" => $quantidade,
								"unidade" => $unidade,
								"valor_unitario" => $valor_unitario,
								"valor_total" => $valor_total
							];
						}
					}
				}
			}catch(\Exception $e){
				$this->call(
					'401',
					'Ops',
					'',
					"ops",
					$e->getMessage()
				)->back(["count" => 0]);
				return;
			}
			
			
			$this->call(
				'200',
				'Sucesso',
				'',
				"ok",
				"Operação realizada com sucesso"
			)->back(["return" => $data]);
			return;
		}
		
		$this->call(
			'401',
			'Ops',
			'',
			"ops",
			"Estado ainda não implementado"
		)->back(["count" => 0]);
		return;

        

        

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }


    public function cnpj()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['cnpj'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if (in_array('', $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não podem ter campos em branco"
            )->back(["count" => 0]);
            return;
        }


        $consult = new Search(TOKEN_CNPJJA, URL_CNPJJA);


        $res = $consult->consultCnpj($postVars['cnpj']);

        if (isset($res['taxId'])) {
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => transformCnpj($res)]);
            return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

    public function protesto()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['cnpj'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if (in_array('', $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não podem ter campos em branco"
            )->back(["count" => 0]);
            return;
        }


        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.infosimples.com/api/v2/consultas/cenprot-sp/protestos?token=SwGTpEyIsRAU7GlVu1F3PN1Bf8CVPFoSllIZAPlZ&timeout=600&ignore_site_receipt=0&cnpj='.$postVars['cnpj'].'&cpf=',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        $res = json_decode($response, true);

        curl_close($curl);

        if (isset($res['code']) && $res['code'] == 200) {
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => $res['data']]);
            return;
        }

        if (isset($res['code']) && $res['code'] == 612) {
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "A consulta não retornou dados no site ou aplicativo de origem no qual a automação foi executada."
            )->back(["retorno" => $res['data']]);
            return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

    public function ie()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['cnpj'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if (in_array('', $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não podem ter campos em branco"
            )->back(["count" => 0]);
            return;
        }


        $consult = new Search(TOKEN_CNPJJA, URL_CNPJJA_IE);


        $res = $consult->consultIE($postVars['cnpj']);

        if (isset($res['taxId'])) {

            $arr['cnpj'] = $res['taxId'];
            $arr['uf'] = $res['originState'];
            if (count($res['registrations']) > '0') {
                for ($x = 0; $x < count($res['registrations']); $x++) {
                    $arr['registros'][$x]['uf'] = $res['registrations'][$x]['state'];
                    $arr['registros'][$x]['ie'] = $res['registrations'][$x]['number'];
                    $arr['registros'][$x]['habilitado'] = $res['registrations'][$x]['enabled'];
                }
            }

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => $arr]);
            return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

    public function cep($param)
    {
        $idReg = $this->checkToken();

        $consult = new Search(TOKEN_CNPJJA, URL_CEP);


        $res = $consult->consultCep($param['cep']);

        if (isset($res['error'])) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Tivemos um erro ao processar a request"
            )->back(["count" => 0]);
            return;
        }

        $this->call(
            '200',
            'Sucesso',
            '',
            "ok",
            "Operação realizada com sucesso"
        )->back(["retorno" => $res]);
        return;
    }

    public function searchNFe()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['chave', 'ideClient'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if (in_array('', $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não podem ter campos em branco"
            )->back(["count" => 0]);
            return;
        }


        $postChave = [
            'chave_ide' => hash('md5', date('YmdHis') . rand(1000, 9999)),
            'chave_id_company' => $idReg,
            'chave_key' => $postVars['chave'],
            'chave_ide_client' => $postVars['ideClient'],
            'chave_status' => '0',
            'chave_data_hora' => date('Y-m-d H:i:s')
        ];


        if ($this->db()->insert($postChave)->into('chave')) {
            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $postChave['chave_ide']]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }

    public function getNFe($param)
    {
        $idReg = $this->checkToken();

        $result = $this->db()->from('chave')
            ->where('chave_id_company')->is($idReg)
            ->andWhere('chave_ide')->is($param['ide'])
            ->orderBy('chave_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if (!$result) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Não encontramos o Ide informado"
            )->back(["count" => 0]);
            return;
        }

        if ($result) {
            foreach ($result as $r) {
                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "ok",
                    "Operação realizada com sucesso"
                )->back(["chave" => $r->chave_key, "xml" => $r->chave_xml]);
                return;
            }
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

	public function ecacCaixaPostal()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['pkcs12_cert', 'pkcs12_pass', 'perfil_procurador_cnpj', 'perfil_sucessora_sucedida_cnpj', 'ignora_nao_lidas', 'ignora_lidas'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }


        $consult = new Search(TOKEN_CNPJJA, URL_CNPJJA_IE);


        $res = $consult->ecacCaixaPostal($postVars['pkcs12_cert'], $postVars['pkcs12_pass'], $postVars['perfil_procurador_cnpj'], $postVars['perfil_sucessora_sucedida_cnpj'], $postVars['ignora_nao_lidas'], $postVars['ignora_lidas']);

		
		if (isset($res['code']) && $res['code'] == 200) {

			unset($res['data']['0']['site_receipt']);
            $arr = $res['data'];

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => $arr]);
            return;
        }

		if (isset($res['code']) && $res['code'] == 612) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Não encontrei os dados no ECAC"
			)->back(["count" => 0]);
			return;
        }

		if (isset($res['code']) && $res['code'] == 620) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Se o login estiver com duplo fator ativado infelizmente não consigo consultar"
			)->back(["count" => 0]);
			return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

	public function ecacComprovantePagamento()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['pkcs12_cert', 'pkcs12_pass', 'perfil_procurador_cnpj', 'data_inicio', 'data_fim', 'documento_numero'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }


        $consult = new Search(TOKEN_CNPJJA, URL_CNPJJA_IE);


        $res = $consult->ecacComprovantePagamento($postVars['pkcs12_cert'], $postVars['pkcs12_pass'], $postVars['perfil_procurador_cnpj'], $postVars['data_inicio'], $postVars['data_fim'], $postVars['documento_numero']);

		if (isset($res['code']) && $res['code'] == 200) {

			for($x=0;$x<count($res['data'][0]['documentos']);$x++){
				unset($res['data'][0]['documentos'][$x]['comprovante_url']);
				$arr[] = $res['data'][0]['documentos'][$x];
			}

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => $arr]);
            return;
        }

		if (isset($res['code']) && $res['code'] == 620) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Se o login estiver com duplo fator ativado infelizmente não consigo consultar"
			)->back(["count" => 0]);
			return;
        }

		if (isset($res['code']) && $res['code'] == 612) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Não encontrei os dados no ECAC"
			)->back(["count" => 0]);
			return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

	public function ecacSituacaoFiscal()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['pkcs12_cert', 'pkcs12_pass', 'perfil_procurador_cnpj'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }


        $consult = new Search(TOKEN_CNPJJA, URL_CNPJJA_IE);


        $res = $consult->ecacSituacaoFiscal($postVars['pkcs12_cert'], $postVars['pkcs12_pass'], $postVars['perfil_procurador_cnpj']);

		
		if (isset($res['code']) && $res['code'] == 200) {

			unset($res['data']['0']['site_receipt']);
            $arr = $res['data'];

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => $arr]);
            return;
        }

		if (isset($res['code']) && $res['code'] == 620) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Se o login estiver com duplo fator ativado infelizmente não consigo consultar"
			)->back(["count" => 0]);
			return;
        }

		if (isset($res['code']) && $res['code'] == 612) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Não encontrei os dados no ECAC"
			)->back(["count" => 0]);
			return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }

	public function ecacPedidoRestituicao()
    {
        $idReg = $this->checkToken();


        $postVars = $this->postVars();


        $fieldsRequired = ['pkcs12_cert', 'pkcs12_pass', 'perfil_procurador_cnpj', 'perdcomp'];

        if (!$this->fieldsRequired($fieldsRequired, $postVars)) {
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }


        $consult = new Search(TOKEN_CNPJJA, URL_CNPJJA_IE);


        $res = $consult->ecacPedidoRestituicao($postVars['pkcs12_cert'], $postVars['pkcs12_pass'], $postVars['perfil_procurador_cnpj'], $postVars['perdcomp']);

		if (isset($res['code']) && $res['code'] == 200) {

			unset($res['data']['0']['site_receipt']);
            $arr = $res['data'];

            $this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["retorno" => $arr]);
            return;
        }

		if (isset($res['code']) && $res['code'] == 620) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Se o login estiver com duplo fator ativado infelizmente não consigo consultar"
			)->back(["count" => 0]);
			return;
        }

		if (isset($res['code']) && $res['code'] == 612) {

            $this->call(
				'401',
				'Ops',
				'',
				"ops",
				"Não encontrei os dados no ECAC"
			)->back(["count" => 0]);
			return;
        }

        $this->call(
            '401',
            'Ops',
            '',
            "ops",
            "Tivemos um erro ao processar a request"
        )->back(["count" => 0]);
        return;
    }
	
	public function regularize():void
    {
        $idReg = $this->checkToken();

        $postVars = $this->postVars();
        $fieldsRequired = ['pkcs12_cert', 'pkcs12_pass', 'perfil_procurador_cnpj'];

        if(!$this->fieldsRequired($fieldsRequired, $postVars)){
            $this->call(
                '401',
                'Ops',
                '',
                "ops",
                "Existem campos inválidos"
            )->back(["count" => 0]);
            return;
        }

        if($postVars['pkcs12_cert'] == ''){
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Existem campos obrigatórios em branco"
            )->back(["count" => 0]);
            return;
        }

        $postWhats = [
            'regularize_ide' => hash('md5', date('YmdHis').rand(1000,9999)),
            'regularize_id_company' => $idReg,
            'regularize_certificado' => $postVars['pkcs12_cert'],
			'regularize_certificado_senha' => $postVars['pkcs12_pass'],
			'regularize_procurador' => $postVars['perfil_procurador_cnpj'],
            'regularize_status' => 0
        ];

        if($this->db()->insert($postWhats)->into('regularize')){
			$this->call(
                '200',
                'Sucesso',
                '',
                "ok",
                "Operação realizada com sucesso"
            )->back(["ide" => $postWhats['regularize_ide']]);
            return;
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Tivemos um problema, tente novamente mais tarde"
        )->back(["count" => 0]);
        return;
    }
	
	public function regularizeIde($param)
    {
        $idReg = $this->checkToken();

        $result = $this->db()->from('regularize')
            ->where('regularize_id_company')->is($idReg)
            ->andWhere('regularize_ide')->is($param['ide'])
            ->orderBy('regularize_id', 'DESC')
            ->limit('1')
            ->select()
            ->all();

        if(!$result){
            $this->call(
                '400',
                'Ops',
                '',
                "ops",
                "Registro não localizada"
            )->back(["count" => 0]);
            return;
        }

        foreach($result as $r){
			$resultItens = $this->db()->from('regularize_itens')
				->where('regularize_itens_id_regularize')->is($r->regularize_id)
				->orderBy('regularize_itens_id', 'DESC')
				->limit('1')
				->select()
				->all();
			if($resultItens){
				foreach($resultItens as $rItens){
					$json = base64_decode($rItens->regularize_itens_json);
					$arr = json_decode($json);
					$this->call(
						'200',
						'Sucesso',
						'',
						"ok",
						"Operação realizada com sucesso"
					)->back(["retorno" => $arr]);
					return;
				}
			}	
        }

        $this->call(
            '400',
            'Ops',
            '',
            "ops",
            "Ainda não temos o retorno informado"
        )->back(["count" => 0]);
        return;
    }
}
