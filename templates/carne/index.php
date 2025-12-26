<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{BENEFICIARIO_NOME}</title>
    <style type="text/css">
        @media print {
            .noprint {
                display:none;
            }
        }

        body{
            background-color: #ffffff;
            margin-right: 0;
        }

        .table-boleto {
            font: 9px Arial;
            width: 666px;
        }

        .table-boleto td {
            border-left: 1px solid #000;
            border-top: 1px solid #000;
        }

        .table-boleto td:last-child {
            border-right: 1px solid #000;
        }

        .table-boleto .titulo {
            color: #000033;
        }

        .linha-pontilhada {
            color: #000033;
            font: 9px Arial;
            width: 100%;
            border-bottom: 1px dashed #000;
            text-align: right;
            margin-bottom: 10px;
        }

        .table-boleto .conteudo {
            font: bold 10px Arial;
            height: 11.5px;
        }

        .table-boleto .sacador {
            display: inline;
            margin-left: 5px;
        }

        .table-boleto td {
            padding: 1px 4px;
        }

        .table-boleto .noleftborder {
            border-left: none !important;
        }

        .table-boleto .notopborder {
            border-top: none !important;
        }

        .table-boleto .norightborder {
            border-right: none !important;
        }

        .table-boleto .noborder {
            border: none !important;
        }

        .table-boleto .bottomborder {
            border-bottom: 1px solid #000 !important;
        }

        .table-boleto .rtl {
            text-align: right;
        }

        .table-boleto .logobanco {
            display: inline-block;
            max-width: 150px;
        }

        .table-boleto .logocontainer {
            width: 257px;
            display: inline-block;
        }

        .table-boleto .logobanco img {
            margin-bottom: -5px;
            height: 27px;
        }

        .table-boleto .codbanco {
            font: bold 20px Arial;
            padding: 1px 5px;
            display: inline;
            border-left: 2px solid #000;
            border-right: 2px solid #000;
            width: 51px;
            margin-left: 25px;
        }

        .table-boleto .linha-digitavel {
            font: bold 14px Arial;
            display: inline-block;
            width: 406px;
            text-align: right;
        }

        .table-boleto .nopadding {
            padding: 0px !important;
        }

        .table-boleto .caixa-gray-bg {
            font-weight: bold;
            background: #ccc;
        }

        .info {
            font: 11px Arial;
        }

        .info-empresa {
            font: 11px Arial;
        }

        .header {
            font: bold 13px Arial;
            display: block;
            margin: 4px;
        }

        .barcode {
            height: 50px;
        }

        .barcode div {
            display: inline-block;
            height: 100%;
        }

        .barcode .black {
            border-color: #000;
            border-left-style: solid;
            width: 0px;
        }

        .barcode .white {
            background: #fff;
        }

        .barcode .thin.black {
            border-left-width: 1px;
        }

        .barcode .large.black {
            border-left-width: 3px;
        }

        .barcode .thin.white {
            width: 1px;
        }

        .barcode .large.white {
            width: 3px;
        }

        .float_left{
            float:left;
        }

        .center {
            text-align: center;
        }

        .conteudo.cpf_cnpj{
            float:right;
            width:24%;
        }
    </style>
</head>
<body>

<div style="width: 863px">
    <div style="float: left">
        <table class="table-boleto" style="width: 180px" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td>
                    <div class="titulo">Vencimento</div>
                    <div class="conteudo">{DATA_VENCIMENTO}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">Agência/Código do Beneficiário</div>
                    <div class="conteudo">{AGENCIA} / {CODIGO_BENEFICIARIO}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">Nosso número</div>
                    <div class="conteudo">{NOSSO_NUMERO}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">Nº documento</div>
                    <div class="conteudo">{NUMERO_DOCUMENTO}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">Espécie</div>
                    <div class="conteudo">{ESPECIE}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">Quantidade</div>
                    <div class="conteudo"></div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">(=) Valor Documento</div>
                    <div class="conteudo">{VALOR}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">(-) Descontos / Abatimentos</div>
                    <div class="conteudo">{DESCONTO_ABATIMENTO}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">(-) Outras deduções</div>
                    <div class="conteudo">{OUTRAS_DEDUCOES}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">(+) Mora / Multa</div>
                    <div class="conteudo">{MULTA}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">(+) Outros acréscimos</div>
                    <div class="conteudo">{OUTROS_ACRESCIMOS}</div>
                </td>
            </tr>
            <tr>
                <td class="bottomborder">
                    <div class="titulo">(=) Valor cobrado</div>
                    <div class="conteudo">{VALOR}</div>
                </td>
            </tr>
            <tr>
                <td class="bottomborder">
                    <div class="titulo">CNPJ do Beneficiário</div>
                    <div class="conteudo">{BENEFICIARIO_DOC}</div>
                </td>
            </tr>
            <tr>
                <td class="bottomborder">
                    <div class="titulo">Endereço do Beneficiário</div>
                    <div class="conteudo">{ENDERECO1_BENEFICIARIO}</div>
                </td>
            </tr>
        </table>
        <span class="header">Recibo do Pagador</span>
    </div>
    <div style="float: left; margin-left: 15px">
        <table class="table-boleto" cellpadding="0" cellspacing="0" border="0">
            <tbody>
            <tr>
                <td valign="bottom" colspan="8" class="noborder nopadding">
                    <div class="logocontainer">
                        <div class="logobanco">
                            <img src="{IMG_BANCO}" alt="logotipo do banco">
                        </div>
                        <div class="codbanco">{CODE_BANCO}</div>
                    </div>
                    <div class="linha-digitavel">{LINHA_DIGITAVEL}</div>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <div class="titulo">Local de pagamento</div>
                    <div class="conteudo">PAGÁVEL EM QUALQUER BANCO OU LOTÉRICA MESMO APÓS O VENCIMENTO</div>
                </td>
                <td width="180">
                    <div class="titulo">Vencimento</div>
                    <div class="conteudo rtl">{DATA_VENCIMENTO}</div>
                </td>
            </tr>
            <tr>
                <td colspan="7" rowspan="2" valign="top">
                    <div class="titulo">Beneficiário</div>
                    <div class="conteudo float_left">{BENEFICIARIO_NOME}<br />{ENDERECO1_BENEFICIARIO}<br />{ENDERECO2_BENEFICIARIO}</div>
                    <div class="conteudo cpf_cnpj">{BENEFICIARIO_DOC}</div>


                </td>
                <td>
                    <div class="titulo">Agência/Código beneficiário</div>
                    <div class="conteudo rtl">{AGENCIA} / {CODIGO_BENEFICIARIO}</div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="titulo">Nosso número</div>
                    <div class="conteudo rtl">{NOSSO_NUMERO}</div>
                </td>
            </tr>
            <tr>
                <td width="110" colspan="2">
                    <div class="titulo">Data do documento</div>
                    <div class="conteudo">{DATA_DOCUMENTO}</div>
                </td>
                <td width="120" colspan="2">
                    <div class="titulo">Nº documento</div>
                    <div class="conteudo">{NUMERO_DOCUMENTO}</div>
                </td>
                <td width="60">
                    <div class="titulo">Espécie doc.</div>
                    <div class="conteudo">{ESPECIE}</div>
                </td>
                <td>
                    <div class="titulo">Aceite</div>
                    <div class="conteudo">{ACEITE}</div>
                </td>
                <td width="110">
                    <div class="titulo">Data processamento</div>
                    <div class="conteudo">{DATA_PROCESSAMENTO}</div>
                </td>
                <td>
                    <div class="titulo">(=) Valor do Documento</div>
                    <div class="conteudo rtl">{VALOR}</div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="titulo">Uso do banco</div>
                    <div class="conteudo"></div>
                </td>

                <td>
                    <div class="titulo">Carteira</div>
                    <div class="conteudo"></div>
                </td>
                <td width="35">
                    <div class="titulo">Espécie</div>
                    <div class="conteudo">{ESPECIE}</div>
                </td>
                <td colspan="2">
                    <div class="titulo">Quantidade</div>
                    <div class="conteudo"></div>
                </td>
                <td width="110">
                    <div class="titulo">Valor</div>
                    <div class="conteudo">{VALOR}</div>
                </td>
                <td>
                    <div class="titulo">(-) Descontos / Abatimentos</div>
                    <div class="conteudo rtl">{DESCONTO_ABATIMENTO}</div>
                </td>
            </tr>
            <tr>
                <td colspan="7" valign="top">
                    <div class="titulo">Instruções (Texto de responsabilidade do beneficiário)</div>
                </td>
                <td>
                    <div class="titulo">(-) Outras deduções</div>
                    <div class="conteudo rtl">{OUTRAS_DEDUCOES}</div>
                </td>
            </tr>
            <tr>
                <td colspan="5" class="notopborder" valign="top">
                    <div class="conteudo">{DADOS_INSTRUCAO}</div>
                </td>
                <td colspan="2" class="noborder" valign="top">
                    <div class="conteudo">{QRCODE_PIX}</div>
                </td>
                <td>
                    <div class="titulo">(+) Mora / Multa</div>
                    <div class="conteudo rtl">{MULTA}</div>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="notopborder">

                </td>
                <td>
                    <div class="titulo">(+) Outros acréscimos</div>
                    <div class="conteudo rtl">{OUTROS_ACRESCIMOS}</div>
                </td>
            </tr>
            <tr>
                <td colspan="7" class="notopborder">

                </td>
                <td>
                    <div class="titulo">(=) Valor cobrado</div>
                    <div class="conteudo rtl">{VALOR}</div>
                </td>
            </tr>
            <tr>
                <td colspan="7" valign="top">
                    <div class="titulo">Pagador</div>
                    <div class="conteudo float_left">{NOME_SACADO}<br />
                        {SACADO_ENDERECO01}<br />{SACADO_ENDERECO02}</div>
                    <div class="conteudo cpf_cnpj">{DOC_SACADO}</div>
                </td>
                <td class="noleftborder">
                    <div class="titulo" style="margin-top: 50px">Cód. Baixa</div>
                </td>
            </tr>

            <tr>
                <td colspan="6" class="noleftborder">
                    <div class="titulo" style="display: none;">Pagador / Avalista <div class="conteudo pagador"><?php echo $sacador_avalista; ?></div></div>
                </td>
                <td colspan="2" class="norightborder noleftborder">
                    <div class="conteudo noborder rtl">Autenticação mecânica - Ficha de Compensação</div>
                </td>
            </tr>

            <tr>
                <td colspan="8" class="noborder">
                    {CODIGO_BARRAS}
                </td>
            </tr>

            </tbody>
        </table>
    </div>
    <div style="clear: both"></div>
    <div class="linha-pontilhada">Corte na linha pontilhada</div>
</div>

</body>
</html>
