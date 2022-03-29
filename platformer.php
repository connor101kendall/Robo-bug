<html>
<head>
    <meta charset="utf-8">
    
    <title>Platformer Example</title>   
    <link rel="icon" href="images/playerR.png" type="image/png" />
    <meta name="description" content="" />
    <meta name="robots" content="All" />
    <meta name="keywords" content="JQuery game development" />
    <!-- load jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>   
    <script type="text/javascript">
        /* Usage */
        window.onload = function (){
            AJAXform( 'subForm', 'my-button', 'my-result', 'post' ); // AJAXform( 'your-form-id', 'button-id', 'result-element-id', 'post or get method' );
        };
    </script>
    
    <style>
    body{
        margin: 0; 
        height: 100%; 
        color: #fff;
        overflow: hidden;
        font-size: 1.1em;
        font-family: 'Lucida Sans Typewriter';
        background-image: url("images/platformer-background.gif");
        background-size: cover;

    }
    a.back-button:link, a.back-button:visited , a.back-button:active {
            padding: 2px;
            font-size: 1.5em;
            color: #fff; 
            border-radius: 0 0.2em 0 0.2em; /*Top/Right/Botton/Left */
            -moz-border-radius: 0.3em 0.3em 0.3em 0.3em; 
            -webkit-border-radius: 0.3em 0.3em 0.3em 0.3em;             
        }
        a.back-button:hover {
            background: #ffff00;
            color: #000000;
            padding: 2px 8px;
            box-shadow: 5px 5px 5px #222222;
            -webkit-box-shadow: 5px 5px 5px #222222;
            -moz-box-shadow: 5px 5px 5px #222222;
            transition: all 0.25s ease;
        }
    </style>
    <script type="text/javascript">
    
    </script>
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" integrity="sha512-+4zCK9k+qNFUR5X+cKL9EIR+ZOhtIloNl9GIKS57V1MyNsYpYcUrUeQc9vNfzsWfV28IaLL3i96P9sdNyeRssA==" crossorigin="anonymous" />
    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>

<a class="back-button" href="https://icsprogramming.ca/2020-2021/kendall1a334/"><i class="fas fa-home"></i></a>
<div align="center" style="width: 80%; margin: 0 auto" >
<canvas style="position: absolute; top: 100; left: 350;" id="StartScreen"></canvas>
<canvas style="position: absolute; top: 100; left: 350;" id="canvas"></canvas>
</div>
<div align="center" style="width: 20%; float: left" >
    <?
    echo "</br></br></br></br></br></br><h3>Leaderboard</h3>
    <ol>";
        include("connectToDB.inc.php");  // connection file
        $submit = false;
        //output leaderboard
        $plat_leaderboard = "SELECT * FROM plat_leaderboard ORDER BY score DESC LIMIT 10";
        $result = $db->query($plat_leaderboard);
        if ($result->num_rows > 0) {
            while($myrow = $result->fetch_assoc()) {
                  // data from the table is stored in an associative array 
                  echo "
            <li>" . $myrow['name'] . " <b>Score:</b> " . $myrow['score'] . "</li>";
                   }
        } else {
            echo "<p>Sorry, no records found.</p>";
        }

        echo "
        <form action='platformer-data.php' name='my-form' method='POST' style='visibility: hidden;' id='subForm'>
                Name:<input type='text' placeholder='EX. Wizard' name='name' autocomplete='off' value=''></input></br>
                <input type='hidden' placeholder='EX. 100' name='score' autocomplete='off' id='score' value=''></input></br>
               <input type='submit' name='subBtnUpdate' class='subButton' id='my-button' value='Submit'>
        </form>
        <div id='my-result'></div>
        ";
    
    ?>
    </ol>
</div>

<img id="lava" width="150" height="30" src="images/lava.gif" alt="lava" style="display: none">
<img id="player" width="20" height="20" src="images/player.png" alt="player" style="display: none">
<img id="playerR" width="20" height="20" src="images/playerR.png" alt="playerR" style="display: none">
<img id="baddie" width="40" height="40" src="images/baddie.png" alt="baddie" style="display: none">
<img id="baddieR" width="40" height="40" src="images/baddieR.png" alt="baddieR" style="display: none">
<img id="goal" width="15" height="100" src="images/goal.png" alt="Goal" style="display: none">
<img id="point" width="40" height="40" src="images/points.png" alt="Point" style="display: none">
<img id="point2" width="40" height="40" src="images/points2.png" alt="Point" style="display: none">
<img id="spikes" width="400" height="400" src="images/spikes.png" alt="Spikes" style="display: none">
<img id="floorLvl2" width="1000" height="50" src="images/floorLvl2.png" alt="floor" style="display: none">
<img id="rock" src="images/rock.png" alt="rock" style="display: none">
<script>
    
    function AJAXform( formID, buttonID, resultID, formMethod = 'post' ){
        /* Usage */
    
        var selectForm = document.getElementById(formID); // Select the form by ID.
        var selectButton = document.getElementById(buttonID); // Select the button by ID.
        var selectResult = document.getElementById(resultID); // Select result element by ID.
        /*console.log(selectResult);
        var responseText = "test";
        selectResult.innerHTML = responseText;*/
        var formAction = document.getElementById(formID).getAttribute('action'); // Get the form action.
        var formInputs = document.getElementById(formID).querySelectorAll("input"); // Get the form inputs.
     
        function XMLhttp(){
            var httpRequest = new XMLHttpRequest();
            var formData = new FormData();
            for( var i=0; i < formInputs.length; i++ ){
                formData.append(formInputs[i].name, formInputs[i].value); // Add all inputs inside formData().
            }
     
            httpRequest.onreadystatechange = function(){
                if ( this.readyState == 4 && this.status == 200 ) {
                    selectResult.innerHTML = this.responseText; // Display the result inside result element.
                }
            };
     
            httpRequest.open(formMethod, formAction);
            httpRequest.send(formData);
        }
     
        selectButton.onclick = function(){ // If clicked on the button.
            XMLhttp();
        }
     
        selectForm.onsubmit = function(){ // Prevent page refresh
            return false;
        }
    }

    // The attributes of the player.
    var player = {
        x: 50,
        y: 450,
        x_v: 0,
        y_v: 0,
        jump : true,
        death : false,
        win : false,
        level : 1,
        points : 0,
        right : true,
        height: 20,
        width: 20
        };
    // The attributes of the enemy.
    var baddie = {
        x: 470,
        y: 120,
        x_v: 0,
        y_v: 0,
        death : false,
        right : true,
        height: 40,
        width: 40
        };
    // The status of the arrow keys
    var keys = {
        right: false,
        left: false,
        up: false,
        };
    // The friction and gravity to show realistic movements    
    var gravity = 0.5;
    var friction = 0.7;
    var t = 5000;
    var dirP = "playerR"
    var dirB = "baddieR"
    var gameStart = false;
    // The number of platforms
    var numP = 2;
    // The platforms
    var platforms = [];
    // The floor
    var floor1 = [];
    var floor2 = [];
    // The floor 2.0
    var floorLvl2 = [];
    // The lava
    var lava = [];
    // The number of spikes
    var numS = 2;
    // The Spikes
    var spikes = [];
    // The jump boost
    var boost = [];
    // The number of slippy platforms
    var numI = 2;
    // The slippery platforms
    var ice = [];
    // The high platform
    var roof = [];
    // The high platform part 2
    var roof2 = [];
    // The goal
    var goal = [];
    // The Points
    var point = [];
    // Function to render the canvas
    function renderStartScreen(){
        
        ctx.fillStyle = "#000000";
        ctx.fillRect(0, 0, 1000, 500);
        ctx.font = "30px Lucida Sans Typewriter";
        ctx.fillStyle = "white";
        ctx.textAlign = "center";
        ctx.fillText("Welcome To Robo-Bug", canvas.width/2, 150);
        ctx.font = "15px Lucida Sans Typewriter";
        ctx.fillText("This is a platformer game, made by Connor Kendall using a JQuery canvas element", canvas.width/2, 200);
        ctx.fillText("use the left and right arrow keys to move back and forth, and the spacebar to jump. ", canvas.width/2, 225);
        ctx.fillText("Collect melons to gain points! avoid lava, icicles, and enemy Robo-bugs... Goodluck!", canvas.width/2, 250);
        ctx.font = "30px Lucida Sans Typewriter";
        ctx.fillText("Press Space to Begin", canvas.width/2, 300);
    }
    function rendercanvas1(){
        var grd = ctx.createRadialGradient(75, 50, 50, 90, 60, 1000);
        grd.addColorStop(0, "#cea240");
        grd.addColorStop(1, "#696231");
        ctx.fillStyle = grd;
        ctx.fillRect(0, 0, 1000, 500);
        //ctx.fillStyle = "#F0F8FF";
        //ctx.fillRect(0, 0, 1000, 500);
    }
    function rendercanvas2(){
        var grd = ctx.createRadialGradient(75, 50, 50, 90, 60, 1000);
        grd.addColorStop(0, "white");
        grd.addColorStop(1, "#87CEEB");
        ctx.fillStyle = grd;
        ctx.fillRect(0, 0, 1000, 500);
        
        //ctx.fillStyle = "#F0F8FF";
        //ctx.fillRect(0, 0, 1000, 500);
    }
    
    // Function to render the player
    /*
    function renderplayerL(){
        //ctx.fillStyle = "#ff00ff";
        var img = document.getElementById("player");
        ctx.drawImage(img, (player.x)-20, (player.y)-20, player.width, player.height);
        //ctx.fillRect((player.x)-20, (player.y)-20, player.width, player.height);
        }
        */
    function renderplayerR(){
        //ctx.fillStyle = "#ff00ff";
        var img = document.getElementById(dirP);
        ctx.drawImage(img, (player.x)-20, (player.y)-20, player.width, player.height);
        //ctx.fillRect((player.x)-20, (player.y)-20, player.width, player.height);
        }
    function renderbaddieR(){
        //ctx.fillStyle = "#ff00ff";
        var img = document.getElementById(dirB);
        ctx.drawImage(img, (baddie.x)-40, (baddie.y)-40, baddie.width, baddie.height);
        //ctx.fillRect((player.x)-20, (player.y)-20, player.width, player.height);
        }
    // Function to create platforms
    function createplat(){
        for(i = 0; i < numP; i++) {
            platforms.push(
                {
                x: 275 + (i*150),
                y: 370,
                width: 70,
                height: 15
                }
            );
        }
        }
    // Function to create slippy platforms
    function createIce(){
        for(i = 0; i < numI; i++) {
            ice.push(
                {
                x: 700 + (i*150),
                y: 200 + (50 * i),
                width: 95,
                height: 15
                }
            );
        }
        }
    // Function to create slippy platforms
    function createSpikes(){
        for(i = 0; i < numI; i++) {
            spikes.push(
                {
                x: 700 + (i*150),
                y: 215 + (50 * i),
                width: 95,
                height: 10
                }
            );
        }
        }
    function createFloor1(){
            floor1.push(
                {
                x: 0, 
                y: 450, 
                width: 200,
                height: 50
                }
            );
        }
    function createFloorLvl2(){
            floorLvl2.push(
                {
                x: 0, 
                y: 450, 
                width: 1000,
                height: 50
                }
            );
        }
    function createLava(){
            lava.push(
                {
                x: 180, 
                y: 470, 
                width: 395,
                height: 30
                }
            );
        }
    function createFloor2(){
            floor2.push(
                {
                x: 575, 
                y: 450, 
                width: 425,
                height: 50
                }
            );
        }
    function createBoost(){
            boost.push(
                {
                x: 800, 
                y: 449, 
                width: 75,
                height: 15
                }
            );
        }
    function createRoof(){
            roof.push(
                {
                x: 0, 
                y: 120, 
                width: 350,
                height: 15
                }
            );
        }
    function createRoof2(){
            roof2.push(
                {
                x: 440, 
                y: 120, 
                width: 210,
                height: 15
                }
            );
        }
    function createGoal(){
            goal.push(
                {
                x: 0, 
                y: 50, 
                width: 75,
                height: 100
                }
            );
        }
    function createPoint(){
            point.push(
                {
                x: 100, 
                y: 350, 
                width: 30,
                height: 30,
                active: true
                }
            );
            point.push(
                {
                x: 630, 
                y: 350, 
                width: 30,
                height: 30,
                active: true
                }
            );
            point.push(
                {
                x: 900, 
                y: 170, 
                width: 30,
                height: 30,
                active: true
                }
            );
            point.push(
                {
                x: 360, 
                y: 280, 
                width: 30,
                height: 30,
                active: true
                }
            );
        }
     // Function to render floor
    function renderFloor(){
        ctx.fillStyle = "#313c28";
        ctx.fillRect(floor1[0].x, floor1[0].y, floor1[0].width-20, floor1[0].height);
        ctx.fillRect(floor2[0].x, floor2[0].y, floor2[0].width, floor2[0].height);
    }
    function renderFloorLvl2(){
        ctx.fillStyle = "#006400";
        ctx.fillRect(floorLvl2[0].x, floorLvl2[0].y, floorLvl2[0].width, floorLvl2[0].height);
       
    }
    // Function to render lava
    function renderLava(){
        ctx.fillStyle = "#ff0000";
        //ctx.fillRect(lava[0].x, lava[0].y, lava[0].width, lava[0].height);
        var img = document.getElementById("lava");
        ctx.drawImage(img, lava[0].x, lava[0].y, lava[0].width, lava[0].height);
    }
    // Function to render boost
    function renderBoost(){
        ctx.fillStyle = "#00ff00";
        ctx.fillRect(boost[0].x, boost[0].y, boost[0].width, boost[0].height);
    }
    // Function to render boost
    function renderGoal(){
        ctx.fillStyle = "#0000ff";
        //ctx.fillRect(goal[0].x, goal[0].y, goal[0].width, goal[0].height);
        var img = document.getElementById("goal");
        ctx.drawImage(img, goal[0].x, 25, 75, 100);
    }
    function renderPoint(){
        var img = document.getElementById("point");
        if (point[0].active == true){
        ctx.drawImage(img, point[0].x, point[0].y, point[0].width, point[0].height);
        }
        if (point[1].active == true){
        ctx.drawImage(img, point[1].x, point[1].y, point[1].width, point[1].height);
        }
        if (point[2].active == true){
        ctx.drawImage(img, point[2].x, point[2].y, point[2].width, point[2].height);
        }
        var img = document.getElementById("point2");
        if (point[3].active == true){
        ctx.drawImage(img, point[3].x, point[3].y, point[3].width, point[3].height);
        }
    }
    function renderRoof(){
        ctx.fillStyle = "#313c28";
        ctx.fillRect(roof[0].x, roof[0].y, roof[0].width-20, roof[0].height);
        ctx.fillRect(roof2[0].x, roof2[0].y, roof2[0].width-20, roof2[0].height);
    }
    function renderMessage(){
        ctx.fillStyle = "#000000";
        ctx.fillRect(0, 0, 1000, 500);
        ctx.font = "30px Lucida Sans Typewriter";
        ctx.fillStyle = "red";
        ctx.textAlign = "center";
        ctx.fillText("You Died, Try Harder", canvas.width/2, 150);
        ctx.fillText("Refresh To Try Again...", canvas.width/2, 200);
        ctx.fillText("Points Collected: "+player.points, canvas.width/2, 250);
        ctx.fillText("Time: "+Math.floor(t/50), canvas.width/2, 300);
    }
    function renderMessage1(){
        ctx.fillStyle = "#000000";
        ctx.fillRect(0, 0, 1000, 500);
        ctx.font = "30px Lucida Sans Typewriter";
        ctx.fillStyle = "green";
        ctx.textAlign = "center";
        ctx.fillText("Congratulations!, You Won!", canvas.width/2, 150);
        ctx.fillText("Refresh To Play Again...", canvas.width/2, 200);
        ctx.fillText("Points Collected: "+player.points, canvas.width/2, 250);
        ctx.fillText("Time Remaining: "+Math.floor(t/50), canvas.width/2, 300);
        var total = (Math.floor(t/50))+player.points;
        ctx.fillText("Total:"+total, canvas.width/2, 400);
        
        document.getElementById("subForm").style.visibility = 'visible';
        document.getElementById("score").value = total;
        //console.log("here");

        //ctx.fillText("Total: "100-+Math.floor(t/50)++player.points, canvas.width/2, 350);
    }
    function renderCount(){
        ctx.font = "20px Lucida Sans Typewriter";
        ctx.fillStyle = "black";
        ctx.textAlign = "center";
        ctx.fillText("Points: "+player.points, canvas.width-75, 30);
    }
    function renderTimer(){
        ctx.font = "20px Lucida Sans Typewriter";
        ctx.fillStyle = "black";
        ctx.textAlign = "center";
        ctx.fillText("Time: "+Math.floor(t/50), canvas.width-75, 60);
    }
    // Function to render platforms
    function renderplat(){
        ctx.fillStyle = "#313c28";
        ctx.fillRect(platforms[0].x, platforms[0].y, platforms[0].width-20, platforms[0].height);
        ctx.fillRect(platforms[1].x, platforms[1].y, platforms[1].width-20,platforms[1]. height);
    }
    function renderIce(){
        ctx.fillStyle = "#6495ed";
        ctx.fillRect(ice[0].x, ice[0].y, ice[0].width-20, ice[0].height);
        ctx.fillRect(ice[1].x, ice[1].y, ice[1].width-20, ice[1]. height);
        /*
        ctx.fillRect(platforms[2].x, platforms[2].y, platforms[2].width,platforms[2]. height);
        */
    }
    function renderSpikes(){
        //ctx.fillStyle = "#0000ff";
        //ctx.fillRect(spikes[0].x, spikes[0].y, spikes[0].width-20, spikes[0].height);
        //ctx.fillRect(spikes[1].x, spikes[1].y, spikes[1].width-20, spikes[1]. height);
        var img = document.getElementById("spikes");
        ctx.drawImage(img, spikes[0].x-18, spikes[0].y-25, spikes[0].width+25, spikes[0].height+50);
        ctx.drawImage(img, spikes[1].x-18, spikes[1].y-25, spikes[1].width+25, spikes[1]. height+50);
    }
    function renderBackground(){
        var img = document.getElementById("rock");
        ctx.drawImage(img, 25, 300, 200, 200);
    }
    // This function will be called when a key on the keyboard is pressed
    function keydown(e) {
        // 37 is the code for the left arrow key
        if(e.keyCode == 37) {
            keys.left = true;
        }
        // 37 is the code for the up arrow key
        if(e.keyCode == 32) {
            if(player.jump == false) {
                player.y_v = -10;
            }
        }
        // 39 is the code for the right arrow key
        if(e.keyCode == 39) {
            keys.right = true;
        }
        if(e.keyCode == 32 && gameStart == false) {
             gameStart = true;
             console.log(gameStart);
             setInterval(loop,20);
        }

    }
    // This function is called when the pressed key is released
    function keyup(e) {
        if(e.keyCode == 37) {
            keys.left = false;
        }
        if(e.keyCode == 38) {
            if(player.y_v < -2) {
            player.y_v = -3;
            }
        }
        if(e.keyCode == 39) {
            keys.right = false;
        }
    } 
    function loop() {
        // Updating the y and x coordinates of the player
        player.y += player.y_v;
        player.x += player.x_v;
        // Move the baddie
        baddie.x += baddie.x_v;
        if(baddie.x == 480) {
            dirB = "baddieR";
        } 
        if (baddie.x == 630){
            dirB = "baddie";
        }
        if (dirB == "baddieR"){
            baddie.x_v = +2;    
        } else {
            baddie.x_v = -2; 
        }
        
        // If the player is not jumping apply the effect of friction
        if(player.jump == false) {
            player.x_v *= friction;
        } else {
            // If the player is in the air then apply the effect of gravity
            player.y_v += gravity;
        }
        player.jump = true;
        // If the left key is pressed increase the relevant horizontal velocity
        if(keys.left) {
            player.right = false;
            dirP = "player";
            if (player.x > 20){
                player.x_v = -2.5;
            } else {
                player.x_v = 0;
            }
        }
        if(keys.right) {
            player.right = true;
            dirP = "playerR";
            if (player.x < 995){
                player.x_v = 2.5;
            } else {
                player.x_v = 0;
            }
        }
        if(player.level == 1){
        // A simple code that checks for collions with the platform
        if(floor1[0].x < player.x && player.x < floor1[0].x + floor1[0].width &&
        floor1[0].y < player.y && player.y < floor1[0].y + floor1[0].height){
            player.jump = false;
            player.y = floor1[0].y;
            gravity = 0.5;
        }
        if(floor2[0].x < player.x && player.x < floor2[0].x + floor2[0].width &&
        floor2[0].y < player.y && player.y < floor2[0].y + floor2[0].height){
            player.jump = false;
            player.y = floor2[0].y;
            gravity = 0.5;
            friction = 0.7;
        }
        
        if(lava[0].x < player.x && player.x < lava[0].x + lava[0].width &&
        lava[0].y < player.y && player.y < lava[0].y + lava[0].height){
            player.death = true;
        }
        if(baddie.x < player.x && player.x < baddie.x + baddie.width &&
        baddie.y < player.y && player.y < baddie.y + baddie.height){
            player.death = true;
        }
        if(goal[0].x < player.x && player.x < goal[0].x + goal[0].width &&
        goal[0].y < player.y && player.y < goal[0].y + goal[0].height){
            player.level = 2;
            player.x = 50;
            player.y = 450;
        }
        if(point[0].x < player.x && player.x < point[0].x + point[0].width &&
        point[0].y < player.y && player.y < point[0].y + point[0].height && point[0].active == true){
            player.points = player.points + 10;
            console.log("Point")
            point[0].active = false;
        }
        if(point[1].x < player.x && player.x < point[1].x + point[1].width &&
        point[1].y < player.y && player.y < point[1].y + point[1].height && point[1].active == true){
            player.points = player.points + 10;
            console.log("Point")
            point[1].active = false;
        }
        if(point[2].x < player.x && player.x < point[2].x + point[2].width &&
        point[2].y < player.y && player.y < point[2].y + point[2].height && point[2].active == true){
            player.points = player.points + 10;
            console.log("Point")
            point[2].active = false;
        }
        if(point[3].x < player.x && player.x < point[3].x + point[3].width &&
        point[3].y < player.y && player.y < point[3].y + point[3].height && point[3].active == true){
            player.points = player.points + 50;
            console.log("Point")
            point[3].active = false;
        }
        if(boost[0].x < player.x && player.x < boost[0].x + boost[0].width &&
        boost[0].y < player.y && player.y < boost[0].y + boost[0].height){
            player.jump = false;
            player.y = boost[0].y;
            gravity = 0.25;
            friction = 0.7;
        }
        if(platforms[0].x < player.x && player.x < platforms[0].x + platforms[0].width &&
        platforms[0].y < player.y && player.y < platforms[0].y + platforms[0].height){
            player.jump = false;
            player.y = platforms[0].y;
            gravity = 0.5;
        }
        if(ice[0].x < player.x && player.x < ice[0].x + ice[0].width &&
        ice[0].y < player.y && player.y < ice[0].y + ice[0].height){
            player.jump = false;
            player.y = ice[0].y;
            gravity = 0.5;
            friction = 0.99;
        }
        if(ice[1].x < player.x && player.x < ice[1].x + ice[1].width &&
        ice[1].y < player.y && player.y < ice[1].y + ice[1].height){
            player.jump = false;
            player.y = ice[1].y;
            gravity = 0.5;
            friction = 0.99;
        }
        if(spikes[0].x < player.x && player.x < spikes[0].x + spikes[0].width &&
        spikes[0].y < player.y && player.y < spikes[0].y + spikes[0].height){
            player.death = true;
        }
        if(spikes[1].x < player.x && player.x < spikes[1].x + spikes[1].width &&
        spikes[1].y < player.y && player.y < spikes[1].y + spikes[1].height){
            player.death = true;
        }

        if(roof[0].x < player.x && player.x < roof[0].x + roof[0].width &&
        roof[0].y < player.y && player.y < roof[0].y + roof[0].height){
            player.jump = false;
            player.y = roof[0].y;
            gravity = 0.5;
            friction = 0.7;
        }
        if(roof2[0].x < player.x && player.x < roof2[0].x + roof2[0].width &&
        roof2[0].y < player.y && player.y < roof2[0].y + roof2[0].height){
            player.jump = false;
            player.y = roof2[0].y;
            gravity = 0.5;
            friction = 0.7;
        }

        if(platforms[1].x < player.x && player.x < platforms[1].x + platforms[1].width &&
        platforms[1].y < player.y && player.y < platforms[1].y + platforms[1].height){
            player.jump = false;
            player.y = platforms[1].y;
        }
        if(t==0){
            player.death = true;
        }
        // Rendering the canvas, the player and the platforms
        
        rendercanvas1();
        renderPoint();
        renderplayerR();
        renderbaddieR();
        renderplat();
        renderIce();
        renderSpikes();
        renderFloor();
        renderLava();
        renderBoost();
        renderRoof();
        renderGoal();
        renderCount();
        renderTimer();
        }
        if(player.level == 2){
            rendercanvas2();
            renderBackground();
            renderplayerR();
            renderFloorLvl2();
            //renderCount();
            //renderTimer();
            if(floorLvl2[0].x < player.x && player.x < floorLvl2[0].x + floorLvl2[0].width &&
            floorLvl2[0].y < player.y && player.y < floorLvl2[0].y + floorLvl2[0].height){
            player.jump = false;
            player.y = floorLvl2[0].y;
        }
        }
        if (player.death == true){
            renderMessage();
            player.x = 1000;
            player.y = 1000;
        } 
        if (player.win == true){
            renderMessage1();
            player.x = 1000;
            player.y = 1000;
        } 
        if (player.win == false && player.death == false){
        t=t-1;
        }
    }
    canvas=document.getElementById("canvas");
    ctx=canvas.getContext("2d");
    ctx.canvas.height = 1000;
    ctx.canvas.width = 1000;
    createplat();
    createIce();
    createSpikes();
    createFloor1();
    createFloor2();
    createFloorLvl2();
    createLava();
    createBoost();
    createRoof();
    createRoof2();
    createGoal();
    createPoint();
    renderStartScreen();
    // Adding the event listeners
    document.addEventListener("keydown",keydown);
    document.addEventListener("keyup",keyup);
    /*if (gameStart == true){
    setInterval(loop,20);
    console.log("Start")
    } else {
        clearInterval(loop)
    }*/
</script>
<body>
</body>