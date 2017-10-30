<?php

//$json = json_decode(file_get_contents("json/respostas.json"));
$json = json_decode(file_get_contents('php://input'));

$id_aluno=$json[0]->{'id_aluno'};
   $rodada=$json[0]->{'rodada'};
   //o nivel eh o id_assunto
   $id_assunto=$json[0]->{'id_assunto'};
   $respostas=$json[0]->{'respostas'};
   $bateria_de_atividades = $json[0]->{'bateria_de_atividades'};

   
$corretas = 0;
$erradas = 0;


for($i = 0; $i < count($respostas); $i++) {
    
       $id_atividade =  $respostas[$i]->{'id_atividade'};
       $resposta_aluno = $respostas[$i]->{'resposta_aluno'};
       $alternativa_marcada = $respostas[$i]->{'alternativa_marcada'};
	if($resposta_aluno == "certa"){
	    $corretas++;
	}else if ($resposta_aluno == "errada") {
	    $erradas++;
	}

	require("conexao.php");
	
	$query_inserir = "INSERT INTO respostas(
             id_aluno, id_atividade, id_assunto, rodada, resposta_aluno, alternativa_marcada)
              VALUES ('$id_aluno','$id_atividade', '$id_assunto', '$rodada', '$resposta_aluno', '$alternativa_marcada')";

	mysqli_query($conexao, $query_inserir) or die(mysqli_error($conexao));
    }

	$sql = "";
	$nquestoes = $corretas + $erradas;
	$porcentagem = ($corretas*100)/$nquestoes;

	if($porcentagem >=60){

	    $rodada = "1";
	    $proximo_nivel = $id_assunto+1;
	    $sql = "SELECT * FROM bd_atividades WHERE rodada=$rodada AND id_assunto=$proximo_nivel LIMIT $bateria_de_atividades";
	    $nome_arquivo = "proximo_nivel";

	    $nome_arquivo = "$id_aluno-$proximo_nivel-$rodada.json";

	} else if($porcentagem<60){
	    if($rodada<3){

		$nova_rodada = $rodada+1;
		$sql = "SELECT * FROM bd_atividades WHERE rodada=$nova_rodada AND id_assunto=$id_assunto LIMIT $bateria_de_atividades";
		$nome_arquivo = "nova_rodada";

		$nome_arquivo = "$id_aluno-$id_assunto-$nova_rodada.json";

	    }else if($rodada==3){

		//???????????????????/ Capitulo 4 - Resultados; Seção 4.2 - Discussão dos resultados

	    }
	}

	//GERAR NOVO JSON DE ACORDO COM AS RESPOSTAS DO ALUNO
	$return_arr = array();
	$fetch = mysqli_query($conexao, $sql);
	while ($row = mysqli_fetch_array($fetch)) {
	    $row_array['id_atividade'] = $row['id_atividade'];
	    $row_array['desc_atividade'] = $row['desc_atividade'];
	    $row_array['id_assunto'] = $row['id_assunto'];
	    $row_array['assunto'] = $row['assunto'];

	    $row_array['imagem_atividade'] = $row['imagem_atividade'];
	    $x=1;
	    while($x<6){
		$row_array["alternativa$x"] = $row["alternativa$x"];
		$row_array["imagem_alternativa$x"] = $row["imagem_alternativa$x"];
		$row_array["justificativa_erro_alternativa$x"] = $row["justificativa_erro_alternativa$x"];

		$x++;
	    }
	    $row_array['alternativa_correta'] = $row['imagem_atividade'];
	    $row_array['rodada'] = $row['rodada'];
	    $row_array['data_de_cadastro'] = $row['data_de_cadastro'];

	    array_push($return_arr,$row_array);
	}

	$arquivo =  json_encode($return_arr, JSON_PRETTY_PRINT);

	$fp = fopen("json/$nome_arquivo", "w");
	$escreve = fwrite($fp, $arquivo);
	fclose($fp);

	$insere = mysqli_query($conexao, "INSERT INTO analise_tutor (id_aluno, porcentagem, arquivo_json) VALUES ('$id_aluno', '$porcentagem', '$nome_arquivo')");

	if($insere){

	    echo "TUTOR INSERIU COM SUCESSO; gerou o arquivo $nome_arquivo; Porcentagem de $porcentagem%";

	}

?>
