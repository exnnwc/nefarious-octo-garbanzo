<?php

<?php
$connection=new PDO ("mysql:host=localhost;dbname=mastery", "root", "");

function display_page($id){
    $statement=$connection->query("select * from functions where id=$id");
    $function=$statement->fetchObject();
    echo "<div>$function->function</div>
          <iframe src='$function->url'></iframe>
         ";
   
   
}







function populate_database(){
    global $connection;
    $arr=get_defined_functions();
    sort($arr['internal']);
    foreach ($arr['internal'] as $function){
        $link=str_replace("_", "-", $function);    
        $query="insert into functions (function, url) values ('$function', 'http://php.net/manual/en/function.$link.php')";
//        echo "$query<BR>";
        $connection->exec($query);        
        
    }
}
