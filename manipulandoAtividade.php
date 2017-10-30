<?php
$operecao = $_GET["operacao"];
include("conexao.php");
session_start();

if($operacao == "excluir"){

  $id_atividade=$_GET["id_atividade"];
  mysqli_query($conexao," DELETE FROM bd_atividades where id_atividade=$id_atividade") or die(mysqli_error($on));
  $_SESSION["mensagem"] = "<font color=green>Excluído com sucesso!</font><br><br>";
  header ('location: verAtividade.php');

}else if($operacao == ""){

  $assunto = $_SESSION["assunto"];
  $rodada = $_SESSION["rodada"];

  $pedaco_sql = "";

  //Descrição da atividade
  $desc_atividade = $_POST['desc_atividade'];

  //Manipulacao da imagem atividade
  $arquivo_atividade = $_FILES['imagem_atividade']["name"];

  if($arquivo_atividade!=""){
    $destino_imagem_atividade = 'imagens/' .md5($arquivo_atividade . time().date("Y-m-d h:i:sa").rand(1,1000).rand(1,1000).rand(1,1000));

    if($_FILES["imagem_atividade"]["type"] == "image/jpeg")
  	  $destino_imagem_atividade = $destino_imagem_atividade.".jpg";
    else
  	  $destino_imagem_atividade = $destino_imagem_atividade.".png";

    $arquivo_tmp_atividade = $_FILES['imagem_atividade']['tmp_name'];
    move_uploaded_file( $arquivo_tmp_atividade, $destino_imagem_atividade );

    $pedaco_sql = $pedaco_sql . " imagem_atividade='$destino_imagem_atividade', ";
  }

  $x = 1;
  while($x<6){
     //Descrição da alternativa (a/1)
     $descricao_alternativa[$x] = $_POST["descricao_alternativa$x"];

     $pedaco_sql = $pedaco_sql . "alternativa$x='".$descricao_alternativa[$x]."', ";

     echo "descricao_alternativa$x <br>";
     //Manipulacao da imagem Alternativa $
     $arquivo_alternativa[$x] = $_FILES["imagem_alternativa$x"]["name"];
     if($arquivo_alternativa[$x]!=""){
        $destino_imagem_alternativa[$x] = 'imagens/' .md5($arquivo_alternativa[$x] . time().date("Y-m-d h:i:sa").rand(1,1000).rand(1,1000).rand(1,1000));

        if($_FILES["imagem_alternativa1"]["type"] == "image/jpeg")
  	     $destino_imagem_alternativa[$x] = $destino_imagem_alternativa[$x] . ".jpg";
        else
  	     $destino_imagem_alternativa[$x] = $destino_imagem_alternativa[$x] . ".png";

         $arquivo_tmp_alternativa[$x] = $_FILES["imagem_alternativa$x"]['tmp_name'];
         move_uploaded_file( $arquivo_tmp_alternativa[$x], $destino_imagem_alternativa[$x]);

         $pedaco_sql = $pedaco_sql . "imagem_alternativa$x='".$destino_imagem_alternativa[$x]."', ";
     }

     $justificativa_erro_alternativa[$x]= $_POST["justificativa_erro_alternativa$x"];

     $pedaco_sql = $pedaco_sql . "justificativa_erro_alternativa$x='".$justificativa_erro_alternativa[$x]."', ";

     $x++;
  }

  $descricao_alternativa_correta = $_POST["alternativa_correta"];
  $rodada = $_POST["rodada"];

  //Faz conexão com o banco
  require("conexao.php");
  if (mysqli_connect_errno($conexao))//pega a conexão e verifica se houve erros de conexão
  {
  	echo "Probelmas para conectar. Erro:";
  	echo mysqli_connect_error();//retorna um texto explicanso o que aconteceu de errado ao conectar com o banco
  	die(); //encerra o programa aqui
  } //está em um laço de repetição para verificar se houve erro ou não
  else{

  	if($_GET["id_atividade"]==""){
      $id_assunto = $_SESSION["id_assunto"];

  	$query = "INSERT INTO bd_atividades(id_assunto, desc_atividade,
  imagem_atividade, alternativa1, imagem_alternativa1, justificativa_erro_alternativa1, alternativa2, 		imagem_alternativa2, justificativa_erro_alternativa2, alternativa3, imagem_alternativa3, 			justificativa_erro_alternativa3, alternativa4, imagem_alternativa4, justificativa_erro_alternativa4, 			alternativa5, imagem_alternativa5, justificativa_erro_alternativa5,alternativa_correta, rodada, assunto, data_de_cadastro)
  	VALUES
    		('$id_assunto', '$desc_atividade', '$destino_imagem_atividade', '".$descricao_alternativa[1]."', '".$destino_imagem_alternativa[1]."', '".$justificativa_erro_alternativa[1]."', '".$descricao_alternativa[2]."', '".$destino_imagem_alternativa[2]."', '".$justificativa_erro_alternativa[2]."', '".$descricao_alternativa[3]."', '".$destino_imagem_alternativa[3]."', '".$justificativa_erro_alternativa[3]. "', '".$descricao_alternativa[4]."', '".$destino_imagem_alternativa[4]."', '".$justificativa_erro_alternativa[4]."', '".$descricao_alternativa[5]."', '".$destino_imagem_alternativa[5]."', '".$justificativa_erro_alternativa[5]."', '$descricao_alternativa_correta', '$rodada', '$assunto', NOW())";
  }else{
  	$id = $_GET["id_atividade"];

  $query = "UPDATE bd_atividades SET desc_atividade='$desc_atividade', $pedaco_sql alternativa_correta='$descricao_alternativa_correta', rodada='$rodada', assunto='$assunto', data_de_cadastro=NOW() WHERE id_atividade	='$id'";

  echo $query;
  }

   mysqli_query($conexao, $query) or die(mysqli_error($conexao));
    header("location: verAtividade.php");
  }
}
?>
