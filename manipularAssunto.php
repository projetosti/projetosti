<?php

session_start();
require ("conexao.php");

$operacao = $_GET["operacao"];

if($operacao == "excluir"){

  mysqli_query($conexao, "DELETE FROM assunto WHERE id_assunto=".$_GET["id_assunto"]);
  $_SESSION["mensagem"]="<font color='green'>ASSUNTO DELETADO COM SUCESSO!!</font><br><br>";
  header("location: verAssunto.php");

}else if($operacao == "editar"){

  $id_assunto = $_GET["id_assunto"];
  $query = mysqli_query($conexao, "SELECT * FROM assunto WHERE id_assunto=$id_assunto");
  $query = mysqli_fetch_array($query);
  $assunto = $query["assunto"];

  echo "
  	<form method=\"POST\" action=\"manipularAssunto.php?id_assunto=$id_assunto&operacao=editando\">

  	Assunto:<br>
  	<textarea name=\"assunto\" rows=\"4\" cols=\"40\">$assunto</textarea>

  <input type=\"submit\" value=\"Editar Assunto\" name=\"cadastrar_atividade\">

  </form>
";
}else if($operacao == "editando"){
  $assunto=$_POST["assunto"];

  mysqli_query($conexao, "UPDATE assunto SET assunto='$assunto' WHERE id_assunto=".$_GET["id_assunto"]);
  $_SESSION["mensagem"]="<font color='green'>ASSUNTO EDITADO COM SUCESSO!!</font><br><br>";
  header("location: verAssunto.php");

}



?>
