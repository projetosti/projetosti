<?php
//http://blog.clares.com.br/gerando-xml-com-php-e-mysql/
require("conexao.php");

$query = mysqli_query($conexao, "SELECT * FROM bd_atividades");

$xml = new XMLWriter;
$xml->openMemory();
  $xml->setIndent(true);

# Definindo o encoding do XML
$xml->startDocument( '1.0' , 'utf-8' );
# Primeiro elemento do XML
$xml->startElement("atividades");
	# Query na tabela albuns
	while($exibir = mysqli_fetch_array($query))
	{
		# Transformando array em objeto
		$exibir = (object)$exibir;

		# Criando elemento album
    $xml->startElement("atividade");
    $xml->writeAttribute("id_atividade", "$exibir->id_atividade");
    $xml->writeElement("desc_atividade", "$exibir->desc_atividade");
    $xml->writeElement("imagem_atividade", "$exibir->imagem_atividade");

        $xml->writeElement("alternativa1", "$exibir->alternativa1");
        $xml->writeElement("imagem_alternativa1", "$exibir->imagem_alternativa1");
        $xml->writeElement("justificativa_erro_alternativa1", "$exibir->justificativa_erro_alternativa1");

        $xml->writeElement("alternativa2", "$exibir->alternativa2");
        $xml->writeElement("imagem_alternativa2", "$exibir->imagem_alternativa2");
        $xml->writeElement("justificativa_erro_alternativa2", "$exibir->justificativa_erro_alternativa2");

        $xml->writeElement("alternativa3", "$exibir->alternativa3");
        $xml->writeElement("imagem_alternativa3", "$exibir->imagem_alternativa3");
        $xml->writeElement("justificativa_erro_alternativa3", "$exibir->justificativa_erro_alternativa3");

        $xml->writeElement("alternativa4", "$exibir->alternativa4");
        $xml->writeElement("imagem_alternativa4", "$exibir->imagem_alternativa4");
        $xml->writeElement("justificativa_erro_alternativa4", "$exibir->justificativa_erro_alternativa4");

        $xml->writeElement("alternativa5", "$exibir->alternativa5");
        $xml->writeElement("imagem_alternativa5", "$exibir->imagem_alternativa5");
        $xml->writeElement("justificativa_erro_alternativa5", "$exibir->justificativa_erro_alternativa5");

    $xml->writeElement("alternativa_correta", "$exibir->alternativa_correta");
    $xml->writeElement("rodada", "$exibir->rodada");
    $xml->writeElement("assunto", "$exibir->assunto");
    $xml->writeElement("data_de_cadastro", "$exibir->data_de_cadastro");

    $xml->endElement();

	}
	# Fechando o elemento album
# Fechando o elemento featureset
$xml->endElement();


# Definindo cabecalho de saida
header( 'Content-type: text/xml' );
# Imprimindo a saida do XML
print $xml->outputMemory(true);

//header('Content-disposition: attachment; filename=atividades.xml');
//header('Content-type: application/xml');
