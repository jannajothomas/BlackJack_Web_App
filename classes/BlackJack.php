<?PHP


require_once("Card.php");
require_once(__DIR__.'/../src/UnicodeDeck.php');
require_once(__DIR__.'/../src/dataBaseManager.php');

class BlackJack
{
    public $deck = array();
    const SUITS = array("heart", "diamond", "spade", "club");
    const VALUES = ["2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K","A"];

    function __construct(){
        //Connect To DB
        $connectError = connectToDatabase();
        echo $connectError;

        //Create cards
        foreach(self::SUITS as $s){
            foreach(self::VALUES as $v){
                array_push($this->deck,(new Card($v,$s)));
            }
        }
        //shuffle them using a built in function
        shuffle($this->deck);
    }

    //function to manage moving cards off the deck array
    public function dealCard()
    {
        return array_pop($this->deck);
    }

    public function getHandPoints($cardArray)
    {
        $handPoints = $this->totalPoints($cardArray);

        if ($handPoints > 21){
            foreach ($cardArray as $card)
            {
                if($card->getCardPoints() == 11)
                {
                    //The first time an ace is found, permanently set its value to 1 and start over
                    $card->setCardPoints(1);
                    $handPoints = $this->totalPoints($cardArray);
                }
            }
        }
        return $handPoints;
    }

    function totalPoints($cardArray){
        $handPoints = 0;
        foreach ($cardArray as $card)
        {
            $handPoints += $card->getCardPoints();
        }
        return $handPoints;
    }

    function updateDatabase($username,$bet, $outcome, $natural){

        if($outcome == 'win') {
            addWin($username);
            if($natural){
                $bet = $bet * 1.5;
            }
            adjustBalance($username,$bet);
        }if($outcome == 'loss'){
            addLoss($username);
            adjustBalance($username,$bet);
        }
    }


    /**returns a number identifying the outcome if game is over, 0 if game if not over*
     * @param $playerPoints
     * @param $housePoints
     * @param $stand
     * @param $numberOfPlayerCards
     * @return int
     */
    public function winCheck($playerPoints, $housePoints, $stand, $numberOfPlayerCards){
        if($stand || ($playerPoints >= 21)){
            if(($playerPoints == 21)&&($numberOfPlayerCards == 2)){
                /**You Win - Natural**/

                 echo <<<_END
                <style>
                    .playerScore{
                        background-color: red;
                    }
                    .houseScore{
                        background-color: green;
                    }
                </style>
            _END;
                return 1;
            }else if(($housePoints > 21)||(($playerPoints<=21) && ($playerPoints>$housePoints))){
                /**You Win **/
                echo <<<_END
                <style>
                    .playerScore{
                        background-color: green;
                    }
                    .houseScore{
                        background-color: red;
                    }
                </style>
            _END;
                return 2;
            }else if($housePoints == $playerPoints){
                /**Push **/
                echo <<<_END
                <style>
                    .playerScore{
                        background-color: yellow;
                    }
                    .houseScore{
                        background-color: yellow;
                    }
                </style>
            _END;
                return 3;
            } else {
                /**You Lose **/
                echo <<<_END
                <style>
                    .playerScore{
                        background-color: red;
                    }
                    .houseScore{
                        background-color: green;
                    }{
                </style>
                _END;
                return 4;
            }
        }
        return 0;
    }
}

