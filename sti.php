<?php

$json = json_decode(file_get_contents("php://input")); // o php://input recebe o json que o sisto está enviando
//$json = json_decode(file_get_contents("json/respostas.json"));//decodifica o arquivo json  e transforma em vetor

$existeJson = @$json[0]->{'id_aluno'}; // Verifica se existe o json
if($existeJson!=""){ // SE existir um json
  moduloPedagogico(); //chama o módulo pedagógico passando o $json como parametro
}else{ // se nao existir um json
    echo "NAO EXISTE ATIVIDADE CADASTRADA AINDA PARA ESTE ASSUNTO E ESTA RODADA"; // mensagem de informação
}

function moduloPedagogico(){//funcao módulo pedagógico

    $porcentagem = moduloAluno();//chamar a função do módulo ALUNO depois recebe o retorno da porcentagemcalculada na função

    global $json; //pegar vetor $json de maneira global
    $rodada = $json[0]->{'rodada'};//criar variavel $rodada que extrai o valor da rodada do vetor $json

    //criar estratégia

    if($porcentagem >=60){
      moduloEspecialista("proximo_assunto");//chamar o módulo ESPECIALISTA
    }else if($porcentagem <60){
      if($rodada <3)//verifica se a rodada é menor que 3
        moduloEspecialista("nova_rodada");//chamar módulo ESPECIALISTA
      else echo "Motivo da Discussão";// aparece mensagem se o aluno passou pelas 3 rodadas mas não atingiu o objetivo
    }

}


function moduloAluno(){//modulo aluno recebe por paramentro a variavel $json que contem os valores decodificados do json

  global $json;
  require("conexao.php");// faz conexão com o banco

  $id_aluno= $json[0]->{'id_aluno'};//criar variavel $id_aluno que extrai o valor id_aluno do vetor $json
  $rodada = $json[0]->{'rodada'};//criar variavel $rodada que extrai o valor da rodada do vetor $json
  $id_assunto = $json[0]->{'id_assunto'};//criar variavel $id_aluno que extrai o valor do id_aluno do vetor $json
  $bateria_de_atividades = $json[0]->{'bateria_de_atividades'};
  $correto=0;
  $incorreto=0;

  $respostas = $json[0]->{'respostas'};//criar variavel $respostas que extrai o valor respostas do vetor json -esta variavel será um vetor pois há mais de uma resposta.
    $cont = count($respostas); //conta o numero de respostas tem dentro do vetor
    $bateria = md5("$id_aluno-$id_assunto-$rodada");

    $relatorio_aluno = "";
    $x=0;     //percorre respostas do aluno
    while($x <$cont){ //entra no laço de repetição para extarir os valores do vetor
      $id_atividade = $respostas[$x]->{'id_atividade'}; //criar variavel $id_atividade para extrair o valor vo vetor $respostas[$x]->{'id_atividade'}
      $resposta_aluno = $respostas[$x]->{'resposta_aluno'};//criar variavel $resposta_aluno para extrair o valor vo vetor $respostas[$x]->{'resposta_aluno'}
      $alternativa_marcada= $respostas[$x]->{'alternativa_marcada'};//criar variavel $alternativa_marcada para extrair o valor vo vetor $respostas[$x]->{'alternativa_marcada'}


      $query = mysqli_query ($conexao, "INSERT INTO respostas (id_aluno, id_atividade,id_assunto, rodada, resposta_aluno, alternativa_marcada, bateria) VALUES ('$id_aluno', '$id_atividade', '$id_assunto', '$rodada', '$resposta_aluno', '$alternativa_marcada', '$bateria')") or die(mysqli_error($conexao)); /*insere os valores extraidos do vetor na tabela respostas*/

      //GERA RELATÓRIO PARA ALUNO
      $json_relatorios_alunos = array(); // cria array para gerar o JSON relatório
      $relatorio_aluno = mysqli_query($conexao, "SELECT * FROM bd_atividades WHERE id_atividade=$id_atividade");
      //Busca na tabela atividades as atividades respondidas pelo aluno

      while($resposta_banco = mysqli_fetch_array($relatorio_aluno)){ //percorre linhas do banco de dados

        $json_relatorio_aluno = array();
        $alternativa_marcada_letra='';

        if($alternativa_marcada == 0) $alternativa_marcada_letra = 'a';
        if($alternativa_marcada == 1) $alternativa_marcada_letra = 'b';
        if($alternativa_marcada == 2) $alternativa_marcada_letra = 'c';
        if($alternativa_marcada == 3) $alternativa_marcada_letra = 'd';
        if($alternativa_marcada == 4) $alternativa_marcada_letra = 'e';

        $z = $x+1;

        $json_relatorio_aluno["questao"] = $z;
        $json_relatorio_aluno["desc_atividade"] = $resposta_banco["desc_atividade"];
        $json_relatorio_aluno["alternativa_marcada_letra"] = $alternativa_marcada_letra;
        $json_relatorio_aluno["alternativa_marcada"] = $resposta_banco["alternativa$alternativa_marcada"];

        //Se a resposta do aluno for igual a resposta do banco de dados para esta atividade
        if($resposta_banco["alternativa_correta"] == $alternativa_marcada_letra){
          $json_relatorio_aluno["resultado"] = "ACERTOU!!"; // Mostra acertou
        }else{ //Caso contrário
          $json_relatorio_aluno["resultado"]="INCORRETO!! Motivo do erro: ".  $resposta_banco["justificativa_erro_alternativa$alternativa_marcada"];// mostra erro e o motivo do erro $resposta_banco["justificativa_erro_alternativa$alternativa_marcada"];
        }
        array_push($json_relatorios_alunos, $json_relatorio_aluno); //adiciona o relatorio do aluno no array principal
      }
      $json_relatorio =  json_encode($json_relatorios_alunos, JSON_PRETTY_PRINT);
      echo $json_relatorio; // gera relatório e envia JSON para o SISTO exibir para o aluno
      //FIM DO GERAR RELATORIO



      //verifica quantos errou e quantos acertou para calcular porcentagem
      if($resposta_aluno == "certa"){// compara se a resposta do aluno é igual a "certa" pra poder contar o numero de acertos
        $correto++; //incrementa o numero de acertos
      }else if($resposta_aluno == "errada"){//compara se a resposta do aluno é igual a "errada" pra poder contar o numero de erros
        $incorreto++;//incrementa o numero de erros
      }

      $x++; /*incrementa $x*/
    }

    $porcentagem = ($correto*100)/$bateria_de_atividades; //faz cálculo da porcentagem de acertos e/ou erros

    $query = mysqli_query ($conexao, "INSERT INTO historico (id_aluno, porcentagem, bateria) VALUES ('$id_aluno', '$porcentagem', '$bateria')") or die(mysqli_error($conexao));

    return $porcentagem;//retorna porcentagem
}


//A função para o módulo do especialista tem como objetivo receber o resultado da avaliação feito pelo moduloPedagogico (que no caso será se o aluno vai para o próximo assunto ou se faz uma nova rodada no mesmo assunto), e gera uma nova bateria de atividades de acordo com este resultado, e grava o arquivo json resultante em disco.
function moduloEspecialista($resultado_avaliacao){

  require("conexao.php");
  global $json;
  $query = "";

  $id_aluno= $json[0]->{'id_aluno'};
  $id_assunto=$json[0]->{'id_assunto'};
  $rodada= $json[0]->{'rodada'};

  $bateria_de_atividades = $json[0]->{'bateria_de_atividades'};

  // Se o resultado da avaliacao do moduloPedagogico for igual a proximo_assunto, entao o moduloEspecialista somar um ao id_assunto, representando que o aluno irá para o próximo assunto, e determina que iniciará na rodada 1 e gera a query para selecionar mais atividades de acordo com o resultado anterior.
  if($resultado_avaliacao == "proximo_assunto"){
    $id_assunto++;
    $query =  "SELECT * FROM bd_atividades WHERE rodada='1' AND id_assunto='$id_assunto' LIMIT $bateria_de_atividades";
  }
  //Caso contrário, caso o resultado da avaliacao seja nova_rodada, o moduloEspecialista irá somar um na rodada, representando que o aluno irá continuar no mesmo assunto e indo para uma nova rodada
  else if($resultado_avaliacao == "nova_rodada"){
    $rodada++;
    $query = "SELECT * FROM bd_atividades WHERE rodada='$rodada' AND id_assunto='$id_assunto' LIMIT $bateria_de_atividades";
  }

  $query=mysqli_query($conexao, $query) or die(mysqli_error($conexao));
  $array_atividades= array();

  while($sql = mysqli_fetch_array($query)) {
    $array_atividade=array();

    $id_atividade = $sql['id_atividade'];
    $array_atividade["id_atividade"]=$id_atividade;

    $id_assunto = $sql['id_assunto'];
    $array_atividade["id_assunto"]=$id_assunto;

    $desc_atividade = $sql['desc_atividade'];
    $array_atividade["desc_atividade"]=$desc_atividade;

    $imagem_atividade = $sql['imagem_atividade'];

    //Uma modificação foi feita no código funcionar no SISTO PARA MOBILE
    $array_imagem_atividade = array();
    $array_imagens_atividade = array();

    if($imagem_atividade==""){
      $array_imagem_atividade["imagem_atividade"] = "http://localhost/tcc_7/"."transparente.png";
      $array_imagem_atividade["tamanho"] = 1;
    }else{
      $array_imagem_atividade["imagem_atividade"]="http://localhost/tcc_7/".$imagem_atividade;
      $array_imagem_atividade["tamanho"] = 100;
    }

    array_push($array_imagens_atividade, $array_imagem_atividade);

    $array_atividade["atividade"]=$array_imagens_atividade;


    //FIM da Modificação para funcionar no SISTO PARA MOBILE

    $x=1;
    $array_alternativas = array();

    //O array_alternativas foi criado para armazenar os dados das novas atividades vindas do banco de dados, facilitando assim a criação do arquivo json para armazenamento em disco.
    while($x<=5){
      $array_alternativa = array();
      $alternativa[$x]= $sql["alternativa$x"];
      $array_alternativa["alternativa"]=$alternativa[$x];

      $imagem_alternativa[$x] = $sql["imagem_alternativa$x"];

      //Modificação para funcionar no SISTO PARA MOBILE
      if($imagem_alternativa[$x]==""){
        $array_alternativa["imagem_alternativa"] = "http://localhost/tcc_7/"."transparente.png";
        $array_alternativa["tamanho"] = 1;
      }else{
        $array_alternativa["imagem_alternativa"] = "http://localhost/tcc_7/".$imagem_atividade;
        $array_alternativa["tamanho"] = 100;
      }
      //FIM da Modificação para funcionar no SISTO PARA MOBILE

      $array_alternativa["imagem_alternativa$x"]="http://localhost/tcc_7/".$imagem_alternativa[$x];

      $justificativa_erro_alternativa[$x]=$sql["justificativa_erro_alternativa$x"];
      $array_alternativa["justificativa_erro_alternativa$x"]=$justificativa_erro_alternativa[$x];

      array_push($array_alternativas, $array_alternativa);

      $x++;
    }
    $array_atividade["alternativas"] =$array_alternativas;

    $alternativa_correta = $sql['alternativa_correta'];
    $array_atividade['alternativa_correta']=$alternativa_correta;

    $rodada = $sql['rodada'];
    $array_atividade['rodada']=$rodada;

    $assunto = $sql['assunto'];
    $array_atividade['assunto']=$assunto;

    $data_de_cadastro = $sql['data_de_cadastro'];
    $array_atividade['data_de_cadastro']=$data_de_cadastro;

    array_push($array_atividades, $array_atividade);
  }

  $json_atividades = json_encode($array_atividades, JSON_PRETTY_PRINT); //a função do javascript json_encode() serve para codificar/comprimir o $array_atividades para um formato JSON. O JSON_PRETTY_PRINT serve pra organizar o JSON

  $nome_do_arquivo =  "$id_aluno-$id_assunto-$rodada.json"; // esta variável recebe o id_aluno, id_assunto e $rodada de acordo com o resultado dado pelo modulo pedagogico. Eles são todos concatenados juntamente com o nome da pasta json/ antes e com a string .json no final para representar o tipo dele.

  $arquivo = fopen("json/".$nome_do_arquivo, "w");  //a função fopen do PHP cria em disco o arquivo com o nome especificado.


  fwrite($arquivo, $json_atividades); //função que grava o json_atividades contendo os dados das novas atividades dso aluno em JSON em disco (HDD).

  echo "GEROU ".$nome_do_arquivo;

  fclose($arquivo); //tira o arquivo da memória temporária

}
