<?php

$connection= new PDO ("mysql:host=localhost;dbname=peeps", "root", "");

switch ($_POST['function_to_be_called']){
    case "create":
        create($_POST['profile_id'], $_POST['make'], $_POST['model'], $_POST['year'], $_POST['color'], $_POST['license'], $_POST['state']);
        break;
    case "delete":
        delete ($_POST['id']);
        break;
}

function create($profile_id, $make, $model, $year, $color, $license, $state){
    echo "0 $profile_id $make $model $year $color $license $state <BR>";
    global $connection;
    $statement=$connection->prepare ("insert into vehicles (owner, make, model, year, color, license, license_origin) values 
        (?, ?, ?, ?, ? , ?, ?)");
    $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
    $statement->bindValue(2, $make, PDO::PARAM_STR);
    $statement->bindValue(3, $model, PDO::PARAM_STR);
    $statement->bindValue(4, $year, PDO::PARAM_STR);
    $statement->bindValue(5, $color, PDO::PARAM_STR);
    $statement->bindValue(6, $license, PDO::PARAM_STR);
    $statement->bindValue(7, $state, PDO::PARAM_STR);
    $statement->execute();
}

function delete($id){
    $statement=$connection->prepare("update vehicles set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
