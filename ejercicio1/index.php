<?php

    #https://stackoverflow.com/questions/9139202/how-to-parse-a-csv-file-using-php

    //Abrimos el archivo para decodificar
    $users = [];
    try
        {
            // Step 1 : Check if csv exist
            $fileName = './decodificar.csv';
            if ( !file_exists($fileName) ) {
                $error = "File not found";
                throw new Exception($error);
                
            }
            /*
            Step 2 : Open file and store in custom variable to handle asuming Column 1 is name,etc
            */
            
            $handle = fopen($fileName, "r");
            while (($row = fgetcsv($handle)) !== FALSE) {
                #I will retrieve the information to handle properly
                $temp_array = [];
                $temp_array ["user_name"] = $row[0];
                $temp_array ["numeric_system"] = $row[1];
                $temp_array ["coded_punctuation"] = $row[2];
                $temp_array ["position"] = "";
                array_push($users,$temp_array);
            }
            fclose($handle);
            //Step 3 Get the position of the coded_punctuation
            $colors = array("red", "green", "blue", "yellow");

            $users_position = 0;
            foreach ($users as $user) {
                $temp_array = [];
                foreach (str_split($user["numeric_system"]) as $char) {
                    array_push($temp_array,$char);
                }
                $temp_coded_punctuation_string = "";
                foreach (str_split($user["coded_punctuation"]) as $char) {
                    
                    $position = 0;
                    foreach ($temp_array as $character_position) {
                        if ($character_position == $char){
                            $temp_coded_punctuation_string .=$position;
                            break;
                        }
                    $position +=1;
                    }
                    
                }
                $users[$users_position]["position"] = $temp_coded_punctuation_string; 
                $users_position +=1;
                

              
            } 

    } catch ( Exception $e ) {
        echo $e;
    // send error message if you can
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejercicio 1</title>
</head>
<body>
    <?php 
    if (!empty($users)) {
        ?>
        <table>
        <tr>
            <th>user_name</th>
            <th>numeric_system</th>
            <th>coded_punctuation</th>
            <th>position</th>
        </tr>
        <?php
        $html_element = "";
        foreach ($users as $user) {
            $tr = "<tr>";
            $tr .= "<td>" . $user["user_name"] . "</td>";
            $tr .= "<td>" . $user["numeric_system"] . "</td>";
            $tr .= "<td>" . $user["coded_punctuation"] . "</td>";
            $tr .= "<td>" . $user["position"] . "</td>";
            $tr .= "</tr>";
            $html_element .=$tr;
        }
        echo $html_element;
        ?>
        </table>
        <?php
    }
    
    ?>
    
</body>
</html>

