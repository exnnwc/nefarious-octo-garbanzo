<?php

$connection = new PDO ("mysql:host=localhost;dbname=peeps", "root", "");

switch($_POST['function_to_be_called']){
    case "create":
        create($_POST['table'], $_POST['id'], $_POST['note']);
        break;
    case "delete":
        delete($_POST['id']);
        break;
}

function create($table, $id, $note){
    global $connection;
    $statement=$connection->prepare("insert into notes (owner_table, owner_id, note) values (?, ?, ?)");
    $statement->bindValue(1, $table, PDO::PARAM_STR);
    $statement->bindValue(2, $id, PDO::PARAM_INT);
    $statement->bindValue(3, nl2br($note), PDO::PARAM_STR);
    $statement->execute();

}

function delete ($id){
    global $connection;
    $statement=$connection->prepare ("update notes set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
