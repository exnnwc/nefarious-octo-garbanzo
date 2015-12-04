<?php
//   echo "ASDFAS";
$connection = new PDO ("mysql:host=localhost;dbname=peeps", "root", "");
$function = $_POST['function_called'];
switch ($function){
    case "load_profile":
        load_profile($_POST['id'], ($_POST['hide_empty_headers']==="true"));
        break;
}

function load_profile($id, $hide_empty_headers){
    echo "<div id='profile_id_header'>
              Profile ID #
              <span id='profile_id'>$id</span> 
              <div>
                  <a href='HTTP://".$_SERVER['SERVER_NAME']."/peeps/'>Back</a>

              </div>
          </div>";
    if (display_names($id, fetch_aliases($id))){
        display_traits($id, $hide_empty_headers);
        display_note_section($id, $hide_empty_headers);
    }
}



function display_names($profile_id, $names){
    $new_alias_form="<span>
                     <input id='show_new_alias' type='button' value='+' 
                       onclick=\"$('#show_new_alias').hide();$('#new_alias_form').show(); \" />
                    <span id='new_alias_form' style='display:none' >
                        <input type='button' value='-' onclick=\"$('#show_new_alias').show();$('#new_alias_form').hide();\" />
                        <input id='new_alias' type='text' onkeypress=\"if (event.keyCode==13){CreateNewAlias(".$profile_id . ", $('#new_alias').val());}\"/> 
                        <input type='button' value='Create new alias' 
                          onclick=\"CreateNewAlias(".$profile_id . ", $('#new_alias').val()); $('#new_alias').val('')\" />
                    </span></span>";

    if (count ($names)==0){
        echo "<div>There are no names registered for this person.$new_alias_form</div>";
        $return_value=0;
    } else {
            echo "<div class='profile_header' style='clear:right;'>Name$new_alias_form</div>";
        for ($element=0; $element<count($names); $element++){
            echo "<div style='clear:left;'><div class='left ' style='width:500px;'> ";
            if ($element!=0){
                echo "AKA ";
            }  
                echo $names[$element]['name'] . "</div> 
                  <span style='margin-left:10px; margin-right:10px;' title='QE'>?</span>
                   <input type='button' value='X' onclick=\"DeleteAlias(".$names[$element]['id'].");\"/>";
                if ($element!=0){
                    echo "<input type='button' value='&#8593;' onclick=\"ChangeAliasRank(".$names[$element]['id'].", 1) \"/>";
                }
                if ($element!=count($names)-1){
                    echo "<input type='button' value='&#8595;' onclick=\"ChangeAliasRank(".$names[$element]['id'].", 0) \"/>";
                }
                echo "</div>";            
            
        }
        $return_value=1;
    }
    echo "";
    return $return_value;
}   

function display_note_section($profile_id, $hide_empty_headers){
    global $connection;
    $num_of_notes=$connection
        ->query("select count(*) from notes where owner_table='people' and owner_id=$profile_id")->fetchColumn();
    if (!$hide_empty_headers || ($hide_empty_headers && $num_of_notes>0)){
        echo "<div class='left profile_header' >
              <span style='margin-right:10px'>Notes</span>
              <input id='show_people_note_form".$profile_id."' class='right' type='button' value='+'  
                onclick=\"$('#show_people_note_form".$profile_id."').hide(); $('#people_note_form".$profile_id."').show(); \" />
          </div>".
        create_note_form("people", $profile_id);
        display_notes("people", $profile_id, 0);
    }
}
function display_notes($table, $id, $hidden){
    global $connection;
    $statement=$connection->query("select * from notes where active=1 and owner_table='$table' and owner_id=$id");
    while ($note=$statement->fetchObject()){
        if ($hidden){
            echo "<input id='show_note".$note->id."' type='button' value='+' 
                    onclick=\"$('#show_note".$note->id."').hide(); $('#note".$note->id."').show(); \" />";
        }
        echo "<div id='note".$note->id."' class='note_div' style='clear:both;";
        if ($hidden){
            echo "display:none;";
        }
        echo "'>";
        if ($hidden){
            echo "<input type='button' value='-' 
                    onclick=\"$('#show_note".$note->id."').show(); $('#note".$note->id."').hide(); \" />";
        } 
        echo "<span style='font-size:12px;'>#$note->id"
                    .date("m/d/y h:i", strtotime($note->created))
                ."</span><span>"
        .create_note_menu($note->id)
                ."</span><div style='margin:8px;'>
                    $note->note
                  </div>
              </div>";
    }
}
function display_traits($profile_id, $hide_empty_headers){
    global $connection;


    $statement=$connection->query("select id, type from traits where active=1 and owner is null and value is null order by rank");
    while ($trait_heading=$statement->fetchObject()){
        $query="select count(*) from traits where active=1 and type='".$trait_heading->type."' and owner="
          .$profile_id." and value is not null";
        $statement2= $connection->query($query);
        $num_of_traits_for_this_person= $statement2->fetchColumn();
        if (!$hide_empty_headers || ($num_of_traits_for_this_person>0 && $hide_empty_headers) ){
            echo "<div class='profile_header'>$trait_heading->type ".create_trait_form($profile_id, $trait_heading) ."</div>";
        }
        if ($num_of_traits_for_this_person>0){
            
            $statement2=$connection->query("select * from traits where active=1 and type='".$trait_heading->type."' and owner="
          .$profile_id." and value is not null order by created desc");
            $old_trait=false;
            while ($trait=$statement2->fetchObject()){
                echo "<div";
                if ($old_trait){
                    echo " class='old'";
                } else {
                    $old_trait=true;
                }
                echo ">
                          $trait->value 
                          <input id='show_traits_note_form".$trait->id."' type='button' value='?'  
                            onclick=\"$('#show_traits_note_form".$trait->id."').hide(); 
                              $('#traits_note_form".$trait->id."').show(); \" />
                      </div>".                     
                create_note_form("traits", $trait->id) ;
                $statement3=$connection
                  ->query("select count(*) from notes where active=1 and owner_table='traits' and owner_id=$trait->id");
                    
                if ($statement3->fetchColumn()>0){  
                    display_notes("traits", $trait->id, 1);
                }               
            }
        }
    }
}

function fetch_aliases($profile_id){
    global $connection;
    $statement=$connection->prepare ("select id, name from aliases where owner=? and active=1 order by rank asc");
    $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchAll();
}
function fetch_number_of_traits($profile_id){
    global $connection;
    $statement=$connection->prepare("select count(*) from traits where owner=?");
    $statement->bindValue(1, $profile_id, PDO::PARAM_INT);
    $statement->execute();
    return $statement->fetchColumn();
}


function create_trait_form($profile_id, $trait){
    return "<span>
              <input id='show_new_trait".$trait->id."' type='button' value='+' 
                onclick=\"$('#show_new_trait".$trait->id."').hide();$('#new_trait_form".$trait->id."').show(); \" />
              <span id='new_trait_form".$trait->id."' style='display:none' >
                <input type='button' value='-' 
                  onclick=\"$('#show_new_trait".$trait->id."').show();$('#new_trait_form".$trait->id."').hide();\" />
                <input id='new_trait".$trait->id."' type='text' 
                  onkeypress=\"if (event.keyCode==13){
                    CreateNewTrait(".$profile_id . ", ".$trait->id .", '".$trait->type ."', $(this).val());
                  }\"/> 
                <input type='button' value='Create new trait' 
                  onclick=\"CreateNewTrait(".$profile_id . ", ".$trait->id .", '".$trait->type ."', $('#new_trait".$trait->id."').val());\" />
              </span>
            </span>";
}

function create_note_form($table, $id){

    return "
          <div id='".$table."_note_form".$id."' style=':float:left;display:none;'>
              <div style='clear:both;'>
                  <input type='button' value='-' style='float:left;' 
                    onclick=\"$('#show_".$table."_note_form".$id."').show(); $('#".$table."_note_form".$id."').hide(); \" />
                  <input type='button' value='Create Note'
                    onclick=\"CreateNote('".$table."', ".$id.", $('#".$table."_note".$id."').val())\" />
              </div><div style='clear:both;'>
                  <textarea class='note_input' id='".$table."_note".$id."'></textarea>
              </div>
          </div>";
}

function create_note_menu($id){
    return "<input type='button' value='X' onclick=\"DeleteNote($id); \" />";
}
