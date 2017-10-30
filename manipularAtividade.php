<head>
    <script>
     var anterior="";

     function desabilitar(justificativa){

	 if(anterior!=""){
	     document.getElementById(anterior).disabled = false;
	 }

	 document.getElementById(justificativa).disabled = true;
	 anterior = justificativa;

     }
    </script>
</head>

<body>

    <?php
    session_start();

    $rodada = $_SESSION["rodada"];
    $assunto = $_SESSION["assunto"];

    require ("conexao.php");
    $id_atividade=@$_GET["id_atividade"];

    if(@$_GET["operacao"]=="editar"){
	$sql = mysqli_query($conexao, "SELECT * FROM bd_atividades WHERE id_atividade=$id_atividade") or die (mysqli_error($conexao));
  	$row = mysqli_fetch_array($sql);

	$correta = $row["alternativa_correta"];

	$x=0;

	echo "<h1>EDITAR ATIVIDADE</h1>";

	echo "<form method='POST' action='manipulandoAtividade.php?id_atividade=".$row['id_atividade']."' enctype='multipart/form-data'>";
	echo "Id Atividade: ". $row['id_atividade']."<br>";

    }else if(@$_GET["operacao"]==""){

	echo "<h1>CADASTRAR ATIVIDADE</h1>";

	echo "<form method='POST' action='manipulandoAtividade.php' enctype='multipart/form-data'>";
    }
    ?>

    <input type="hidden" name="rodada" value="<?=$rodada?>">
    <input type="hidden" name="assunto" value="<?=$assunto?>">
    <!--Assunto: <?=$_SESSION["assunto"]?></b><br>
    <b>Rodada: <?=$_SESSION["rodada"]?></b><br-->

    <b><font color=blue>Assunto: <?=$assunto?></b><br>
        <b>Rodada: <?=$rodada?></font></b><br>

	Atividade:<br>
	<textarea name="desc_atividade" rows="10" cols="40" placeholder="Escreva aqui o exercício"><?=@$row['desc_atividade']?></textarea>

	<?php
	$img_atividade = @$row['imagem_atividade'];
	if(@$_GET["operacao"]=="editar" && $img_atividade!=""){
	    echo "<img src='$img_atividade' width='275'>";
	}
	?>
  	<br>Descrever aqui o exercicio<br>
	Imagem: <input type="file" name="imagem_atividade" accept="image/jpeg, image/png" > <br><br>

	Alternativas: (Marque ao lado das alternativas a correta).<br>


	<?php
	$correta = @$row['alternativa_correta'];
	$x = 1;
	while($x<6){
            if($x==1) $letra = "a";
            if($x==2) $letra = "b";
            if($correta == "a"){ echo "checked"; $x=1;}
            if($x==3) $letra = "c";
            if($x==4) $letra = "d";
            if($x==5) $letra = "e";

	?>

	<input type="radio" name="alternativa_correta" value="<?=$letra?>" onclick="desabilitar('justificativa_erro_alternativa<?=$x?>')" <?php if($correta == "$letra"){ echo "checked"; $desabilitar=$x;}?> required>

	<?=$letra?>) <textarea name="descricao_alternativa<?=$x?>" required placeholder="Descreva aqui a alternativa <?=$letra?>" cols=40><?=@$row["alternativa$x"]?></textarea><br>

	<textarea name ="justificativa_erro_alternativa<?=$x?>" id="justificativa_erro_alternativa<?=$x?>" rows="10" cols="40" required placeholder="Descreva aqui o motivo dessa alternativa não estar correta caso não seja a certa"><?=@$row["justificativa_erro_alternativa$x"]?></textarea>

	<?php
	$img_alternativa = @$row["imagem_alternativa$x"];
	if(@$_GET["operacao"]=="editar" && $img_alternativa!=""){
	    echo "<img src='$img_alternativa' width='275'>";
	}
	?>
	<br><br>
	Imagem: <input type="file" name="imagem_alternativa<?=$x?>" accept="image/jpeg, image/png" > <br><br>
	<br>
	<?php
	$x++;
	}

	if(@$_GET["operacao"]=="editar"){
	    echo "  <input type='submit' value='FINALIZAR EDIÇÃO' id='cadastrando' name='cadastrastrando_atividade'>";
	}else {
	    echo "  <input type='submit' value='CADASTRAR ATIVIDADE' id='cadastrando' name='cadastrastrando_atividade'>";
	}
	?>
</form>

<script>
 desabilitar("justificativa_erro_alternativa<?=$desabilitar?>");
</script>
