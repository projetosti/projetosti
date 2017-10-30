<?php

$ip = "localhost";

session_start();

if(@$_GET["rodada"]==""){

    if(@$_SESSION["rodada"]==""){
	$_SESSION["rodada"] = $rodada = $_POST["rodada"];

	$assunto = explode(" - ", $_POST["assunto"]);

	$_SESSION["id_assunto"] = $assunto[0];
	$_SESSION["assunto"] = $assunto = $_POST["assunto"];


    }else{

	$rodada = $_SESSION["rodada"];
	$assunto = $_SESSION["assunto"];
    }
}else{
    $_SESSION["rodada"] = $rodada = $_GET["rodada"];
    $_SESSION["assunto"] = $assunto = $_GET["assunto"];
}

if($_SESSION["assunto"]==""){

    header("location: index.php");

}

?>

<script>
 function excluir(id){

     if(confirm("Deseja realmente deletar essa atividade?")){
	 window.location.href="manipulandoAtividade.php?id_atividade="+id+"&operacao=excluir";
     }

 }

 function highlightNext(element, color) {
     var next = element;
     do { // find next td node
         next = next.nextSibling;
     }
     while (next && !('nodeName' in next && next.nodeName === 'TD'));
     if (next) {
         next.style.color = color;
     }
 }

 function highlightBG(element, color) {
     element.style.backgroundColor = color;
 }
</script>
<link href="css/estilo.css" rel="stylesheet" />
<?php

echo "<meta charset=utf8>";
require ("conexao.php");

echo "<a href='index.php'>Escolher Assunto e Rodada </a>&nbsp;&nbsp;";
echo "<a href='manipularAtividade.php'>Cadastrar nova atividade</a> &nbsp;&nbsp; ";

if($rodada==1){
    echo "<a href='verAtividade.php?rodada=2&assunto=$assunto'>Ver Atividades Rodada 2</a>&nbsp;&nbsp;";
    echo "<a href='verAtividade.php?rodada=3&assunto=$assunto'>Ver Atividades Rodada 3</a>&nbsp;&nbsp;";
    
}else if ($rodada==2){
    echo "<a href='verAtividade.php?rodada=1&assunto=$assunto'>Ver Atividades Rodada 1</a>&nbsp;&nbsp;";
    echo "<a href='verAtividade.php?rodada=3&assunto=$assunto'>Ver Atividades Rodada 3</a>&nbsp;&nbsp;";
    
}else{
    echo "<a href='verAtividade.php?rodada=1&assunto=$assunto'>Ver Atividades Rodada 1</a>&nbsp;&nbsp;";
    echo "<a href='verAtividade.php?rodada=2&assunto=$assunto'>Ver Atividades Rodada 2</a>&nbsp;&nbsp;";
    
}

echo "<a href='gerar_json.php'>Gerar JSON</a>&nbsp;&nbsp;";
echo "<a href='gerar_xml.php'>Gerar XML</a>&nbsp;&nbsp;";

echo "<br><br><br><b><font color=blue> Assunto: $assunto</font><b><br><br>";
echo "<br><b><font color=blue> Rodada: $rodada</font><b><br><br>";

$sql = mysqli_query($conexao, "SELECT * FROM bd_atividades WHERE rodada=$rodada AND id_assunto=".$_SESSION["id_assunto"]) or die (mysqli_error($conexao));
$cont = mysqli_num_rows($sql);

if($cont==0){
    echo "<font color=red>NENHUMA ATIVIDADE CADASTRADA!!!";
}else{    
    echo @$_SESSION["mensagem"];
    $_SESSION["mensagem"] = "";
    
    echo "Número de objetos de Aprendizagem Encontrado (Atividades) : ". mysqli_num_rows($sql);
    
    echo "<br><br>";
    echo "<table border>";
    echo "<tr><td></td><td>rodada</td><td>Atividade</td><td>Alternativa a</td><td>Alternativa b</td><td>Alternativa c</td><td>Alternativa d</td><td>Alternativa e</td></tr>";


    
    while ($row = mysqli_fetch_array($sql)) {
	
	$tamanho_imagem = "100%";
	$id = $row["id_atividade"];

	$assunto=$row["id_assunto"];

	$rodada = $row["rodada"];
	
        //ATIVIDADE
        $atividade = $row["desc_atividade"];

	$imagem_atividade = $row["imagem_atividade"];
	
 	if($imagem_atividade != ""){
	    $imagem_atividade = "<img src='$imagem_atividade' width='$tamanho_imagem' />";
	}else{
	    $imagem_atividade = "sem imagem";
	}
	
        //ALTERNATIVAS
        $x=1;
        while($x<6){
            $alternativa[$x] = $row["alternativa$x"];
            $justificativa_erro_alternativa[$x] = $row["justificativa_erro_alternativa$x"];
	    $imagem_alternativa[$x]  = $row["imagem_alternativa$x"];
	    if($imagem_alternativa[$x] != "")
		$imagem_alternativa[$x]  = "<img src='".$imagem_alternativa[$x]." ' width='$tamanho_imagem' />";
  	    else
		$imagem_alternativa[$x]  = "sem imagem<br>";

	    $x++;
        }
        $alternativaCorreta =$row["alternativa_correta"];
	
	echo "<tr onMouseOver=\"highlightBG(this, '#a1ced1');highlightNext(this, '#a1ced1')\" onMouseOut=\"highlightBG(this, 'white');highlightNext(this, 'black')\"><td width=110 rowspan=2 valign='top'><a href='manipularAtividade.php?id_atividade=$id&operacao=editar'>editar</a> <a href=\"javascript:excluir($id)\">excluir</a></td><td rowspan=2  valign='top'>$rodada</td><td  valign='top'>$atividade</td>";
	
        $x=1;
        while($x<6){
	    echo "<td  valign='top'>".$alternativa[$x]."</td>";
            $x++;
        }
	
        echo "</tr><tr><td valign='top'>$imagem_atividade</td>";
        $x=1;
        while($x<6){
            echo "<td valign='top'>".$imagem_alternativa[$x]." <br>";
            if($justificativa_erro_alternativa[$x] == "")
		echo  "<font color=green> Correta! </font></td>";
            else
		echo "<font color=red> Erro: ".$justificativa_erro_alternativa[$x]."</font></td>";
	    
            $x++;
	}
	echo "</tr>";


    }
    echo "</table>";

}

require("conexao.php");

$fetch = mysqli_query($conexao, "SELECT * FROM bd_atividades WHERE id_assunto=1 AND rodada=1") or die(mysqli_error($conexao))	;

$array_atividades = array();
$array_alternativas = array();

$y=1;
while ($linha = mysqli_fetch_array($fetch)) {
    $array_atividade = array();
    $array_alternativas = array();
    $array_imagem_atividade = array();
    $array_imagens = array();

    
    $array_atividade['id_atividade'] = $linha['id_atividade'];
    $array_atividade['id_assunto'] =$linha['id_assunto'];
    $array_atividade['desc_atividade'] = "Desafio $y) ". $linha['desc_atividade'];

    $imagem = $linha['imagem_atividade'];
    if($imagem==""){
	$array_imagem_atividade["imagem_atividade"] = "http://$ip/tcc_6/transparente.png";
	$array_imagem_atividade["tamanho"] = 1;
	
    }else{
	$array_imagem_atividade["imagem_atividade"] = "http://$ip/tcc_6/$imagem";
	$array_imagem_atividade["tamanho"] = 100;
    }    
    array_push($array_imagens, $array_imagem_atividade);
    $array_atividade["atividade"] = $array_imagens;

    $y++;
    
    $x=1;
    
    while($x<6){

        $array_alternativa = array();
	
	if($x=='1') $letra = 'a';
	if($x=='2') $letra = 'b';
	if($x=='3') $letra = 'c';
	if($x=='4') $letra = 'd';
	if($x=='5') $letra = 'e';
	
	$array_alternativa["alternativa"] = $letra.") ".$linha["alternativa$x"];

	$imagem = $linha["imagem_alternativa$x"];
	if($imagem == ""){
	    $array_alternativa["imagem_alternativa"] = "http://$ip/tcc_6/transparente.png";
	    $array_alternativa["tamanho"] = 1;
	}else{
	    $array_alternativa["imagem_alternativa"] = "http://$ip/tcc_6/$imagem";
	    $array_alternativa["tamanho"] = 100;
	}
	$array_alternativa["justificativa_erro_alternativa"] = $linha["justificativa_erro_alternativa$x"];

	array_push($array_alternativas, $array_alternativa);
	
	$x++;
    }
    
    $array_atividade["alternativas"] = $array_alternativas; 
    
    if($linha['alternativa_correta']=='a') $correta = 0;
    if($linha['alternativa_correta']=='b') $correta = 1;
    if($linha['alternativa_correta']=='c') $correta = 2;
    if($linha['alternativa_correta']=='d') $correta = 3;
    if($linha['alternativa_correta']=='e') $correta = 4;
    
    $array_atividade['alternativa_correta'] = $correta;
    $array_atividade['rodada'] = $linha['rodada'];
    $array_atividade['assunto'] = $linha['assunto'];
    $array_atividade['data_de_cadastro'] = $linha['data_de_cadastro'];

    array_push($array_atividades, $array_atividade);
    
}
$arquivo =  json_encode($array_atividades, JSON_PRETTY_PRINT);
$fp = fopen("json/atividades.json", "w");
$escreve = fwrite($fp, $arquivo);
fclose($fp);

?>
<br>
<a href="json/atividades.json"><font color="green">Arquivo JSON gerado com sucesso, clique aqui para ve-lo</font>

