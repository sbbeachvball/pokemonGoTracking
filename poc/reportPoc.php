<!DOCTYPE html>
<html>
<head>
<title>Proof of Concept report</title>
<style>
body {
    font-family: helvetica, arial, san-serif;
    font-size: 100%;
}
table {
    border: none;
}
td {
    border: none;
}
tr {
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
.checkbox {
    height: 15px;
    width: 15px;
    padding: 0px;
    border: 1px solid blue;
    text-align: center;
    vertical-align: middle ;
    color: #aaa;
    font-size: 75%;
    display: table-cell;
}
.ev2 {
    height: 20px;
    width: 20px;
    border: 1px solid blue;
    text-align: center;
    color: #aaa;
    font-size: 100%;
}
.container {
    border: 2px solid black;
    padding: 20px;
    width: 300px;
    border-radius: 8px;
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

<?php
define('NL',"\n");
$data = array(
 'rattata' => 27,
 'pidgey' => 38,
 'ponyta' => 2,
 'venonat' => 3,
 'shellder' => 4,
 'caterpie' => 10,
 'spearow' => 6,
 );


function mimicTable($k,$v,$cols){
   
    $nl = "\n";
    $b = '';
    $b .= '<hr><div class="table">' . NL;
    $b .= '<div class="caption">' . $k . '</div>' . NL;
    for($i=0; $i < $v; $i++){
        $cv = $i + 1;
        if ($cols ){
            
            if( $i == 0 ) $b .= '<div class="row">';
            if( ! ($i % $cols ) && $i  ) $b .= '</div>' . NL . '<div class="row">';
            $b .= '<div class="cell checkbox"><p>' . $cv . '</p></div>';
            if( $i == ($v-1)) $b .= '</div>' . NL;
        }
        else {
            $b .= '<div class="row">';
            $b .= '<div class="cell checkbox">' . $cv . '</div>';
            $b .= '<div class="cell desc"></div>';
            $b .= '</div>' . NL;
        }
    }
    $b .= '</div>' . NL;
    return $b;
}
function buildTableWithDesc($k,$v,$cols){
   
    $nl = "\n";
    $b = '';
    $b .= '<hr><table>' . NL;
    $b .= '<caption>' . $k . '</caption>' . NL;
    for($i=0; $i < $v; $i++){
        $cv = $i + 1;
        if ($cols ){
            
            if( $i == 0 ) $b .= '<tr>';
            if( ! ($i % $cols ) && $i  ) $b .= '</tr>' . NL . '<tr>';
            $b .= '<td class="checkbox">' . $cv . '</td>';
            if( $i == ($v-1)) $b .= '</tr>' . NL;
        }
        else {
            $b .= '<tr>';
            $b .= '<td class="checkbox">' . $cv . '</td>';
            $b .= '<td class="desc"></td>';
            $b .= '</tr>' . NL;
        }
    }
    $b .= '</table>' . NL;
    return $b;
}

// Ideally want to sort the results by number to do
// a(r)sort works perfectly for this simple data, if we have first and second
// evolution data, not clear how we will do it...  how to key that some number
// of the evolves are second level evolves??????
arsort($data);

// generate the html
$html = '';
$html = '<div class="container">';
//foreach($data as $k => $v){
//    if ( $v <= 5 ){
//        $html .= buildTableWithDesc($k,$v,0);
//    }
//    elseif( $v > 5 && $v <=15 ){
//        $html .= buildTableWithDesc($k,$v,10);
//    }
//    else {
//        $html .= buildTableWithDesc($k,$v,10);
//    }
//}
foreach($data as $k => $v){
    if ( $v <= 5 ){
        $html .= mimicTable($k,$v,0);
    }
    elseif( $v > 5 && $v <=15 ){
        $html .= mimicTable($k,$v,10);
    }
    else {
        $html .= mimicTable($k,$v,10);
    }
}
$html .= '</div>' . NL;
print $html;
?>

<footer>
</footer>
</body>
</html>