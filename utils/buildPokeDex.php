#!/usr/bin/env php
<?php
// this simple script generates some data that will then be manually fine tuned and 
// ulitmately end up in a db.
$f = 'pokemonBaseIndex.txt';
$lines = file($f);
class pokemon {
    function specialCandy($indexes,$candies){
        $this->specialIndexes[] = $indexes;
        $this->specialCandies[] = $candies;
    }
    function __construct($index,$name,$evLevel){
        $this->index = $index;
        $this->name = $name;
        $this->evLevel = $evLevel;
        $this->evFrom = ($evLevel > 0) ? $index -1 : 0;   // could have this be index
        $this->evBase = $index - $evLevel;
        $this->evCandy = 0;
        $this->specialIndexes = array();
        $this->specialCandies = array();
    }
    function calcCandy($numEvs){
        // special cases that describe a set of base poke indexes and their candy requirements
        // leave the 0 index one in to aid in indexing arrays
        // The only totally odd case, not covered here is the eevee evolutions
        $this->specialCandy(array(10,13,16),array(0,12,50));
        $this->specialCandy(array(129),array(0,400));
        $this->specialCandy(array(19),array(0,25));
        $this->specialCandy(array(133),array(0,25));
        //if( $evNum
        // check to see if the base index here is in the specials
        $candyIndex = null;
        foreach($this->specialIndexes as $key => $si){
            if( in_array($this->evBase,$si)) {
                $candyIndex = $this->specialCandies[$key];
            }
        }
        if( is_null($candyIndex) && $numEvs == 0){  // non-evolving pokemon
            $candyIndex = array(0);
        }
        if( is_null($candyIndex) && $numEvs == 1){  // single evolution pokemon
            $candyIndex = array(0,50);
        }
        if( is_null($candyIndex) && $numEvs == 2){  // dual evolution pokemon
            $candyIndex = array(0,25,100);
        }
        if( is_null($candyIndex)){
            print "Fell through all cases, not sure why, shouldnt happen\n";
        }
        
        $whichEv = $this->index - $this->evBase;
        if( ! isset($candyIndex[$whichEv])){  
            // assume this totally wierd case is eevee and make the necessary adjustments
            $this->evBase = 133;
            $this->evFrom = 133;
            $this->evCandy = 25;      
        }
        else {
            $this->evCandy = $candyIndex[$whichEv];
        }
    }
    function output(){
        return $this->index . ' ' . $this->name . ' ' . $this->evBase . ' ' . $this->evFrom . ' ' . $this->evLevel . ' ' . $this->evCandy;
    }
}
$allPokemon = array();
$evNumTracking = array();
foreach($lines as $line){
    list($index,$evLevel,$name) = explode(",",trim($line));
    $p = new pokemon($index,$name,$evLevel);
    $base = $p->evBase;
    
    //$candy = calcCandy($evLevel,$base);
    if( isset($evNumTracking[$base]) )  $evNumTracking[$base]++;
    else                                $evNumTracking[$base] = 0;
    $allPokemon[] = $p;
}

foreach( $allPokemon as &$p){
    $p->calcCandy($evNumTracking[$p->evBase]);
    print $p->output() . "\n";
}