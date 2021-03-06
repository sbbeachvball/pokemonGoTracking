<?php
define('NL',"\n");
define('FILE_IN',"./data.csv");
define('DEBUG',false);

// define column indexes for the CSV data file
define('EVC',1);
define('CND',2);
define('L1',3);
define('L2',4);

// 16 works well for portrait, 10 or 12 for landscape
//define('ENTRIES_PER_CONTAINER',16);
//define('ENTRIES_PER_CONTAINER',14);
//define('COLUMNS_PER_ROW',7);

// should consider making these form entries
if (isset($_GET['epd'])){
    define('ENTRIES_PER_CONTAINER',$_GET['epd']);
}
else {
    define('ENTRIES_PER_CONTAINER',12);
}

if (isset($_GET['cpr'])){
    define('COLUMNS_PER_ROW',$_GET['cpr']);
}
else {
    define('COLUMNS_PER_ROW',7);
}
// there is a css change required to change this at this time, maybe move that into this code
//define('COLUMNS_PER_ROW',7);
define('CONTAINER_WIDTH',((COLUMNS_PER_ROW * 17) + 14));
?>
<!DOCTYPE html>
<html>
<head>
<title>Proof of Concept report</title>
<style>
body {
    font-family: helvetica, arial, san-serif;
    font-size: 80%;
    margin-top: 40px;  /* control the top padding so it fits better on a clipboard :-) */
}
table {
    border: 1px solid black;
    border-collapse: collapse;
}
td{
    border: 1px solid black;
}
.doc {
    width: 500px;
}
.label {
    padding: 4px;
    font-weight: bold;
    font-size: 125%;
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
/* ~ 20px per box...  maybe 18*numperrow + 14 */
.conWide {
    width: 191px;
}
.conNarrow {
    width: 140px;
}
.conWidth {
    width: <?php print CONTAINER_WIDTH ?>px;
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
p.banner-top {
    font-size: 200%;
    font-weight: bold;
}
.single-evolve {
    color: #4f4;
}
.multi-evolve {
    color: #f44;
}
.double-evolve {
    color: #44f;
}

</style>
<!--
<script>
function validateForm() {
    var cpr = document.forms["controls"]["cpr"].value;
    var epd = document.forms["controls"]["epd"].value;
    if (cpr == "") {
        alert("Name must be filled out");
        return false;
    }
}
-->
</script>
</head>
<body>
<header>
<p class="banner-top">
Report Produced on <?php print date('Y-m-d'); ?>
</p>
</header>
<div class="content">

<?php
$html = '';
$GLOBALS['objects'] = array();
////////////////////////////////////////////////////////////////////////////////
function dprint($toPrint){
    if ( DEBUG ) {
        print "$toPrint";
    }
}
////////////////////////////////////////////////////////////////////////////////
// input file is a csv of pokemon,1stLevelEvolves,2ndLevelEvolves
////////////////////////////////////////////////////////////////////////////////
function readInputFile($file){
    $lines = file($file);
    foreach($lines as $line){
        if ( $line[0] == "#" ) { dprint("comment<br />"); continue; }
        if ( $line[0] == "\n" ) { dprint("blank<br />") ; continue; }
        $records = explode(",",trim($line));
        $key = $records[0];
        if( isset($GLOBALS['objects'][$key])){
            dprint("duplicated key: $key<br />\n");
        }
        $GLOBALS['objects'][$key] = $records;
    }
}
////////////////////////////////////////////////////////////////////////////////
function mimicTable($k,$entryNum,$evclass,$candies,$v1,$v2,$cols){
    $v = $v1 + $v2;
    $nl = "\n";
    $b = '';
    if ( $entryNum ) $b .= '<hr>';
    $b .= '<div class="table">' . NL;
    $b .= '<div class="caption"><span class="' . $evclass . '">' . $k . ' (' . $candies . ')</span></div>' . NL;

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

// if we want to sort by number of evolves we need to do this
//print_pre($keySort,"keysort");

// gather keys from the keySort array
$order = array_keys($keySort);

// if we want to sort by pokemon, just sort the order array, otherwise will be
// grouped by type (single evolve, double evolve)...
sort($order);

// can just comment out this sort to keep the types grouped
//arsort($keySort);

// generate the html
$html = '';
$html = '<div class="container conWidth">';
$cntr = 0;
$entriesPerContainer = ENTRIES_PER_CONTAINER;
//foreach($keySort as $pokemonName => $pokemonNumEvolves){
foreach( $order as $pokemonName){
    $evclass = $GLOBALS['objects'][$pokemonName][EVC];
    $candies = $GLOBALS['objects'][$pokemonName][CND];
    $v1      = $GLOBALS['objects'][$pokemonName][L1];
    $v2      = $GLOBALS['objects'][$pokemonName][L2];

    $entryMod = ( $cntr % $entriesPerContainer );
    if( $cntr && ! $entryMod ){
        $html .= "</div>\n<div class=\"container conWidth\">\n";
    }

    $html .= mimicTable($pokemonName,$entryMod,$evclass,$candies,$v1,$v2,COLUMNS_PER_ROW);
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
<table class="doc">
<tr>
<th>&nbsp;</th>
<th>Begin</th>
<th>End</th>
<th>Controls</th>
<th>&nbsp;</th>
</tr>

<tr>
<td class="label">XP</td>
<td class="doc"></td>
<td class="doc"></td>
<td class="doc">Colmns Per Row</td>
<td class="doc">Entries Per Div</td>
</tr>

<tr>
<td class="label">Time</td>
<td class="doc"></td>
<td class="doc"></td>
<?php
    // should build these with php
?>
<td class="doc">
    <form name="controls" onsubmit="return validateForm()">
        <select name="cpr" onchange="this.form.submit()">
            <?php
                foreach(range(6,10) as $cprval){
                    print "<!-- " . $cprval . " vs. " . COLUMNS_PER_ROW . " -->\n";
                    $selected = ( $cprval == COLUMNS_PER_ROW ) ? "selected" : "" ;
                    print "<option $selected>$cprval</option>\n";
                }
            ?>
        </select>
</td>
<td class="doc">
        <select name="epd" onchange="this.form.submit()">
            <?php
            foreach(range(10,18) as $epdval){
                $selected = ( $epdval == ENTRIES_PER_CONTAINER ) ? "selected" : "" ;
                print "<option $selected>$epdval</option>\n";
            }
            ?>
        </select>
    </form></td>
</tr>


</table>
</footer>
</body>
</html>
