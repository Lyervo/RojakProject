<?php
$current = 'home';
include 'header.php';

include "../model/db_connect.php";
require "../recipe/recipe_db.php";


$id = $_REQUEST['id'];

$recipe = getRecipeByID($id);

$ingredients = getRecipeIngredientByID($id);

$steps = getStepByID($id);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>

    init();

    var user_id;



    function init()
    {
        refreshComments();
        refreshLikes();

        setInterval(refreshComments, 5000);
        setInterval(refreshLikes, 10000);
        checkLike();
    }

    function checkLike()
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                if (this.responseText == 1)
                {
                    document.getElementById("likeButton").innerHTML = "Unlike";
                } else
                {
                    document.getElementById("likeButton").innerHTML = "Like";
                }
            }
        };
        xmlhttp.open("GET", "../like/checkLiked.php?recipe_id=" +<?php echo $id ?> + "&user_id=" + 1, true);
        xmlhttp.send();
    }

    function refreshLikes()
    {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                document.getElementById("likes").innerHTML = this.responseText + " likes";
            }
        };
        xmlhttp.open("GET", "../like/getLikes.php?recipe_id=" +<?php echo $id ?>, true);
        xmlhttp.send();
    }

    function refreshComments()
    {

        console.log("Im upddating the comments");

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                document.getElementById("comments").innerHTML = this.responseText;

            }
        };
        xmlhttp.open("GET", "../review/getReviews.php?recipe_id=" +<?php echo $id ?>, true);
        xmlhttp.send();

    }

    function comment()
    {

        var comment = document.getElementById("commentInput").value;

        if (comment.length === 0)
        {

        } else
        {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function ()
            {
                if (this.readyState == 4 && this.status == 200)
                {
                    refreshComments();
                }
            };

            xmlhttp.open("GET", "../review/submit_comment.php?recipe_id=" +<?php echo $id ?> + "&user_id=" + user_id + "&review=" + comment, true);
            xmlhttp.send();
        }

    }

    function like()
    {

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                if (this.responseText == 1)
                {
                    document.getElementById("likeButton").innerHTML = "Unlike";

                } else
                {
                    document.getElementById("likeButton").innerHTML = "Like";
                }

                refreshLikes();
            }
        };
        xmlhttp.open("GET", "../like/like.php?recipe_id=" +<?php echo $id ?> + "&user_id=" + user_id, true);
        xmlhttp.send();


    }

    var user_id;

    function checkLoginStatus(task)
    {

        if (user_id >= 1)
        {
            if (task === 1)
            {

                like();
            } else if (task === 2)
            {

                comment();
            } else if (task === 3)
            {

                submitReportComment();
            } else if (task === 4)
            {

                submitReportRecipe();
            }
        }

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                if (this.responseText >= 1)
                {
                    if (task === 1)
                    {
                        user_id = this.responseText;
                        like();
                    } else if (task === 2)
                    {
                        user_id = this.responseText;
                        comment();
                    } else if (task === 3)
                    {
                        user_id = this.responseText;
                        submitReportComment();
                    } else if (task === 4)
                    {
                        user_id = this.responseText;
                        submitReportRecipe();
                    }


                } else
                {
                    alert("Sorry, you need to login to perform this action.");
                    $('#login-modal').modal('show');
                }
            }
        };
        xmlhttp.open("GET", "../user/checkLoginStatus.php", true);
        xmlhttp.send();
    }

    function submitReportComment()
    {

        var type = document.getElementById("reportReason").value;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                alert(this.responseText);
            }
        };
        xmlhttp.open("GET", "../ticket/submitTicket.php?action=1&recipe_id=" +<?php echo $id ?> + "&type=" + type + "&review_id=" + report_review_id, true);
        xmlhttp.send();
    }

    function submitReportRecipe()
    {

        var type = document.getElementById("reportReasonRecipe").value;

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                alert(this.responseText);
            }
        };
        xmlhttp.open("GET", "../ticket/submitTicket.php?action=1&recipe_id=" +<?php echo $id ?> + "&type=" + type + "&review_id=0", true);
        xmlhttp.send();
    }

    var report_review_id;

    function initReport(id)
    {

        report_review_id = id;
        getTargetComment();
        document.getElementById("report_tab").style.display = "block";
    }

    function getTargetComment()
    {

        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function ()
        {
            if (this.readyState == 4 && this.status == 200)
            {
                document.getElementById("targetedComment").innerHTML = this.responseText;


            }
        };
        xmlhttp.open("GET", "../review/getReviewByID.php?review_id=" + report_review_id, true);
        xmlhttp.send();


    }


</script>


<!-- Page Content -->
<div class="container">

    <div class="row">

        <!--        side div-->
        <div class="col-lg-4">

            <?php
            if ($recipe['image_blob'] == null) {
                echo "<p>This recipe has no image</p>";
            } else {
                echo '<img id="recipe_picture" src="data:image/jpeg;base64,' . base64_encode($recipe['image_blob']) . '" height="280px" width="400px"/>';
            }
            ?>
            <p id="likes"></p>
            <button id="likeButton" onclick="checkLoginStatus(1)"></button>
            <br>
            <a href="#comments">write a review</a>
            <h5>Ingredents:</h5>
            <?php
            foreach ($ingredients as $ing) {
                $ingredient_name = getIngredientNameByID($ing['ingredient_id']);

                echo "<p>" . $ingredient_name . ", " . $ing['amount'];

                if ($ing['unit'] != "null") {
                    echo " " . $ing['unit'];
                }

                if ($ing['modifier'] != "null") {
                    echo "," . $ing['modifier'];
                }

                echo "</p>";
            }
            ?>

        </div>
        <!-- /.col-lg-3 -->

        <!--        body div-->
        <div class="col-lg-8">

            <h2>Recipe Name: <?php echo $recipe['recipe_name']; ?></h2>
            By (Username)
            <p>Recommended Amount of Servings: <?php echo $recipe['serving'] ?></p>
            <p>Recipe Description :<?php echo $recipe['description'] ?></p>



            <br>
            <div id="sharing_plugins">
                <div class="fb-share-button" data-href="http://127.0.0.1/RojakProject/view/view_recipe.php?id=<?php echo $id; ?>" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>

            </div>
            <br>
            Report this recipe: 
            <select id="reportReasonRecipe" onchange='checkLoginStatus(4)'>
                <option value='' selected disabled hidden>Select Reason</option>
                <option value="missing allergen">Missing allergen</option>
                <option value="incorrect recipe">Incorrect recipe</option>
                <option value="duplicate recipe">Duplicate recipe</option>
                <option value="malicious links">Malicious links</option>
                <option value="other">Other</option>
            </select>

            <h5>Method</h5>
            <?php
            $num = 1;

            foreach ($steps as $step) {
                echo "<p>" . $num . ". " . $step['description'] . "</p>";
                if ($step['step_image'] == null) {
                    
                } else {
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($step['step_image']) . '" height="40px"/><br>';
                }
                $num += 1;
            }
            ?>

            <a href="#"><i class="fab fa-youtube"></i>Click here for a video Tutorial</a>

            <br><br>




            <div style="display:none;border: 1px solid black;padding:5px" id="report_tab">
                <div id="targetedComment"></div>
                Report this comment: 
                <select id="reportReason" onchange='checkLoginStatus(3)'>
                    <option value='' selected disabled hidden>Select Reason</option>
                    <option value="profanity">Profanity</option>
                    <option value="advertisement">Advertisement</option>
                    <option value="malicious link">Malicious Link</option>
                    <option value="other">Other</option>

                </select>
                <br>
            </div>

            <div class="comment_section">
                <div class="comment_contain">
                    <div id="comments">Reviews</div>
                </div>

                <textarea placeholder="write a comment..." id="commentInput"></textarea>
                <br>
                <button onclick="checkLoginStatus(2)" >Comment</button>
            </div> 
            
            <br>
            
            <h2>Simular Recipes</h2>
            ...
            <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>




        </div>
        <!-- /.col-lg-9 -->

    </div>
    <!-- /.row -->

</div>
<!-- /.container -->

<?php
include 'footer.php';
?>

</body>

</html>
