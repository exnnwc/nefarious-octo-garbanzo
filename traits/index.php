<html>
<head>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>

function CreateTrait(trait){
    $.ajax({
        method:"POST",
        url:"traits.php",
        data:{function_to_be_called:"create", new_trait:trait}
    })
        .done (function(result){
            if(result.substr(0,1)=="0"){
                $('#error').html(result);
            } else {
                ListTraits();
                $("#new_trait").val('');
            }
        });
}
function ListTraits(){
    $.ajax({
        method:"POST",
        url:"traits.php",
        data:{function_to_be_called:"list"}
    })
        .done (function(result){
            $('#list_of_traits').html(result);
            //document.write(result);
        });
    //document.write("ADFAS");
}

function Delete(trait){

    $.ajax({
    method:"POST",
    url:"traits.php",
    data:{function_to_be_called:"delete", type:trait}
    })
        .done (function(result){
            ListTraits();
        });

}

function ChangeRank(id, is_direction_up){
    $.ajax({
        method:"POST",
        url:"traits.php",
        data:{function_to_be_called:"change_rank", id:id, is_direction_up:is_direction_up}
    })
        .done(function(result){
            ListTraits();
        });
}

</script>
</head>
<body onload="ListTraits()">
<input id='new_trait' type='text' onkeypress="if(event.keyCode==13){CreateTrait(this.value);}" />
<input type='button' value='Create' onclick="CreateTrait($('#new_trait').val())" />
<div id='error'></div>
<div id='list_of_traits'></div>
</body></html>
<?php

?>
