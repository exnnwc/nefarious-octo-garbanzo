<html>
<head>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script>

function CreateTrait(trait){
//    document.write($('#discrete_true').is(':checked') +
    error=false;
    if($('#discrete_true').is(':checked')){
        discrete=false;
    } else if ($('#discrete_false').is(':checked')){
        discrete=true;
    } else {
        error=true;
        $('#error').html("Please select if this is a discrete trait.");
    }
if (!error){
    $.ajax({
        method:"POST",
        url:"traits.php",
        data:{function_to_be_called:"create", new_trait:trait, discrete:discrete}
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
<label for='discrete_true'>Discrete</label>
<input id='discrete_true' name='discrete_trait' type='radio' /> 
<label for='discrete_false'>Non-Discrete</label>
<input id='discrete_false' name='discrete_trait' type='radio' /><br />
<input id='new_trait' type='text' onkeypress="if(event.keyCode==13){CreateTrait(this.value);}" />
<input type='button' value='Create' onclick="CreateTrait($('#new_trait').val())" />
<div id='error'></div>
<div id='list_of_traits'></div>
</body></html>
<?php

?>
