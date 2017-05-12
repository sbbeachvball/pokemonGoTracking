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
define('INIT_TABLES',true);
//$f = '../data/pokemonBaseIndex.csv';

if( ! file_exists( POKEDEX_BASE_FILE ) ){
    print "Filename " . POKEDEX_BASE_FILE . " is hardwired into code which is expected to be run from the utils directory\n";
    exit(1);
}
////////////////////////////////////////////////////////////////////////////////
class evTracker {
    function __construct(){
        $this->track = array();
        $this->finalTrack = array();
    }
    function addEntry($evBase,$evLevel){
        if ( isset($this->track[$evBase] )){
            $this->track[$evBase][] = $evLevel;
        }
        else {
            $this->track[$evBase] = array();
            $this->track[$evBase][] = $evLevel;
        }
    }
    function reduce(){
        foreach($this->track as $i => $t){
            $tmp = array_unique($t);
            asort($tmp);
            $tmp2 = array();
            foreach($tmp as $v){
                $tmp2[] = $v;
            }
            $this->finalTrack[$i] = $tmp2;
            //print "index: $i, list: " . implode(",",$this->finalTrack[$i]) . "\n";
        }
    }
    function getNumEvs($base){
        if (isset($this->finalTrack[$base])){
            return count($this->finalTrack[$base]);
        }
        return 0;
    }
    function getTracking($base){
        if (isset($this->finalTrack[$base])){
            return $this->finalTrack[$base];
        }
        return array();
    }
}
////////////////////////////////////////////////////////////////////////////////
class pokemon {
    function specialCandy($indexes,$candies){
        $this->specialIndexes[] = $indexes;
        $this->specialCandies[] = $candies;
    }
    function __construct($index,$name,$evLevel,$evFrom,$evBase){
        $this->index = $index;
        $this->name = $name;
        $this->evLevel = $evLevel;
        //$this->evFrom = ($evLevel > 0) ? $index -1 : 0;   // could have this be index
        $this->evFrom = $evFrom;   // could have this be index
        //$this->evBase = $index - $evLevel;
        $this->evBase = $evBase;
        $this->evCandy = 0;
        $this->specialIndexes = array();
        $this->specialCandies = array();
        $this->dbData = array();
    }
    // numEvs is the base index
    // this routine is sorta broken in a few ways right now
    // one is the babies intro (using index -1)
    function calcCandyNew($evtr){
        // special cases that describe a set of base poke indexes and their candy requirements
        // leave the 0 index one in to aid in indexing arrays
        // The only totally odd case, not covered here is the eevee evolutions
        $this->specialCandy(array(10,13,16),array(0,12,50));
        $this->specialCandy(array(129),array(0,400));
        $this->specialCandy(array(19,133,161,165,183),array(0,25));
        $this->specialCandy(array(236,238,239,240),array(0,25));
        //if( $evNum
        // check to see if the base index here is in the specials
        $candyIndex = null;
        foreach($this->specialIndexes as $key => $si){
            if( in_array($this->evBase,$si)) {
                $candyIndex = $this->specialCandies[$key];
            }
        }
        $evTrackArray = $evtr->getTracking($this->evBase);
        $whichEv = -1;
        foreach($evTrackArray as $i => $v){
            //if ( $this->index == 236 ){
            //    print "whichEv prep {$this->index} {$this->evBase} {$this->evFrom} {$this->evLevel}: i: $i, v: $v\n";
            //}
            if ($this->evLevel == $v){
                $whichEv = $i;
            }
        }
        if ( $whichEv < 0 ){
            print "Issue with locating an Evolution value for: {$this->index}\n";
        }
        
        $numEvs = count($evTrackArray);
        if( is_null($candyIndex) && $numEvs == 1){  // non-evolving pokemon
            $candyIndex = array(0);
        }
        if( is_null($candyIndex) && $numEvs == 2){  // single evolution pokemon
            $candyIndex = array(0,50);
        }
        if( is_null($candyIndex) && $numEvs == 3){  // dual evolution pokemon
            $candyIndex = array(0,25,100);
        }
        if( is_null($candyIndex)){
            print "Fell through all cases, not sure why, shouldnt happen - pokemon index: {$this->index} (numEvs: {$numEvs})\n";
        }
        // find which index this value is
        
        
        // this indexing no longer works either
        //$whichEv = $this->index - $this->evBase;
        if( ! isset($candyIndex[$whichEv])){  
            // somethings wrong, this was a wierd edge case at one point,
            
            print "Wierd edge case?\n";
            //// assume this totally wierd case is eevee and make the necessary adjustments
            //print "Wierd Eevee case?: {$this->index} ($whichEv) (" . implode(",",$candyIndex) . ") (evLevel: {$this->evLevel})\n";
            //print "                 : evTrackArray: " . implode(",",$evTrackArray) . "\n";
            
            //$this->evBase = 133;
            //$this->evFrom = 133;
            //$this->evCandy = 25;      
        }
        else {
            $this->evCandy = $candyIndex[$whichEv];
        }
    }
    // numEvs is the base index
    // this routine is sorta broken in a few ways right now
    // one is the babies intro (using index -1)
    /////function calcCandy($numEvs){
    /////    // special cases that describe a set of base poke indexes and their candy requirements
    /////    // leave the 0 index one in to aid in indexing arrays
    /////    // The only totally odd case, not covered here is the eevee evolutions
    /////    $this->specialCandy(array(10,13,16),array(0,12,50));
    /////    $this->specialCandy(array(129),array(0,400));
    /////    $this->specialCandy(array(19),array(0,25));
    /////    $this->specialCandy(array(133),array(0,25));
    /////    //if( $evNum
    /////    // check to see if the base index here is in the specials
    /////    $candyIndex = null;
    /////    foreach($this->specialIndexes as $key => $si){
    /////        if( in_array($this->evBase,$si)) {
    /////            $candyIndex = $this->specialCandies[$key];
    /////        }
    /////    }
    /////    if( is_null($candyIndex) && $numEvs == 0){  // non-evolving pokemon
    /////        $candyIndex = array(0);
    /////    }
    /////    if( is_null($candyIndex) && $numEvs == 1){  // single evolution pokemon
    /////        $candyIndex = array(0,50);
    /////    }
    /////    if( is_null($candyIndex) && $numEvs == 2){  // dual evolution pokemon
    /////        $candyIndex = array(0,25,100);
    /////    }
    /////    if( is_null($candyIndex)){
    /////        print "Fell through all cases, not sure why, shouldnt happen - pokemon index: {$this->index} (numEvs: {$numEvs})\n";
    /////    }
    /////    
    /////    $whichEv = $this->index - $this->evBase;
    /////    if( ! isset($candyIndex[$whichEv])){  
    /////        // assume this totally wierd case is eevee and make the necessary adjustments
    /////        $this->evBase = 133;
    /////        $this->evFrom = 133;
    /////        $this->evCandy = 25;      
    /////    }
    /////    else {
    /////        $this->evCandy = $candyIndex[$whichEv];
    /////    }
    /////}
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
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
$lines = file(POKEDEX_BASE_FILE);
$evTracker = new evTracker();

$dsn = "sqlite:".POKEDEX_DB;
try {
    $dbh = new PDO($dsn);
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
}

$allPokemon = array();
$evNumTracking = array();
foreach($lines as $line){
    if ( preg_match("/^#/",$line)){
        continue;
    }
    list($index,$evLevel,$evBase,$evFrom,$name) = explode(",",trim($line));
    $p = new pokemon($index,$name,$evLevel,$evFrom,$evBase);
    $base = $p->evBase;
    
    $evTracker->addEntry($base,$evLevel);
    
    //$candy = calcCandy($evLevel,$base);
    //if( isset($evNumTracking[$base]) )  $evNumTracking[$base]++;
    //else                                $evNumTracking[$base] = 0;
    $allPokemon[$index] = $p;
}

$evTracker->reduce();

// loop over all pokemon and fill in the evolve candy
///foreach( $allPokemon as $i => &$p){
///    $p->calcCandyNew($evTracker);
///    
///    //$p->calcCandy($evNumTracking[$p->evBase]);
///    //if (! $ins->execute($p->dbPrep())){
///    //    print "Error on inserting into db\n";
///    //}
///}

$opts = getopt('',array("create-tables:","drop-tables:","load-tables:"));


$td = array();
$td['drop-tables'] = array();
$td['create-tables'] = array();

function tableDef($key,$table,$def){
    global $td;
    $td['drop-tables'][$key] = 'drop table if exists ' . $table . ';';
    $td['create-tables'][$key] = 'create table if not exists ' . $table . ' ' . $def . ";";
}

tableDef('user'   ,'user'     ,'(uid int primary key not null, username text)');
tableDef('candy'  ,'userCandy','(rowid integer primary key autoincrement, userId int, userEvBase int, userEvCandy int)');
tableDef('has'    ,'userHas'  ,'(rowid integer primary key autoincrement, userId int, userPdex int, userCount int)');
tableDef('pokemon','pokemon'  ,'(pdex int primary key not null, name text, evBase int, evFrom int, evLevel int, evCandy int)');

//print var_dump($opts);

// print var_dump($td);

if( isset($opts['drop-tables'])){
    $tables = explode(",",$opts['drop-tables']);
    foreach($tables as $tableKey){
        print "drop key: " . $tableKey . "\n";
        if ( isset($td['drop-tables'][$tableKey])){
            print "sql: " . $td['drop-tables'][$tableKey] . "\n";
            $res  = $dbh->query($td['drop-tables'][$tableKey]);
        }
    }
}

if( isset($opts['create-tables'])){
    $tables = explode(",",$opts['create-tables']);
    foreach($tables as $tableKey){
        print "create key: " . $tableKey . "\n";
        if ( isset($td['create-tables'][$tableKey])){
            print "sql: " . $td['create-tables'][$tableKey] . "\n";
            $res  = $dbh->query($td['create-tables'][$tableKey]);
        }
    }
}

if( isset($opts['load-tables'])){
    $tables = explode(",",$opts['load-tables']);
    foreach($tables as $tableKey){
        //######### pokemon
        if( $tableKey == 'pokemon' ){
            // build pokedex based data
            $ins = $dbh->prepare("insert or replace into pokemon ( pdex,name,evBase,evFrom,evLevel,evCandy) VALUES (:pdex,:name,:evBase,:evFrom,:evLevel,:evCandy);");
            foreach( $allPokemon as &$p){
                //$p->calcCandy($evNumTracking[$p->evBase]);
                $p->calcCandyNew($evTracker);
                if (! $ins->execute($p->dbPrep())){
                    print "Error on inserting into db\n";
                }
            }
        }
        //######### user
        if( $tableKey == 'user' ){
        }
    }
}

/////////// create database and some data
/////////if ( false ) $res = $dbh->query("drop table if exists pokemon;");
/////////$res = $dbh->query("create table if not exists pokemon (pdex int primary key not null, name text, evBase int, evFrom int, evLevel int, evCandy int);");
/////////
/////////// users
/////////if ( INIT_TABLES )  $res = $dbh->query("drop table if exists user;");
/////////$res = $dbh->query("create table if not exists user (uid int primary key not null, username text);");
///////////$res = $dbh->query("insert into user (user, username) VALUES (1,'aaron');");
///////////$res = $dbh->query("insert into user (user, username) VALUES (2,'malia');");
/////////
/////////// userData
/////////// hmmmm, need to track whether a user has a specific evolution to make calculations
/////////if ( INIT_TABLES ) $res = $dbh->query("drop table if exists userCandy;");  // will want to comment this out once in use
/////////$res = $dbh->query("create table if not exists userCandy (rowid integer primary key autoincrement, userId int, userEvBase int, userEvCandy int);");
/////////
/////////if ( INIT_TABLES ) $res = $dbh->query("drop table if exists userHas;");  // will want to comment this out once in use
/////////$res = $dbh->query("create table if not exists userHas (rowid integer primary key autoincrement, userId int, userPdex int, userCount int);");
///////////print_r($res);
/////////


// this query is close to getting the data I need from the db...
// could do some joins on the data as well.... to get the ev1 and ev2 data???
//  select pdex,name from pokemon where pdex in ( select evBase from pokemon group by evBase );
// select p.pdex,p.name,ev1.name from pokemon as p left join pokemon as ev1 on p.pdex = ev1.evBase and ev1.evLevel = 1 where p.pdex in ( select evBase from pokemon group by evBase );
// select p.pdex,p.name,ev1.name,ev2.name from pokemon as p left join pokemon as ev1 on p.pdex = ev1.evBase and ev1.evLevel = 1 left join pokemon as ev2 on p.pdex = ev2.evBase and ev2.evLevel = 2 where p.pdex in ( select evBase from pokemon group by evBase );