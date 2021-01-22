<?php

require_once (__DIR__.'/classes/BlackJack.php');
require_once(__DIR__.'/src/UnicodeDeck.php');

// Establish defaults
$connectError=0;
$gameOver = 0;

#establish a database connection, create one if none exists

$game = new BlackJack();		// Create a new deck and start a new game

/**clear all session variables if user plays again**/
session_start();

/**Set default session variables**/
if(empty($_SESSION['betValue'])){
    $_SESSION['betValue'] = 5;                  //Default value for bet is 5
}

/** Update bet value when a new bet is placed **/
if (isset($_GET['betValue'])){
    $_SESSION['betValue'] = $_GET['betValue'];
}

/** Create new user selected **/
if (isset($_GET['createUser'])){
    createUser($_GET['newName'],$_GET['newUserName'],$_GET['newBalance']);

    $userdata = getUserData($_GET['newUserName']);
    $_SESSION['dbName'] = $userdata[0];
    $_SESSION['dbUserName'] = $userdata[1];
    $_SESSION['dbBalance'] = $userdata[2];
    $_SESSION['dbWins'] = $userdata[3];
    $_SESSION['dbLosses'] = $userdata[4];
}

/** Select new user **/
if(isset($_GET['selectUser'])){
    $userdata = getUserData($_GET['userName']);
    $_SESSION['dbName'] = $userdata[0];
    $_SESSION['dbUserName'] = $userdata[1];
    $_SESSION['dbBalance'] = $userdata[2];
    $_SESSION['dbWins'] = $userdata[3];
    $_SESSION['dbLosses'] = $userdata[4];
}

if (empty($_GET) || isset($_GET['again'])){
    /**initial deal**/
    $playerHand[0] = $game->dealCard();
    $houseHand[0] = $game->dealCard();
    $playerHand[1] = $game->dealCard();
    $houseHand[1] = $game->dealCard();

    /**Set session variables **/
    $_SESSION['playerHand'] = $playerHand;
    $_SESSION['playerHandPoints'] = $game->getHandPoints($_SESSION['playerHand']);
    $_SESSION['houseHand'] = $houseHand;
    $_SESSION['houseHandPoints'] = $game->getHandPoints($_SESSION['houseHand']);
}

if (isset($_GET['hit'])) {
    /**Player requests a card**/
    $_SESSION['playerHand'][sizeof($_SESSION['playerHand'])] = $game->dealCard();
    $_SESSION['playerHandPoints'] = $game->getHandPoints($_SESSION['playerHand']);
    $_SESSION['houseHandPoints'] = $game->getHandPoints($_SESSION['houseHand']);

    /**Don't allow user to draw cards if points ==21 **/
    if ($_SESSION['playerHandPoints'] >= 21)
        header("Location: index.php?stand=stand");

    /**Check to see if player busted**/
    $gameOver = $game->winCheck($_SESSION['playerHandPoints'], $_SESSION['houseHandPoints'], 0,sizeof($_SESSION['playerHand']));
}

if (isset($_GET['stand'])) {
    /**Players turn is over, this is the house deciding if they should hit**/
    while (($_SESSION['houseHandPoints'] < 17) && ($_SESSION['houseHandPoints'] <= $_SESSION['playerHandPoints']) && ($_SESSION['playerHandPoints'] <= 21)) {
        $_SESSION['houseHand'][sizeof($_SESSION['houseHand'])] = $game->dealCard();
        $_SESSION['houseHandPoints'] = $game->getHandPoints($_SESSION['houseHand']);
        $_SESSION['playerHandPoints'] = $game->getHandPoints($_SESSION['playerHand']);
    }
    $gameOver = $game->winCheck($_SESSION['playerHandPoints'], $_SESSION['houseHandPoints'], 1, sizeof($_SESSION['playerHand']));
}

if($gameOver !=0){
    /**Victory conditions are met; print final screen**/

    if($gameOver == 1){
        $game->updateDatabase($_SESSION['dbUserName'],$_SESSION['betValue'],"win",true);
    }else if($gameOver ==2){
        $game->updateDatabase($_SESSION['dbUserName'],$_SESSION['betValue'],"win",false);

    }else if($gameOver == 4){
        $game->updateDatabase($_SESSION['dbUserName'],$_SESSION['betValue'],"loss",false);
    }
    $userdata = getUserData($_SESSION['dbUserName']);
    $_SESSION['dbBalance'] = $userdata[2];
    $_SESSION['dbWins'] = $userdata[3];
    $_SESSION['dbLosses'] = $userdata[4];
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Blackjack!</title>
    <meta charset="utf-8">
    <meta name="description" content="PHP Blackjack Project">
    <meta name="created" content="2020-10-14">
    <meta name="author" content="Janna Thomas">
    <link href = "src/Style.css?d=2345" rel = "stylesheet" type = "text/css" />

</head>

<body>
    <header>
        <h1> Black Jack Parlor</h1>
    </header>

    <div class="playerHand">
        <?php
        for ($i = 0; $i < sizeof($_SESSION['playerHand']); $i++) {
            echo '<div class="card">';
            echo getUnicodeCard($_SESSION['playerHand'][$i]);
            echo '</div>';
        }
        ?>
    </div>

    <div class="houseHand">
        <?php
        if ($gameOver == 0){
            for ($j = 0; $j < (sizeof($_SESSION['houseHand']) - 1); $j++) {
                echo '<div class="card">';
                echo getUnicodeCard($_SESSION['houseHand'][$j]);
                echo '</div>';
            }
            /** Face down card */
            echo '<div class="card">';
            echo '&#127136;';
            echo '</div>';
        }else{
            for ($j = 0; $j < sizeof($_SESSION['houseHand']); $j++) {
                echo '<div class="card">';
                echo getUnicodeCard($_SESSION['houseHand'][$j]);
                echo '</div>';
            }
        }
        ?>
    </div>

    <div class="playerScore">
        <?php echo $_SESSION['playerHandPoints'];?>
    </div>

    <div class="houseScore">
        <?php
        if($gameOver != 0){  /** Only show the house score when the game is over **/
            echo $_SESSION['houseHandPoints'];
        } ?>
    </div>


    <footer>
        <div class="footer_info">
            <table class="table_current_user">
                <tr>
                    <td style="width:65%">Player: <?php echo $_SESSION['dbName']; ?></td>
                    <td>Wins: <?php echo $_SESSION['dbWins']; ?></td>
                </tr>

                <tr>
                    <td>Balance: <?php echo $_SESSION['dbBalance']; ?></td>
                    <td>Losses: <?php echo $_SESSION['dbLosses']; ?></td>
                </tr>
            </table>
        </div>

        <div class="footer_user_select">
            <form id="form_new_user" style='text-align:center' action='index.php' method='get'>
                <fieldset id="createUser">
                    <?php
                    if($gameOver !=0){?>
                    <label for="newUserName">Username: </label>
                    <input name="newUserName" id=newUserName form="form_new_user" />

                    <label for="newName">Name: </label>
                    <input name="newName" id=newName form="form_new_user" />

                    <label for="newBalance">Balance:</label>
                    <input name="newBalance" id=newBalance form="form_new_user" size="3"/>


                    <input type='submit' name='createUser' form ="form_new_user" value='Create User'/>

                    <?php } ?>
                </fieldset>

            </form>


            <form id="form_select_user" style='text-align:center' action='index.php' method='get'>
                <fieldset id="selectUser">
                    <?php if($gameOver !=0){ ?>
                        <label for="userName">Players</label>
                    <?php } ?>


                    <?php
                    if($gameOver != 0){?>
                        <select name="userName" id="userName" form="form_select_user" size="1">
                            <option value="none" selected disabled hidden> Select username </option>
                            <?php /**Create drop down menu of users in database **/
                            $users = getUsers();
                            foreach ($users as $u){
                                echo "<option value = $u>$u</option>";
                            }?>
                        </select>
                    <?php } ?>

                    <?php /**Select button for username **/
                    if($gameOver != 0) { ?>
                        <input type='submit' name='selectUser' form="form_select_user" value='Select'>
                    <?php }else{?>
                        <input type='hidden' name='selectUser' form="form_select_user" value='Select'>
                    <?php } ?>
                </fieldset>

            </form>

        </div>
        <div class = "footer_controls">
            <div class = "div_place_bet">
                    <form id="form_bet" style='text-align:center' action='index.php' method='get'>
                        <fieldset id="setBet">
                            <label for="betValue">Bet is <?php echo $_SESSION['betValue'];?></label>

                            <?php if($gameOver != 0){ ?>
                                <input name="betValue" id="betValue" type="number" value="<?php echo $_SESSION['betValue'];?>" min="5" max=<?php echo $_SESSION['dbBalance'];?> step="1" form="form_bet" />

                            <?php }else{ ?>
                                <input type="hidden">
                            <?php } ?>

                            <?php if($gameOver != 0){ ?>
                                <input type='submit' form='form_bet' value='Update Bet' name='stand' />
                            <?php } ?>

                        </fieldset>


                    </form>

            </div>

            <div class = "div_actions">
                <?php if ($gameOver == 0) {
                    /**game is not over;enable hit and stand buttons**/
                    echo '<form style=\'text-align:center\' action=\'index.php\' method=\'get\'>
                                <input type=\'submit\' name=\'hit\' value=\'hit\'/>
                                <input type=\'submit\' name=\'stand\' value=\'stand\'/></form>';
                }else { /** Only option for player is play again **/
                    echo '<form style=\'text-align:center\' action=\'index.php\' method=\'get\'>
                            <input type=\'submit\' name=\'again\' value=\'Play Again\'/></form>';
                } ?>
            </div>
        </div>

    </footer>

</body>
</html>
