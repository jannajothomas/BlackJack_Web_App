<?php

require_once __DIR__ . "/../classes/Card.php";

use PHPUnit\Framework\TestCase;

class CardTest extends TestCase
{
    public function testCardCanBeInitialized(): void{
        new Card(4,"heart");
        $this->assertSame(true,true,"Card can be initialized");
    }

    public function testGetNumberCardValue(): void{
        $card = new Card('4',"heart");
        $this->assertSame('4',$card->getCardValue(),"Function getValue works with number");
    }

    public function testGetLetterCardValue(): void{
        $card = new Card('K',"heart");
        $this->assertSame('K',$card->getCardValue(), "Function getValue works with a letter");
    }

    public function testGetSuit(): void{
        $card = new Card('K',"heart");
        $this->assertSame('heart',$card->getCardSuit(),"Function getSuit works with a heart");
    }

}
