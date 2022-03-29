<?php
    if( isset($_POST['name']) and !empty($_POST['name'])){
        	$name = $_POST['name'];
            $score = $_POST['score'];
            include("connectToDB.inc.php");  // connection file
            $sql_insert = "INSERT INTO plat_leaderboard (name,score) VALUES ('$name','$score')";
            if($submit == false){
            $result_insert = $db->query($sql_insert);
            echo "Thank you, your score has been added";
            $submit = true;
        	} else{
        		echo 'You have already submitted your score';
        	}
        exit();
    }
    else{
    	if(empty($_POST['name'])){
        echo 'Please enter your name!';
    	}
       
        exit();
    }
?>