<?php

    include '../model/db_connect.php';
    require "user_db.php";
    
    
    $user_id = $_REQUEST['user_id'];
    
    $result = getUserLikedRecipes($user_id);
    
    if(empty($result))
    {
        $response = "This user has no liked recipe yet.";
    }else
    {
        $response = "";
        foreach($result as $res)
        {
            $response = $response."<p><a href='?action=view_recipe&id=".$res['recipe_id']."'>".$res['recipe_name']."</a></p>";
        }
    }
    
    echo $response;
    