<?php
  function Conection(){
    
    $mysql = mysqli_connect('localhost', 'root', '', 'registro');
    if (!$mysql) {
      die('Could not connect: ' . mysqli_error($mysql));
    }
    return $mysql;
  }

  class Database{
    public static function criarTabela(){
      try {
        $dbConnection = Conection();

        $query = "CREATE TABLE IF NOT EXISTS registros (
          id INT AUTO_INCREMENT PRIMARY KEY,
          ip VARCHAR(255) NOT NULL,
          dtSalva DATETIME NOT NULL
        )";

        $dbConnection->query($query);

        return "Tabela Registros criada com sucesso";
      } catch (Exception $e) {
        error_log("Erro na criação da tabela 'Registros': " . $e->getMessage());
        return "Erro na criação da tabela 'Registros': " . $e->getMessage();
      }
    }
  }

  $dbBase = new Database();
  echo $dbBase->criarTabela(); 
  
?>