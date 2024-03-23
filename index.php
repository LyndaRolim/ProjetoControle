<?php
$dbConnection = mysqli_connect('localhost', 'root', '', 'registro');
if (!$dbConnection) {
  die ('Could not connect: ' . mysqli_error($dbConnection));
}

$query = "CREATE TABLE IF NOT EXISTS registros (
          id INT AUTO_INCREMENT PRIMARY KEY,
          ip VARCHAR(255) NOT NULL,
          dtSalva DATETIME NOT NULL
        )";

$dbConnection->query($query);

if (!empty ($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $ip_do_usuario = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
  $ip_do_usuario = $_SERVER['REMOTE_ADDR'];
}
$agora = date('Y-m-d H:i:s') . "<br><br>";
$usuario = $_SERVER['HTTP_USER_AGENT'];

//Insere os dados no banco
$get_data = "INSERT INTO registros (ip , dtSalva, acesso) VALUES (? , ?, ?)";

$insert_data = $dbConnection->prepare($get_data);
$insert_data->bind_param('sss', ...[$ip_do_usuario, $agora, $usuario]);
$insert_data->execute();
$insert_data->store_result();

// Verificando se o ID foi enviado via POST
if (isset ($_POST['excluir'])) {
  $id = $_POST['excluir'];

  // Query para excluir o registro com o ID especificado
  $sql = "DELETE FROM registros WHERE id = $id";

  if ($dbConnection->query($sql) === TRUE) {
    echo "<div class='alert alert-success' role='alert'>Registro excluído com sucesso!</div>";
  } else {
    echo "<div class='alert alert-warning' role='alert'>Erro ao excluir o registro: {$dbConnection->error}</div>";
  }
} else {
  echo "<div class='alert alert-warning' role='alert'>ID não especificado.</div>";
}

//Como pegar os valores da Query
$sql = "SELECT * 
        FROM registros
        WHERE ip <> '::1' 
        AND acesso IS NOT NULL
        ";

$line = "";

if ($result = $dbConnection->query($sql)) { ?>

  <head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
      integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
      integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
      crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
      integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
      crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
      integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
      crossorigin="anonymous"></script>
  </head>
  <table class="table table-striped table-dark">
    <h1 class="table table-striped table-dark text-center">Acessos anteriores ao Sistema</h1>
    <th scope="col">ID</th>
    <th scope="col">IP de Acesso</th>
    <th scope="col">Data de Acesso</th>
    <th scope="col">Local de Acesso</th>
    <th scope="col">Ação</th>
    <?php while ($obj = $result->fetch_object()) {
      $line = "<td>{$obj->id}</td>";
      $line .= "<td>{$obj->ip}</td>";
      $date = date('d/m/Y H:i:s', strtotime($obj->dtSalva));
      $line .= "<td>{$date}</td>";
      $line .= "<td>{$obj->acesso}</td>";
      $line .= "<td><form method='POST'><button type='submit' class='btn btn-danger' name='excluir' value='$obj->id'>Excluir</button></form></td>";
      ?>
      <tr>
        <?= $line ?>
      </tr>
      <?php
    }
    ?>
  </table>
  <?php

}

$result->close();
unset($obj);
unset($sql);
unset($query);

?>