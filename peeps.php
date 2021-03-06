<?php
/*TODO
11/24/15
-Make this code more object oriented.

*/
$dbh = new PDO ("mysql:host=localhost;dbname=peeps", "root", "");
$function = $_POST['function_called'];
switch ($function){
    case "new_person":
        create_new_person();
        break;
    case "list_people":
        list_people();
        break;
    case "search":
        search($_POST['search_string']);
        break;
}


function create_new_person(){
    global $dbh;
    $dbh->exec("insert into people () values ()");
}


function does_name_exist($name){
    //Need to extend this to include a search of first and last names then pop up with an error indicating previous people.
    global $dbh;
    $sth = $dbh ->prepare("select count(*) from people where primary_full_name=?");
    $sth->bindValue(1, $name, PDO::PARAM_STR);
    $sth->execute();
    if ($sth->fetchColumn()>0){
        return 1;   
    }   else {
        return 0;
    }
}

function list_people(){
    global $dbh;
    $sth=$dbh->prepare("select * from people where active=1");
    $sth->execute();
    while ($person=$sth->fetchObject()){
        $name_of_person=$dbh->query("select name from aliases where active=1 and rank=1 and owner=".$person->id)->fetchColumn();
        if (!$name_of_person){
            $name_of_person="No name has been assigned to this person yet.";
        } 
        echo "<div class='profile_link_div' id='profile_link_div$person->id'
                style='cursor:pointer;'
                onclick=\"window.location.assign('http://".$_SERVER['SERVER_NAME']."/peeps/?id=$person->id')\">#$person->id<br />
        $name_of_person</div>";
        
    }
}

function search($search_str){
    global $dbh;
    $search_str="%$search_str%";
    $statement=$dbh->prepare("select * from aliases where active=1 and name like ?");
    $statement->bindValue(1, $search_str, PDO::PARAM_STR);
    $statement->execute();
    while ($aliases=$statement->fetchObject()){
        echo "<div class='profile_link_div' id='profile_link_div$aliases->owner'
                style='cursor:pointer;'
                onclick=\"window.location.assign('http://".$_SERVER['SERVER_NAME']."/peeps/?id=$aliases->owner')\">#$aliases->owner<br />
        $aliases->name</div>";
        
    }
}
