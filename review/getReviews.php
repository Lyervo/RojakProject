<?php

    include "../model/db_connect.php";
    
    require 'review_db.php';
    require '../user/user_db.php';
    

    $recipe_id = $_REQUEST['recipe_id'];

    $result = getReviews($recipe_id);
    
    $response = "";
    if(empty($result))
    {
        echo 'Be the first one to comment!';
    }else
    {
        foreach($result as $res)
        {
            $user = getUserByID($res['user_id']);
            $response = $response."<div id='one_comment'><a href='?action=user_profile&user_id=".$user['user_id']."'>".$user['username']."</a><p>".$res['comment']."</p><i>".$res['review_date']."</i></div><br>";
        }
        echo $response;
    }
    
