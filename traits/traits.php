<?php

$connection = new PDO ("mysql:host=localhost;dbname=peeps", "root", "");
switch ($_POST["function_to_be_called"]){
    case "create":
        create_type(trim($_POST['new_trait']));
        break;
    case "list":
        display_all();
        break;
    case "delete":
        delete ($_POST['type']);
        break;
    case "delete trait":
        delete_trait($_POST['id']);
        break;
    case "change_rank":
        change_rank($_POST['id'], $_POST['is_direction_up']);
        break;
    case "create_trait":
        create_trait($_POST['profile_id'], $_POST['trait_id'], $_POST['trait_type'], $_POST['trait_value']);
        break;
}

function change_rank($id, $is_direction_up){
    global $connection;
    $statement=$connection->prepare("select * from traits where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    $main_record=$statement->fetchObject();
    if ($is_direction_up){
        $new_rank=$main_record->rank-1;
    } else {
        $new_rank=$main_record->rank+1;
    }
    //$other_record=$connection->query("select * from traits where active=1 and rank=".$new_rank)->fetchObject();
    $connection->exec("update traits set rank=".$main_record->rank." where active=1 and rank=".$new_rank);
    $connection->exec("update traits set rank=".$new_rank." where id=".$main_record->id);
}
function create_trait($profile_id, $trait_id, $trait_type, $trait_value){
    global $connection;
    $statement=$connection->prepare("select count(*) from traits where type=? and value=?");
    $statement->bindValue(1, $trait_type, PDO::PARAM_STR);
    $statement->bindValue(2, $trait_value, PDO::PARAM_STR);
    $statement->execute();
    if (!$statement->fetchColumn()){
        $rank=fetch_rank($trait_id);
        $statement=$connection->prepare("insert into traits (owner, type, value, rank) values (?, ?, ?, ?)");
        $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
        $statement->bindValue(2, $trait_type, PDO::PARAM_STR);
        $statement->bindValue(3, $trait_value, PDO::PARAM_STR);
        $statement->bindValue(4, $rank, PDO::PARAM_INT);
        $statement->execute();
    } else {
        echo "0 There is already a trait with that value registered for that person.";
    }
}

function create_type($trait){
    global $connection;
    $statement=$connection->prepare ("select count(*) from traits where type=?");
    $statement->bindValue(1, $trait, PDO::PARAM_STR);
    $statement->execute();
    $trait_name_exists=$statement->fetchColumn();
    if (!$trait_name_exists){
        $statement=$connection->prepare("insert into traits(type, rank) values (?, ".fetch_next_rank().")");
        $statement->bindValue(1, $trait, PDO::PARAM_STR);
        $statement->execute();
    } else {
        //Possibly extend this to similar traits using levenschtein() and there may be an issue with case sensitivity.
        echo "0 There is already a trait by that name. Please use that instead.";
    }
}

function delete($type){
    global $connection;
    $statement=$connection->prepare("update traits set active=0 where type=?");
    $statement->bindValue(1, $type, PDO::PARAM_STR);
    $statement->execute();
    
}
function delete_trait($id){
    global $connection;
    $statement=$connection->prepare("update traits set active=0 where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_STR);
    $statement->execute();
}
function display_all(){
    $traits=fetch_all();
    while ($trait=$traits->fetchObjecT()){
        echo "<div><div style='width:350px;float:left;'>ID #$trait->id - $trait->type </div>";
        display_menu($trait);
        echo "</div>";        
    }    
}

function display_menu($trait){
    echo "<input type='button' value='X' onclick=\"Delete('$trait->type');\" />";
    if ($trait->rank!=1){
          echo "<input type='button' value='&#8593;' onclick=\"ChangeRank($trait->id, 1)\"/>";
    }
    if ($trait->rank!=fetch_next_rank()-1){
          echo "<input type='button' value='&#8595;'    onclick=\"ChangeRank($trait->id, 0)\" />";
    }


}

function fetch_all(){
    global $connection;
    $query="select * from traits where active=1 and owner is null and value is null order by rank";
    return $connection->query($query);
}

function fetch_next_rank(){
    global $connection;
    return $connection->query("select rank from traits order by rank desc limit 1")->fetchColumn()+1;
   
}

function fetch_rank($id){
    global $connection;
    $statement= $connection->prepare("select rank from traits where id=?");
    $statement->bindValue(1, $id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}
?>
