<html>
<head>
<style>

    .new_person{
        font-size:20px;
        height:75px;
        width:300px;
    }
    .old{
        color:grey;
        text-decoration:line-through;
    }
    .right{
        float:right;
    }
    .left{
        float:left;
    }
    .break{
        clear:both;
    }
    .profile_header{
        font-size:24px;
                clear:left;
        margin-top:20px;
        margin-bottom:10px;

    }
    #profile_id_header{
        font-size:10px;

     }
    .profile_link_div{
        width:600px;
        padding:16px;
        text-align:center;
        border:solid 1px black;
    }
    .note_input{
        width:400px;
        height:100px;
    }
    .note_div{
        width:800px;
        min-height:80px;        
        background-color:lightgray;
        color:black;
        border:4px solid gray;
    }
</style>

<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>


<script>
function ChangeAliasRank (alias_id, is_direction_up){
        
        $.ajax({
        method:"POST",
        url:"aliases.php",
        data: {function_called:"change_rank", id:alias_id, is_direction_up:is_direction_up}
    }).
        done(function (result){

        ReloadProfile();
        });
    
}

function CreateNewPerson(){
    $.ajax({
        method:"POST",
        url:"peeps.php",
        data: {function_called:"new_person"}
    }).
        done(function (result){
                $('#test').html(result);
        });
}

function CreateNewAlias(profile_id, alias){
    $.ajax({
        method:"POST",
        url:"aliases.php",
        data: {function_called:"new_alias", profile_id:profile_id, new_alias:alias}
    }).
        done(function (result){
            ReloadProfile();
        });
}

function CreateNote(table, id, note){
//    document.write (table + " " + id + " " + note);
    $.ajax({
        method:"POST",
        url:"notes.php",
        data:{function_to_be_called:"create", table:table, id:id, note:note}
    })
        .done(function(result){
             if (result.substr(0,1) =="0"){   
                $('#error').html(result);
            } else {
                ReloadProfile();
                
            }           
        });
}
function CreateNewTrait(profile_id, trait_id, trait_type, trait_value){
    $.ajax({
        method:"POST",
        url:"traits/traits.php",
        data:{function_to_be_called:"create_trait", profile_id:profile_id, 
          trait_id:trait_id, trait_type:trait_type, trait_value:trait_value}
    })
        .done(function (result){
            if (result.substr(0,1) =="0"){   
                $('#error').html(result);
            } else {
                ReloadProfile();
            }
        });
}

function CreateVehicle(id, make, model, year, color, license, state){
//    document.write (id + " " + make + " " + model + " " + year + " " + color + " " + license + " " + state);
    $.ajax({
        method:"POST",
        url:"vehicles.php",
        data:{function_to_be_called:"create", profile_id:id, make:make, model:model, year:year, color:color, license:license, state:state}
    })
        .done(function (result){
            if (result.substr(0,1)=="0"){
                $('#error').html(result);
            } else {
                ReloadProfile();
            }
        });

} 
function DeleteAlias(alias_id){
            $.ajax({
            method:"POST",
            url:"aliases.php",
            data: {function_called:"delete_alias", id:alias_id}
        }).
            done(function (result){
                ReloadProfile();
            });

}

function DeleteNote(id){
    if (confirm("Are you sure you want to delete this entry?")){
        $.ajax({
            method:"POST",
            url:"notes.php",
            data:{function_to_be_called:"delete", id:id}
        })
            .done(function(result){
                if (result.substr(0,1) =="0"){   
                    $('#error').html(result);
                } else {
                    ReloadProfile();
                }
            });
    }
}
function DeleteTrait(id){
        if (confirm("Are you sure you want to delete this entry?")){
        $.ajax({
            method:"POST",
            url:"traits/traits.php",
            data:{function_to_be_called:"delete_trait", id:id}
        })
            .done(function(result){
                if (result.substr(0,1) =="0"){   
                    $('#error').html(result);
                } else {
                    ReloadProfile();
                }
            });
    }
}
function DeleteVehicle(id){

    $.ajax({
        method:"POST",
        url:"vehicles.php",
        data:{function_to_be_called:"delete", id:id}
    })
        .done(function (result){
                if (result.substr(0,1) =="0"){   
                    $('#error').html(result);
                } else {
                    ReloadProfile();
                }
        });
}
function ListPeople(){
    $.ajax({
        method:"POST",
        url:"peeps.php",
        data: {function_called:"list_people"}
    }).
        done(function (result){
                $('#list_of_people').html(result);
        });
}

function LoadProfile (profile_id){
        hide_empty_headers=$('#hide_empty_headers').prop("checked");
        $.ajax({
        method:"POST",
        url:"profile.php",
        data: {function_called:"load_profile", id:profile_id, hide_empty_headers:hide_empty_headers}
    }).
        done(function (result){
                $('#profile_content').html(result);

        });


}

function ReloadProfile (){
    LoadProfile($('#profile_id').html());
}

function SearchAliases(str){
    $.ajax({
        method:"POST",
        url:"peeps.php",
        data:{function_called:"search", search_string:str}
    })
        .done (function (result) {
            $("#list_of_people").html(result);
        });
}
</script>
</head>

<?php

if (!isset($_GET['id'])){
    $_GET['id']=0;
}


if($_GET['id']==0):?>

<body onload="ListPeople()">
<input type='text' onkeyup="SearchAliases(this.value)"/>
<div id='list_of_people' style='float:left'></div>
<div style="float:right;text-align:center;" >
    <div>
        <a href="traits/" style="clear:both"> Traits</a>
    </div><div>
        <input id='new_person_button' class='new_person right' style='clear:both' type='button' value='New Person'
          onclick="CreateNewPerson();"/>
    </div>
</div>
<?php elseif($_GET['id']>0):?>
<body onload="LoadProfile(<?php echo $_GET['id']; ?>)">
<div id='error'></div>
<div id='profile_menu'>Hide empty traits?<input id='hide_empty_headers' type='checkbox' onclick="ReloadProfile()"/></div>
<div id="profile_content"> </div>
<?php endif;?>

<div id="test">

</div>


</body></html>
