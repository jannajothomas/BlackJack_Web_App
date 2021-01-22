<?PHP

class Card
{
    protected $value;
    protected $suit = 'none';
    public $points;

    function __construct($new_value, $new_suit){
        $this->value = $new_value;
        $this->suit = $new_suit;
        $this->points = $this->getInitialCardPoints();
    }

    public function getCardPoints(){
        return ($this->points);
    }

    public function setCardPoints($newPoints){
        $this->points = $newPoints;
    }

    public function getCardSuit(){
        return($this->suit);
    }

    public function getCardValue(){
        return($this->value);
    }

    private function getInitialCardPoints():int{
        switch($this->value){
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
                return $this->value;
            case 10:
            case "J":
            case "Q":
            case "K":
                return 10;
            case "A":
                return 11;
        }
        return 0;
    }

    function getCardUnicodeValue(){
        return "&#125128";
    }
}
