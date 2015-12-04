<?php
$connection= new PDO ("mysql:host=localhost;dbname=peeps", "root", "");

switch ($_POST['function_called']){
    case "new_alias":
        create_new_alias($_POST['profile_id'], $_POST['new_alias']);
        break;
    case "delete_alias":
        delete_alias($_POST['id']);
        break;
    case "change_rank":
        change_rank($_POST['id'], $_POST['is_direction_up']);
        break;
}

function change_rank($id, $is_direction_up){
    global $connection;
    $statement=$connection->prepare("select * from aliases where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $main_record=$statement->fetchObject();
    if ($is_direction_up){
        $new_rank=$main_record->rank-1;
    } else {
        $new_rank=$main_record->rank+1;
    }
    $other_record=$connection->query("select * from aliases where active=1 and owner=".
      $main_record->owner." and rank=".$new_rank)->fetchObject();
    $connection->exec("update aliases set rank=".$main_record->rank." where id=".$other_record->id);
    $connection->exec("update aliases set rank=".$new_rank." where id=".$main_record->id);
}
function create_new_alias($profile_id, $alias){
    new_alias($profile_id, $alias, fetch_next_rank_of($profile_id));
}

function delete_alias($id){
    global $connection;
    $statement=$connection->prepare("update aliases set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
}
function number_of_aliases_for($profile_id){
    global $connection;
    $statement=$connection->prepare("select count(*) from aliases where owner=?");
    $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}

function fetch_next_rank_of($profile_id){
    global $connection;
    $statement=$connection->prepare("select rank from aliases where owner=? order by rank desc limit 1");
    $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn()+1;
}
function new_alias($profile_id, $alias, $new_rank){
    global $connection;
    $statement=$connection->prepare("insert into aliases (owner, name, rank) values (?, ?, ?)");
    $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
    $statement->bindValue(2, $alias, PDO::PARAM_STR);
    $statement->bindValue(3, $new_rank, PDO::PARAM_INT);
    $statement->execute();
}
