<?php
//https://stackoverflow.com/questions/6281963/how-to-build-a-json-array-from-mysql-database
//https://stackoverflow.com/questions/11545661/php-force-download-json
//https://pt.slideshare.net/MarcioJuniorVieira/criando-e-consumindo-webservice-rest-com-php-e-json
$return_arr = array();

include("conexao.php");

$fetch = mysqli_query($conexao, "SELECT * FROM bd_atividades");

while ($row = mysqli_fetch_array($fetch)) {
    $row_array['id_atividade'] = $row['id_atividade'];
    $row_array['id_assunto'] = $row['id_assunto'];
    $row_array['desc_atividade'] = $row['desc_atividade'];
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
    $row_array['assunto'] = $row['assunto'];
    $row_array['data_de_cadastro'] = $row['data_de_cadastro'];

    array_push($return_arr,$row_array);
}

header('Content-disposition: attachment; filename=atividades.json');
header('Content-type: application/json');

echo json_encode($return_arr, JSON_PRETTY_PRINT);

?>
