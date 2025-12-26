<html charset="UTF-8">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="robots" content="noindex">
    <style>
        body {
            font-family: "Arial";
        }

        @media print {

            .no-print,
            .no-print * {
                display: none !important;
            }
        }

        .document {
            margin: auto auto;
            width: 216mm;
            height: 108mm;
            background-color: #fff;
        }

        .headerBtn {
            margin: auto auto;
            width: 216mm;
            background-color: #fff;
            display: none;
        }

        table {
            width: 100%;
            position: relative;
            border-collapse: collapse;
        }

        .bankLogo {
            width: 28%;
        }

        .boletoNumber {
            width: 62%;
            font-weight: bold;
        }

        .center {
            text-align: center;
        }

        .right {
            text-align: right;
            right: 20px;
        }

        td {
            position: relative;
        }

        .title {
            position: absolute;
            left: 0px;
            top: 0px;
            font-size: 12px;
            font-weight: bold;
        }

        .text {
            font-size: 12px;
        }

        p.content {
            padding: 0px;
            width: 100%;
            margin: 0px;
            font-size: 12px;
        }

        .sideBorders {
            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        hr {
            size: 1;
            border: 1px dashed;
        }

        br {
            content: " ";
            display: block;
            margin: 12px 0;
            line-height: 12px;
        }

        .print {
            background-color: rgb(77, 144, 254);
            background-image: linear-gradient(to bottom, rgb(77, 144, 254), rgb(71, 135, 237));
            border: 1px solid rgb(48, 121, 237);
            color: #fff;
            text-shadow: 0 1px rgba(0, 0, 0, 0.1);
        }

        .btnDefault {
            font-kerning: none;
            font-weight: bold;
        }

        .btnDefault:not(:focus):not(:disabled) {
            border-color: #808080;
        }

        button {
            border: 1px;
            padding: 5px;
            line-height: 20px;
        }



        i[class*=icss-] {
            position: relative;
            display: inline-block;
            font-style: normal;
            background-color: currentColor;
            -webkit-box-sizing: border-box;
            box-sizing: border-box;
            vertical-align: middle
        }

        i[class*=icss-]:after,
        i[class*=icss-]:before {
            content: "";
            border-width: 0;
            position: absolute;
            -webkit-box-sizing: border-box;
            box-sizing: border-box
        }

        i.icss-print {
            width: .68em;
            height: 1em;
            border-style: solid;
            border-color: currentcolor;
            border-width: .07em;
            -webkit-border-radius: .05em;
            border-radius: .05em;
            background-color: transparent;
            margin: 0 .17em
        }

        i.icss-print:before {
            width: 1em;
            height: .4em;
            border-width: .07em .21em 0;
            border-style: solid;
            border-color: currentColor currentcolor transparent;
            -webkit-border-radius: .05em .05em 0 0;
            border-radius: .05em .05em 0 0;
            top: .25em;
            left: 50%;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
            background-image: -webkit-gradient(linear, left top, left bottom, color-stop(20%, transparent), color-stop(20%, currentcolor), color-stop(60%, currentcolor), color-stop(60%, transparent));
            background-image: -webkit-linear-gradient(transparent 20%, currentcolor 20%, currentcolor 60%, transparent 60%);
            background-image: -o-linear-gradient(transparent 20%, currentcolor 20%, currentcolor 60%, transparent 60%);
            background-image: linear-gradient(transparent 20%, currentcolor 20%, currentcolor 60%, transparent 60%)
        }

        i.icss-print:after {
            width: .45em;
            height: .065em;
            background-color: currentColor;
            left: 50%;
            -webkit-transform: translateX(-50%);
            -ms-transform: translateX(-50%);
            transform: translateX(-50%);
            top: .6em;
            -webkit-box-shadow: 0 .12em, -.1em -.28em 0 .05em;
            box-shadow: 0 .12em, -.1em -.28em 0 .05em
        }

        i.icss-files {
            width: .75em;
            height: .95em;
            background-color: transparent;
            border: .05em solid transparent;
            border-width: 0 .05em .05em 0;
            -webkit-box-shadow: inset 0 0 0 .065em, .13em .11em 0 -.05em;
            box-shadow: inset 0 0 0 .065em, .13em .11em 0 -.05em;
            -webkit-border-radius: 0 .3em 0 0;
            border-radius: 0 .3em 0 0;
            margin: 0 .17em .05em .1em
        }

        i.icss-files:before {
            border-style: solid;
            border-width: .2em;
            top: .037em;
            left: .25em;
            -webkit-border-radius: .1em;
            border-radius: .1em;
            border-color: transparent currentColor transparent transparent;
            -webkit-transform: rotate(-45deg);
            -ms-transform: rotate(-45deg);
            transform: rotate(-45deg)
        }
    </style>
</head>

<body>
    <br />
    <div style="width: 216mm;margin: auto auto;">
        {QRCODE_PIX}
    </div>
    <br />
    <div class="document">
        <table cellspacing="0" cellpadding="0">
            <tr class="topLine">
                <td class="bankLogo">
                    <img src="{IMG_BANCO}"
                        alt="" width="100px">
                </td>
                <td class="sideBorders center"><span style="font-size:24px;font-weight:bold;">{CODE_BANCO}</span></td>
                <td class="boletoNumber center"><span>{LINHA_DIGITAVEL}</span></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1">
            <tr>
                <td width="70%" colspan="6">
                    <span class="title">Local de Pagamento</span>
                    <br />
                    <span class="text">ATÉ O VENCIMENTO EM QUALQUER BANCO OU CORRESPONDENTE NÃO BANCÁRIO, APÓS O VENCIMENTO, PAGUE EM QUALQUER BANCO OU CORRESPONDENTE NÃO BANCÁRIO</span>
                </td>
                <td width="30%">
                    <span class="title">Data de Vencimento</span>
                    <br />
                    <br />
                    <p class="content right text" style="font-weight:bold;">{DATA_VENCIMENTO}</p>
                </td>
            </tr>
            <tr>
                <td width="70%" colspan="6">
                    <span class="title">Nome do Beneficiário / CNPJ / CPF / Endereço:</span>
                    <br />
                    <table border="0" style="border:none">
                        <tr>
                            <td width="60%"><span class="text">{BENEFICIARIO_NOME}</span></td>
                            <td><span class="text">CNPJ {BENEFICIARIO_DOC}</span></td>
                        </tr>
                    </table>
                    <br />
                    <span class="text">{ENDERECO1_BENEFICIARIO}</span>
                </td>
                <td width="30%">
                    <span class="title">Agência/Código Beneficiário</span>
                    <br />
                    <br />
                    <p class="content right">{AGENCIA} / {CODIGO_BENEFICIARIO}</p>
                </td>
            </tr>

            <tr>
                <td width="15%">
                    <span class="title">Data do Documento</span>
                    <br />
                    <p class="content center">{DATA_DOCUMENTO}</p>
                </td>
                <td width="17%" colspan="2">
                    <span class="title">Num. do Documento</span>
                    <br />
                    <p class="content center">{NUMERO_DOCUMENTO}</p>
                </td>
                <td width="10%">
                    <span class="title">Espécie doc</span>
                    <br />
                    <p class="content center">{ESPECIE}</p>
                </td>
                <td width="8%">
                    <span class="title">Aceite</span>
                    <br />
                    <p class="content center">{ACEITE}</p>
                </td>
                <td>
                    <span class="title">Data Processamento</span>
                    <br />
                    <p class="content center">{DATA_PROCESSAMENTO}</p>
                </td>
                <td width="30%">
                    <span class="title">Nosso Número</span>
                    <br />
                    <br />
                    <p class="content right">{NOSSO_NUMERO}</p>
                </td>
            </tr>

            <tr>
                <td width="15%">
                    <span class="title">Uso do Banco</span>
                    <br />
                    <p class="content center">&nbsp;</p>
                </td>
                <td width="10%">
                    <span class="title">Carteira</span>
                    <br />
                    <p class="content center"></p>
                </td>
                <td width="10%">
                    <span class="title">Espécie</span>
                    <br />
                    <p class="content center">R$</p>
                </td>
                <td width="8%" colspan="2">
                    <span class="title">Quantidade</span>
                    <br />
                    <p class="content center">N</p>
                </td>
                <td>
                    <span class="title">Valor</span>
                    <br />
                    <p class="content center">{VALOR}</p>
                </td>
                <td width="30%">
                    <span class="title">(=) Valor do Documento</span>
                    <br />
                    <br />
                    <p class="content right">{VALOR}</p>
                </td>
            </tr>
            <tr>

                <td colspan="6" rowspan="4">
                    <span class="title">Instruções de responsabilidade do BENEFICIÁRIO. Qualquer dúvida contate o beneficiário.</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">(-) Descontos/Abatimento</span>
                    <br />
                    <p class="content right">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">(+) Juros/Multa</span>
                    <br />
                    <p class="content right">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">(=) Valor Pago</span>
                    <br />
                    <p class="content right">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <table border="0" style="border:none">
                        <tr>
                            <td width="60%"><span class="text"><b>Nome do Pagador: </b> {NOME_SACADO}</span></td>
                            <td><span class="text"><b>CNPJ/CPF: </b> {DOC_SACADO}</span></td>
                        </tr>
                        <tr>
                            <td><span class="text"><b>Endereço: </b> {SACADO_ENDERECO01} - {SACADO_ENDERECO02}</span></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr STYLE="display: none">
                            <td><span class="text"><b>Sacador/Avalista: </b> &nbsp;</span></td>
                            <td><span class="text"><b>CNPJ/CPF: </b> &nbsp;</span></td>
                        </tr>
                    </table>

                </td>

            </tr>
        </table>
    </div>
    <div class="document">
        <hr />
        <table cellspacing="0" cellpadding="0">
            <tr class="topLine">
                <td class="bankLogo">
                    <img src="{IMG_BANCO}"
                        alt="" width="100px">
                </td>
                <td class="sideBorders center"><span style="font-size:24px;font-weight:bold;">{CODE_BANCO}</span></td>
                <td class="boletoNumber center"><span>{LINHA_DIGITAVEL}</span></td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" border="1">
            <tr>
                <td width="70%" colspan="6">
                    <span class="title">Local de Pagamento</span>
                    <br />
                    <span class="text">ATÉ O VENCIMENTO EM QUALQUER BANCO OU CORRESPONDENTE NÃO BANCÁRIO, APÓS O VENCIMENTO, PAGUE EM QUALQUER BANCO OU CORRESPONDENTE NÃO BANCÁRIO</span>
                </td>
                <td width="30%">
                    <span class="title">Data de Vencimento</span>
                    <br />
                    <br />
                    <p class="content right text" style="font-weight:bold;">{DATA_VENCIMENTO}</p>
                </td>
            </tr>
            <tr>
                <td width="70%" colspan="6">
                    <span class="title">Nome do Beneficiário / CNPJ / CPF / Endereço:</span>
                    <br />
                    <table border="0" style="border:none">
                        <tr>
                            <td width="60%"><span class="text">{BENEFICIARIO_NOME}</span></td>
                            <td><span class="text">CNPJ {BENEFICIARIO_DOC}</span></td>
                        </tr>
                    </table>
                    <br />
                    <span class="text">{ENDERECO1_BENEFICIARIO}</span>
                </td>
                <td width="30%">
                    <span class="title">Agência/Código Beneficiário</span>
                    <br />
                    <br />
                    <p class="content right">{AGENCIA} / {CODIGO_BENEFICIARIO}</p>
                </td>
            </tr>

            <tr>
                <td width="15%">
                    <span class="title">Data do Documento</span>
                    <br />
                    <p class="content center">{DATA_DOCUMENTO}</p>
                </td>
                <td width="17%" colspan="2">
                    <span class="title">Num. do Documento</span>
                    <br />
                    <p class="content center">{NUMERO_DOCUMENTO}</p>
                </td>
                <td width="10%">
                    <span class="title">Espécie doc</span>
                    <br />
                    <p class="content center">{ESPECIE}</p>
                </td>
                <td width="8%">
                    <span class="title">Aceite</span>
                    <br />
                    <p class="content center">{ACEITE}</p>
                </td>
                <td>
                    <span class="title">Data Processamento</span>
                    <br />
                    <p class="content center">{DATA_PROCESSAMENTO}</p>
                </td>
                <td width="30%">
                    <span class="title">Nosso Número</span>
                    <br />
                    <br />
                    <p class="content right">{NOSSO_NUMERO}</p>
                </td>
            </tr>

            <tr>
                <td width="15%">
                    <span class="title">Uso do Banco</span>
                    <br />
                    <p class="content center">&nbsp;</p>
                </td>
                <td width="10%">
                    <span class="title">Carteira</span>
                    <br />
                    <p class="content center"></p>
                </td>
                <td width="10%">
                    <span class="title">Espécie</span>
                    <br />
                    <p class="content center">R$</p>
                </td>
                <td width="8%" colspan="2">
                    <span class="title">Quantidade</span>
                    <br />
                    <p class="content center"></p>
                </td>
                <td>
                    <span class="title">Valor</span>
                    <br />
                    <p class="content center">{VALOR}</p>
                </td>
                <td width="30%">
                    <span class="title">(=) Valor do Documento</span>
                    <br />
                    <br />
                    <p class="content right">{VALOR}</p>
                </td>
            </tr>
            <tr>
                <td colspan="6" rowspan="4">
                    <span class="title">Instruções de responsabilidade do BENEFICIÁRIO. Qualquer dúvida contate o beneficiário.</span>

                    {DESCRICAO_BOLETO}

                    {JUROS_MULTA_BOLETO}
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">(-) Descontos/Abatimento</span>
                    <br />
                    <p class="content right">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">(+) Juros/Multa</span>
                    <br />
                    <p class="content right">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="title">(=) Valor Pago</span>
                    <br />
                    <p class="content right">&nbsp;</p>
                </td>
            </tr>
            <tr>
                <td colspan="7">
                    <table border="0" style="border:none">
                        <tr>
                            <td width="60%"><span class="text"><b>Nome do Pagador: </b> {NOME_SACADO}</span></td>
                            <td><span class="text"><b>CNPJ/CPF: </b> {DOC_SACADO}</span></td>
                        </tr>
                        <tr>
                            <td><span class="text"><b>Endereço: </b> {SACADO_ENDERECO01} - {SACADO_ENDERECO02}</span></td>
                            <td>&nbsp;</td>
                        </tr>
                        <tr STYLE="display: none;">
                            <td><span class="text"><b>Sacador/Avalista: </b> &nbsp;</span></td>
                            <td><span class="text"><b>CNPJ/CPF: </b> &nbsp;</span></td>
                        </tr>
                    </table>

                </td>

            </tr>
        </table>
        <br />
        {CODIGO_BARRAS}
        <br />
        <br />
        <br />
    </div>
</body>

</html>