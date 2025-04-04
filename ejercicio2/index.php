<?php
    $servername = "database";
    $username = "root";
    $password = "root";
    $database = "root";
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "SHOW TABLES LIKE 'users'";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $count = $stmt->fetchColumn();
    if (!$count){
        try {

            $sql = "CREATE TABLE users (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                dni VARCHAR(30) NOT NULL,
                telephone VARCHAR(30) NOT NULL,
                email VARCHAR(50),
                schedule_type VARCHAR(50),
                hour INT(255) NOT NULL,
                date VARCHAR(30) NOT NULL
                );";
            /*
            $sql = "CREATE TABLE users (
                id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(30) NOT NULL,
                dni VARCHAR(30) NOT NULL,
                telephone VARCHAR(30) NOT NULL,
                email VARCHAR(50),
                schedule_type VARCHAR(50),
                date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )";*/
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } catch(PDOException $e) {
            echo $e;
        }
    }

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    html {
  min-height: 100%;/* fill the screen height to allow vertical alignement */
  display: grid; /* display:flex works too since body is the only grid cell */
}

body {
  margin: auto;
}
.message {
  margin: auto;
}
.error{
    background-color:red;
    color:white
}
.success{
    background-color:green;
    color:white
}
</style>
<body>
    <form>
      <label for="name">Nombre:</label><br>
      <input name="name" id="name" value="" placeholder="nombre"><br>
      <label for="dni">DNI:</label><br>
      <input name="dni" id="dni" value="" placeholder="dni"><br>
      <label for="telephone">Teléfono:</label><br>
      <input name="telephone" id="telefono" value="" placeholder="teléfono"><br>
      <label for="email">Email:</label><br>
      <input name="email" id="email" value="" placeholder="email"><br>
      <label for="schedule_type">Tipo de consulta:</label><br>
      <select name="schedule_type" id="schedule_type">
        <option value="primera_consulta">Primera Consulta</option>
      </select>
      <input name="submit" type="submit" value="Submit">

    </form>
    <div class="message"></div>
  </body>
  <script src="./assets/js/jquery.js"></script>
  <script>
    $(function () {
        $("#dni").focusout(function(){
            $.ajax({  
            type: 'POST',  
            url: './assets/php/query.php',
            data: { dni_verify: $("#dni").val() },
            success: function(data) {
                var data = $.parseJSON(data);
                if (data.success == true){
                    if ($("#schedule_type option[value='revision']").length > 0){
                        
                    }else{
                        $('#schedule_type').append($('<option>', { 
                            value: "revision",
                            text : "REVISION" 
                        }));
                    }
                }else{
                    $("#schedule_type option[value='revision']").remove()
                    
                }
            
            }
        });
            
            
        });
        
        function add_error_class(message) {
            $(".message").removeClass("success");
            $(".message").removeClass("error");
            $(".message").addClass("error");
            $(".message").html(message);
            
        }

        function remove_error_class() {
            $(".message").html("");
            $(".message").removeClass("error");
            
        }

        function success_post(message){
            $(".message").html(message);
            $(".message").removeClass("error");
            $(".message").addClass("success");
        }
        function error_post(message){
            $(".message").html(message);
            $(".message").removeClass("success");
            $(".message").addClass("error");
        }

        $('form').on('submit', function (e) {
           e.preventDefault();

            if ($("#name").val() != ""){
                remove_error_class()
            }else{
                add_error_class("Campo Nombre vacio");
            return
            }

            if ($("#dni").val() != ""){
                remove_error_class()
            }else{
                add_error_class("Campo DNI vacio");
            return
            }

            if ($("#telefono").val() != ""){
                remove_error_class()
            }else{
                add_error_class("Campo Telefono vacio");
            return
            }

            if ($("#email").val() != ""){
                remove_error_class()
            }else{
                add_error_class("Campo Email vacio");
            return
            }

           /*Email verification */
           if(!isEmail($("#email").val())){
            add_error_class("Email incorrecto");
            return
           }else{
            remove_error_class()
           }
           

        $.ajax({
            type: 'post',
            url: './assets/php/query.php',
            data: $('form').serialize(),
            success: function (data) {
                
                var data = $.parseJSON(data);
                if (data.success == true){
                    success_post(data.message)
          
                }else{
                    error_post(data.message)
                    
                }
                
            }
        });

        });

        });

        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }

  </script>

</html>