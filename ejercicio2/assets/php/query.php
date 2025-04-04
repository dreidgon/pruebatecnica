<?php


    $servername = "database";
    $username = "root";
    $password = "root";
    $database = "root";

    $from = 10;
    $to = 22;
    $date_today = date("Y-m-d");
    try {
      $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
      // set the PDO error mode to exception
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      
        if( isset($_POST['dni_verify']) ){
          $sql = "SELECT COUNT(*) FROM users WHERE dni = :dni";
          $stmt = $conn->prepare($sql);
          $stmt->execute(['dni' => $_POST['dni_verify']]);
          $count = $stmt->fetchColumn();

          if ($count > 0) {
            $message_response = [];
            $message_response["success"] = true;
            $message_response["message"] = "Dni exist";
            echo json_encode($message_response);
          } else {
            $message_response = [];
            $message_response["success"] = "false";
            $message_response["message"] = "No user with that dni";
            echo json_encode($message_response);
          }
        }else{
          /*We check for the last hour to ensure schedule correction */
          $sql = "SELECT hour FROM `users` ORDER BY hour DESC;";
          $stmt = $conn->prepare($sql);
          $stmt->execute();
          $count = $stmt->fetchColumn();
          if ($count > 0){
            if ($count == "21"){
              $from = "10";
              $date_today = date("Y-m-d", time() + 86400);
            }else{
              $from = (int)$count + 1;
            }
            
          }

          /*Check to see if it is a new user*/
          $sql = "SELECT COUNT(*) FROM users WHERE dni = :dni";
          $stmt = $conn->prepare($sql);
          $stmt->execute(['dni' => $_POST['dni']]);
          $count = $stmt->fetchColumn();

          if ($count > 0) {
            /*Exist, we dont need to create it, just update it */
            $sql = "UPDATE users SET hour= :hour,schedule_type= :schedule_type, date = :date  WHERE dni= :dni";
            $stmt = $conn->prepare($sql);
            $data = [
              'hour' => $from,
              'date' =>  $date_today,
              'schedule_type' => $_POST['schedule_type'],
              'dni' => $_POST['dni']
            ];
            $stmt->execute($data);
  
            $count = $stmt->rowCount();
            if($count =='0'){
              $message_response = [];
              $message_response["success"] = false;
              $message_response["message"] = "FAILED";
              echo json_encode($message_response);
            }else{
              $message_response = [];
              $message_response["success"] = true;
              $message_response["message"] = "Cita actualizada para el " . $date_today . " a las  " . $from;
              echo json_encode($message_response);
              
              }

           } else {
            /*We need to create it first */
            $sql = "INSERT INTO users (name, dni, telephone,email,schedule_type,date,hour) VALUES (?,?,?,?,?,?,?)";
            $stmt= $conn->prepare($sql);
            $stmt->execute(
              [
                $_POST['name'],
                $_POST['dni'],
                $_POST['telephone'],
                $_POST['email'],
                $_POST['schedule_type'],
                $date_today,
                $from
              ]);
            $count = $stmt->rowCount();
            if($count =='1'){
              $message_response = [];
              $message_response["success"] = true;
              $message_response["message"] = "Nuevo usuario añadido, cita para el " . $date_today . " a las  " . $from;
              echo json_encode($message_response);
            }
             
          }
        }
    } catch(PDOException $e) {
      $message_response = [];
      $message_response["success"] = false;
      $message_response["message"] = "Connection failed: " . $e->getMessage();
      echo json_encode($message_response);
    }
