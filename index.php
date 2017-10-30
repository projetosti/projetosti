<?php
	session_start();
	echo @$_SESSION["mensagem"];
	@$_SESSION["mensagem"]="";

session_destroy();
?>
	<meta charset="utf8" />
		<form method="POST" enctype="multipart/form-data" action="verAtividade.php" >
				Escolha um assunto para sua atividade:<br>
					<?php
					require("conexao.php");

					$sqli = mysqli_query($conexao ,"SELECT * FROM assunto")or die (mysqli_error());
					$conta = mysqli_num_rows($sqli);

					if($conta==0){
						echo "<font color=red>Nenhum assunto cadastrado. Cadastre no botão abaixo.</font>";
					}else{
		   			echo "<select name='assunto'required>";
						echo "<option></option>";
	 	   			while ($registro = mysqli_fetch_array($sqli)) {
		      		$assunto = $registro["assunto"];
							$id_assunto = $registro["id_assunto"];
		      		echo "<option value='$id_assunto - $assunto'>$id_assunto - $assunto</option><br><br>";
		   			}
		   echo "</select><br><br>
 		 	Escolha uma rodada:	<br>
	  		<input type='radio' name='rodada' value='1' required> Rodada 1<br>
	  		<input type='radio' name='rodada' value='2' required> Rodada 2<br>
	  		<input type='radio' name='rodada' value='3' required> Rodada 3<br><br>
				<input type='submit' value='Selecionar Assunto e Rodada' id='selecionar_assunto' name='selecionar_assunto'>";
				}
					?>
				<a href="cadastroAssunto.php">Cadastrar Novo Assunto</a>
				<a href="verAssunto.php">Editar ou Excluir Assunto</a>
		</form>
