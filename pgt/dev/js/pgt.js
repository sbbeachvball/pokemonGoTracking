/*
function incCount(user,pdex,id){
    //console.log('increment: user: '+user+', pdex: '+pdex);
    element = document.getElementById(id);
    curr = parseInt(element.innerHTML);
    curr += 1;
    element.innerHTML = curr.toString();
    //$.ajax('http://localhost:3000/api/db/count/inc/'+user+'/'+pdex);
    $.ajax('http://localhost:3000/api/db/count/set/'+user+'/'+pdex+'/'+curr.toString());
}
function decCount(user,pdex,id){
    // console.log('decrement: user: '+user+', pdex: '+pdex);
    element = document.getElementById(id);
    curr = parseInt(element.innerHTML);
    curr -= 1;
    element.innerHTML = curr.toString();
    $.ajax('http://localhost:3000/api/db/count/set/'+user+'/'+pdex+'/'+curr.toString());
}
function incCandy(user,pdex,id){
    //console.log('increment: user: '+user+', pdex: '+pdex);
    element = document.getElementById(id);
    curr = parseInt(element.innerHTML);
    curr += 1;
    element.innerHTML = curr.toString();
    //$.ajax('http://localhost:3000/api/db/count/inc/'+user+'/'+pdex);
    $.ajax('http://localhost:3000/api/db/candy/set/'+user+'/'+pdex+'/'+curr.toString());
}
function decCandy(user,pdex,id){
    // console.log('decrement: user: '+user+', pdex: '+pdex);
    element = document.getElementById(id);
    curr = parseInt(element.innerHTML);
    curr -= 1;
    element.innerHTML = curr.toString();
    $.ajax('http://localhost:3000/api/db/candy/set/'+user+'/'+pdex+'/'+curr.toString());
}
*/

// The above functions should be deprecated now
// These could likely be refactored into a single function
function alterCandy(user,pdex,id,change){
    element = document.getElementById(id);
    curr = parseInt(element.innerHTML);
    if (isNaN(curr)){
        curr = 0;
    }
    curr += change;
    element.innerHTML = curr.toString();
    $.ajax('http://localhost:3000/api/db/candy/set/'+user+'/'+pdex+'/'+curr.toString());
}
function alterCount(user,pdex,id,change){
    element = document.getElementById(id);
    curr = parseInt(element.innerHTML);
    if (isNaN(curr)){
        curr = 0;
    }
    curr += change;
    element.innerHTML = curr.toString();
    $.ajax('http://localhost:3000/api/db/count/set/'+user+'/'+pdex+'/'+curr.toString());
}
$(document).ready(function(){
     //console.log("hey there");
});

//$(".increment").click(function(){
//    console.log("jQuery click detected");
//});