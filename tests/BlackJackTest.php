<?php

include __DIR__ . '/../classes/BlackJack.php';

use PHPUnit\Framework\TestCase;

class BlackJackTest extends TestCase
{
    public function testDeckSizeAfterConstructor(){
        $game = new BlackJack();		// Create a new deck and start a new game
        $this->assertSame(52,count($game->deck),"Deck has 52 cards");
    }

    public function testDeckSizeAfterDealingACard(){
        $game = new BlackJack();		// Create a new deck and start a new game
       $game->dealCard();

        $this->assertSame(51,count($game->deck),"A card has been drawn, deck has 51 cards");
    }

    public function testGetUnicodeValue(){
        new BlackJack();		// Create a new deck and start a new game
        $card = new Card("2","heart");
        $unicodeValue = $card->getCardUnicodeValue();
        $this->assertSame("&#125128",$unicodeValue,"Deck has 52 cards");
    }


}
