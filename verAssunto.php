<script>
function excluir(id){

  if(confirm("Deseja realmente deletar essa atividade?")){
	window.location.href="excluir.php?id_atividade="+id;
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


echo "<a href='cadastroAssunto.php'>Cadastrar novo assunto</a> &nbsp;&nbsp; <a href='index.php'>Escolher Assunto e Rodada</a>";

$sql = mysqli_query($conexao, "SELECT * FROM assunto") or die (mysqli_error($conexao));
$cont = mysqli_num_rows($sql);
if($cont==0){
echo "<br><br><font color=red>NENHUM ASSUNTO CADASTRADO!!!";
}else{

echo "Número de assuntos encontrado : ". mysqli_num_rows($sql);

echo "<br><br>";
echo "<table border>";
echo "<tr><td></td>
<td>Assunto</td>

</tr>";
while ($row = mysqli_fetch_array($sql)) {

			  $id = $row["id_assunto"];
			  $assunto=$row["assunto"];

echo "<tr><td> <a href='manipularAssunto.php?id_assunto=$id&operacao=editar'> Editar</a> &nbsp; <a href='manipularAssunto.php?id_assunto=$id&operacao=excluir'> Excluir </a></td><td>".$assunto."</td><tr>";


}
echo "</table>";

}
?>
