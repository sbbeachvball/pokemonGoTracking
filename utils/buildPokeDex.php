#!/usr/bin/env php
<?php
// this simple script generates some data that will then be manually fine tuned and 
// ulitmately end up in a db.  Most cases ended up getting handled here, but has
// not been completely vetted at this point.

// Input file is expected to be a CSV with 3 records:
//   Pokedex Index #, Evolution Level of that Pokemon, Pokemon Name.
// We expect that evolved pokemon directly follow the base pokemon entry.
// Most fit a very specific pattern other than the eevee evolves which have 
// three evLevel = 1 entries after the base pokemon (eevee).

// filename is hardwired to the 
define('POKEDEX_BASE_FILE','../data/pokemonBaseIndex.csv');
define('POKEDEX_DB','../data/pokemon.sqlite');
define('INIT_TABLES',false);
//$f = '../data/pokemonBaseIndex.csv';

if( ! file_exists( POKEDEX_BASE_FILE ) ){
    print "Filename " . POKEDEX_BASE_FILE . " is hardwired into code which is expected to be run from the utils directory\n";
    exit(1);
}

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
        $this->dbData = array();
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
    function dbPrep(){
        $this->dbData[':pdex'] = $this->index;
        $this->dbData[':name'] = $this->name;
        $this->dbData[':evBase'] = $this->evBase;
        $this->dbData[':evFrom'] = $this->evFrom;
        $this->dbData[':evLevel'] = $this->evLevel;
        $this->dbData[':evCandy'] = $this->evCandy;
        return $this->dbData;
    }
    function output(){
        $sep = ',';
        return $this->index . $sep . $this->name . $sep . $this->evBase . $sep . $this->evFrom . $sep . $this->evLevel . $sep . $this->evCandy;
    }
}


$lines = file(POKEDEX_BASE_FILE);

$dsn = "sqlite:".POKEDEX_DB;
try {
    $dbh = new PDO($dsn);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
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

// create database and some data
if ( false ) $res = $dbh->query("drop table if exists pokemon;");
$res = $dbh->query("create table if not exists pokemon (pdex int primary key not null, name text, evBase int, evFrom int, evLevel int, evCandy int);");

// users
if ( INIT_TABLES )  $res = $dbh->query("drop table if exists user;");
$res = $dbh->query("create table if not exists user (user int primary key not null, username text);");
$res = $dbh->query("insert into user (user, username) VALUES (1,'aaron');");
$res = $dbh->query("insert into user (user, username) VALUES (2,'malia');");

// userData
// hmmmm, need to track whether a user has a specific evolution to make calculations
if ( INIT_TABLES ) $res = $dbh->query("drop table if exists userCandy;");  // will want to comment this out once in use
$res = $dbh->query("create table if not exists userCandy (rowid integer primary key autoincrement, userId int, userEvBase int, userEvCandy int);");

if ( INIT_TABLES ) $res = $dbh->query("drop table if exists userHas;");  // will want to comment this out once in use
$res = $dbh->query("create table if not exists userHas (rowid integer primary key autoincrement, userId int, userPdex int, userCount int);");
//print_r($res);

// build pokedex based data
$ins = $dbh->prepare("insert into pokemon ( pdex,name,evBase,evFrom,evLevel,evCandy) VALUES (:pdex,:name,:evBase,:evFrom,:evLevel,:evCandy);");
foreach( $allPokemon as &$p){
    $p->calcCandy($evNumTracking[$p->evBase]);
    if (! $ins->execute($p->dbPrep())){
        print "Error on inserting into db\n";
    }
}

// Set up some initial fake userdata
foreach( $allPokemon as &$p){
    $userHas = rand(0,4);
    // this first command should have skipped non evBase ids - clean up that issue with the following line
    // delete from userCandy where userEvBase not in ( select distinct(evBase) from pokemon);
    $res = $dbh->query("insert into userHas (userId,userPdex,userCount) VALUES (1,". $p->index . ",$userHas);");
    
    // this should NOT be done for every userCandy, this should only be done for entries that are evLevel 0
    if ( $p->evLevel == 0 ){
        $candyCount = rand(1,40);
        $res = $dbh->query("insert into userCandy (userId,userEvBase,userEvCandy) VALUES (1,". $p->index . ",$candyCount);");
    }
}

// this query is close to getting the data I need from the db...
// could do some joins on the data as well.... to get the ev1 and ev2 data???
//  select pdex,name from pokemon where pdex in ( select evBase from pokemon group by evBase );
// select p.pdex,p.name,ev1.name from pokemon as p left join pokemon as ev1 on p.pdex = ev1.evBase and ev1.evLevel = 1 where p.pdex in ( select evBase from pokemon group by evBase );
// select p.pdex,p.name,ev1.name,ev2.name from pokemon as p left join pokemon as ev1 on p.pdex = ev1.evBase and ev1.evLevel = 1 left join pokemon as ev2 on p.pdex = ev2.evBase and ev2.evLevel = 2 where p.pdex in ( select evBase from pokemon group by evBase );