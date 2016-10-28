<?php
define('NL',"\n");
define('FILE_IN',"./data.csv");

// define column indexes for the CSV data file
define('EVC',1);
define('CND',2);
define('L1',3);
define('L2',4);

// 16 works well for portrait, 10 or 12 for landscape
//define('ENTRIES_PER_CONTAINER',16);
define('ENTRIES_PER_CONTAINER',10);

?>
<!DOCTYPE html>
<html>
<head>
<title>Proof of Concept report</title>
<style>
body {
    font-family: helvetica, arial, san-serif;
    font-size: 80%;
}
table {
    border: none;
}
td {
    border: none;
}
tr {
}
.content {
    vertical-align: top;
}
caption {
    text-align: left;
    font-size: 120%;
}
.desc {
    /* want bottom border only */
    height: 10px;
    width: 80px;
    border-bottom: 1px solid blue;
}
.ev1 {
    height: 12px;
    width: 12px;
    padding: 0px;
    border: 1px solid blue;
    text-align: center;
    vertical-align: middle ;
    color: #aaa;
    font-size: 75%;
    display: table-cell;
}
.ev2 {
    height: 16px;
    width: 16px;
    border: 3px solid blue;
    text-align: center;
    color: #aaa;
    font-size: 100%;
    /* 
    background-color: #ccf;
    */
}
.container {
    border: 2px solid black;
    padding: 7px;
    margin: 3px;
    border-radius: 8px;
    display: inline-block;
    vertical-align: top;
}
.conWide {
    width: 180px;
}
.conNarrow {
    width: 140px;
}
div.table {
}
div.row {
}
div.cell {
    display: inline-block;
    padding: 0px;
    margin: 2px;
    text-align: center;
    vertical-align: middle ;
}
hr {
    margin: 3px;
}
p {
    /*
    -webkit-margin-before: 0px;
    -webkit-margin-after: 0px;
    -webkit-margin-start: 0px;
    -webkit-margin-end: 0px;
    padding: auto;
    */
    margin: auto;
}

</style>
</head>
<body>
<header>
</header>
<div class="content">
<?php

$GLOBALS['objects'] = array();

////////////////////////////////////////////////////////////////////////////////
// input file is a csv of pokemon,1stLevelEvolves,2ndLevelEvolves
////////////////////////////////////////////////////////////////////////////////
function readInputFile($file){
    $lines = file($file);
    foreach($lines as $line){
        $records = explode(",",trim($line));
        $key = $records[0];
        if( isset($GLOBALS['objects'][$key])){
            print "duplicated key: $key\n";
        }
        $GLOBALS['objects'][$key] = $records;
        
        //$GLOBALS['data'][$key] = $records[1];
    }
}
////////////////////////////////////////////////////////////////////////////////
function mimicTable($k,$entryNum,$candies,$v1,$v2,$cols){
    $v = $v1 + $v2;
    $nl = "\n";
    $b = '';
    if ( $entryNum ) $b .= '<hr>';
    $b .= '<div class="table">' . NL;
    $b .= '<div class="caption">' . $k . ' (' . $candies . ')</div>' . NL;
    
    for($i=0; $i < $v ; $i++){
        $clstr = ( $i < $v1 ) ? "ev1" : "ev2";
        $cv = $i + 1;
        if ($cols ){
            if( $i == 0 ) $b .= '<div class="row">';
            if( ! ($i % $cols ) && $i  ) $b .= '</div>' . NL . '<div class="row">';
            $b .= '<div class="cell ' . $clstr . '"><p>' . $cv . '</p></div>';
            if( $i == ($v-1)) $b .= '</div>' . NL;
        }
        else {
            $b .= '<div class="row">';
            $b .= '<div class="cell ' . $clstr . '">' . $cv . '</div>';
            $b .= '<div class="cell desc"></div>';
            $b .= '</div>' . NL;
        }
    }
    $b .= '</div>' . NL;
    return $b;
}
////////////////////////////////////////////////////////////////////////////////
function  print_pre($data,$label){
    print "<pre>\n";
    print_r($data);
    print "</pre>\n";
}
////////////////////////////////////////////////////////////////////////////////
// Ideally want to sort the results by number to do
// a(r)sort works perfectly for this simple data, if we have first and second
// evolution data, not clear how we will do it...  how to key that some number
// of the evolves are second level evolves??????

// maybe deal with objects and then create an array that duplicates the original
// data structure that can then reference an associative array of the objects.
readInputFile(FILE_IN);

// derive an order array from the object data
//print_pre($GLOBALS['objects'],"GLOBALS objects");

$keySort = array();
$totalEvolves = 0;
foreach($GLOBALS['objects'] as $k => $a){
    $keySort[$k] = $a[L1] + $a[L2];
    $totalEvolves += $a[L1] + $a[L2];
}

//print_pre($keySort,"keysort");

arsort($keySort);

// generate the html
$html = '';
$html = '<div class="container conWide">';
$cntr = 0;
$entriesPerContainer = ENTRIES_PER_CONTAINER;
foreach($keySort as $pokemonName => $v){
    $evclass = $GLOBALS['objects'][$pokemonName][EVC];
    $candies = $GLOBALS['objects'][$pokemonName][CND];
    $v1      = $GLOBALS['objects'][$pokemonName][L1];
    $v2      = $GLOBALS['objects'][$pokemonName][L2];
    
    $entryMod = ( $cntr % $entriesPerContainer ); 
    if( $cntr && ! $entryMod ){
        $html .= "</div>\n<div class=\"container conNarrow\">\n";
    }
    
    $html .= mimicTable($pokemonName,$entryMod,$candies,$v1,$v2,10);
    // the last argument for mimicTable was originally expected to be
    // different for each call (0,5,10)
    ////if ( $v <= 5 ){
    ////    $html .= mimicTable($pokemonName,$entryMod,$candies,$v1,$v2,10);
    ////}
    ////elseif( $v > 5 && $v <=15 ){
    ////    $html .= mimicTable($pokemonName,$entryMod,$candies,$v1,$v2,10);
    ////}
    ////else {
    ////    $html .= mimicTable($pokemonName,$entryMod,$candies,$v1,$v2,10);
    ////}
    
    $cntr++;
}
$html .= '<br/>TotalEvolves: ' . $totalEvolves . '</div>' . NL;
print $html;
?>
</div>
<footer>
</footer>
</body>
</html>