var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

////////////////////////////////////////////////////////////////////////////////
// Pass in the raw data and build up the data for display
////////////////////////////////////////////////////////////////////////////////
function evolveCrunch(d){
    d.pData = [];
    d.gData = {};
    d.gData.singles      = { min: 0, max: 0, cp: 0, desc: "Single evolves ready"   , data : [] };
    d.gData.multi        = { min: 0, max: 0, cp: 0, desc: "Double evolves ready"    , data : [] };
    d.gData.doubles      = { min: 0, max: 0, cp: 0, desc: "Single evolves ready, but double evolves to be considered", data : [] };
    d.gData.singlesClose = { min: 0, max: 0, cp: 0, desc: "Single evolves within 25%"   , data : [] };
    // these next three all host the same type of data, should probably set this
    // up a bit differently (as I am trying to do above)
    d.csv = {};
    d.csv.singles = '';
    d.csv.doubles = '';
    d.csv.doubleo = '';
    d.singles = [];
    d.doubles = [];
    d.multi = [];
    d.attached = "Attached New Data";
    // loop over the user candy data (ie: each pokemon)
    for(var i=0; i < d.userCandy.length; i++){
        var valid = true;
        var n = {};
        n.candies = d.userCandy[i].userEvCandy;
        n.pokemonEvolves = [];
        n.pokemonEvolvesStr2 = '';
        n.class = '';
        n.maxEvLevel = 0;
        n.maxEvCandy = 0;
        n.ev1Candy = 0;
        n.multiEvolveAnOption = false;
        var evolveAnOption = false;
        for(var j=0; j < d.pokemon.length; j++){
            // check if this pokemon is in the right evolve chain
            if( d.pokemon[j].evBase == d.userCandy[i].userEvBase ){
                n.pokemonEvolves.push(d.pokemon[j].name);
                
                // get max evolution level (that should probably have been done in db
                if( d.pokemon[j].evLevel > n.maxEvLevel) { 
                    n.maxEvLevel = d.pokemon[j].evLevel; 
                    n.maxEvCandy += d.pokemon[j].evCandy;
                }
                
                // this conditional builds the evolve string (end version)
                if(d.pokemon[j].evCandy == 0){
                    n.pokemonEvolvesStr2 += d.pokemon[j].name;
                }
                else {
                    n.pokemonEvolvesStr2 += ' > ';
                    n.pokemonEvolvesStr2 += d.pokemon[j].evCandy;
                    n.pokemonEvolvesStr2 += ' > ';
                    n.pokemonEvolvesStr2 += d.pokemon[j].name;
                }
                if ( d.pokemon[j].evLevel == 1 ){
                    n.ev1Candy = d.pokemon[j].evCandy;
                }
                
                // this checks to see if any of the evolves are an option
                if( d.pokemon[j].evCandy > 0 && n.candies >= d.pokemon[j].evCandy ){
                    evolveAnOption = true;
                    //console.log(d.pokemon[j].name+' MaxEvCandy: '+n.maxEvCandy);
                }
            }
        }
        if ( ! evolveAnOption ) { valid = false; }
        if ( n.candies == 0 ){ valid = false; }
        
        if (n.candies >= n.maxEvCandy ){
            n.multiEvolveAnOption = true;
        }

        /// the string is no longer used, but we still need this conditional as a check
        if ( n.maxEvLevel == 0){
            valid = false;
        }
        else if( n.maxEvLevel == 1){
            n.class = 'single-evolve';
            if ( valid ) { 
                //d.singles.push(n); 
                d.gData.singles.data.push(n);
                
                // add up the evolves to have a running total
                // looks like min and max is the same, not sure how/why that was done
                // or if there is a typo
                d.gData.singles.min += Math.floor(n.candies / n.maxEvCandy);
                d.gData.singles.max += Math.floor(n.candies / n.maxEvCandy);
                d.gData.singles.cp = d.gData.singles.max * 500;
                
                // i is an index into userCandy d.userCandy[i] 
                //pokename = 'pokemonName';
                // sort this out for later use in the CSV data
                // total hack relying on the face that the base is one below
                // to do this right we would need to do a lookup
                pdex = d.userCandy[i].userEvBase -1;
                pokename = d.pokemon[pdex].name;
                d.csv.singles += pokename + ',' + n.candies + ',' + Math.floor(n.candies / n.maxEvCandy) + ',0\n';
            }
            else if ((n.candies / n.maxEvCandy) > 0.75 ){
                n.class = 'single-evolve-close';
                d.gData.singlesClose.data.push(n);
            }
        }
        else if( n.maxEvLevel == 2){
            n.class = 'double-evolve';
            if ( n.multiEvolveAnOption && valid ) {
                n.class = 'multi-evolve';
                //d.multi.push(n);
                d.gData.multi.data.push(n);
                d.gData.multi.min += Math.floor(n.candies / n.maxEvCandy);
                d.gData.multi.max += Math.floor(n.candies / n.maxEvCandy);
                d.gData.multi.cp = d.gData.multi.max * 500;

                // sort this out for later use in the CSV data
                pdex = d.userCandy[i].userEvBase;
                // this is a total hack, because I am relying on the evBase being 1 below
                basepdex = d.pokemon[pdex].evBase - 1;
                pokename = d.pokemon[basepdex].name;
                d.csv.doubles += pokename + ',' + n.candies + ',1,' + Math.floor(n.candies / n.maxEvCandy) + '\n';
                d.csv.doubleo += pokename + ',' + n.candies + ',' + Math.floor(n.candies / n.ev1Candy) + ',0\n';
                //console.log({ 'pdex' : pdex, 'basepdex' : basepdex } );
            }
            else if ( valid ) { 
                //d.doubles.push(n); 
                d.gData.doubles.data.push(n);
                
                // figure out the max number of first order evolves
                pdex = d.userCandy[i].userEvBase;
                // this is a total hack, because I am relying on the evBase being 1 below
                basepdex = d.pokemon[pdex].evBase - 1;
                pokename = d.pokemon[basepdex].name;
                d.csv.doubleo += pokename + ',' + n.candies + ',' + Math.floor(n.candies / n.ev1Candy) + ',0\n';
                
                //nevolves = Math.floor(n.candies / n.maxEvCandy);
            }
        }
        else {
            n.class = 'error-evolve';
        }
         
        
        // only push this onto the array if we are still valid
        // dont think we ever get here now...
        //if (valid) {
        //    d.pData.push(n);
        //}
    }
    
    // evolve logic, single evolves, no problem, just 
    // double evolves we have to 
    //d.attached = 'Attached New Data';
    //delete d.userHas;
    //delete d.userCandy;
    //delete d.pokemon;
    //console.log(d);
}
////////////////////////////////////////////////////////////////////////////////
globalData = {};
/* GET home page. */
router.get('/:userId', function(req, res, next) {
    uid = req.params.userId;
    //select pdex,name,userEvCandy as candy from pokemon left join userCandy on pdex = userEvBase where userId = 1 and pdex in ( select evBase from pokemon group by evBase );
    var query = 'select * from pokemon order by pdex;';
    try {
        db.all(query,[ ], function(err,rows) {
            globalData.pokemon = rows;
            var query = 'select * from userCandy where userId = ? order by userEvCandy desc';
            try {
                db.all(query,[uid], function(err,rows){
                    globalData.userCandy = rows;
                    try {
                        var query = 'select * from userHas where userId = ?';
                        db.all(query,[uid], function(err,rows){
                            globalData.userHas = rows;
                            evolveCrunch(globalData);
                            console.log(globalData);
                            res.render('evolve', { title: 'Pokemon GO Evolve Report', data: globalData });
                        });
                    } catch(exception){
                        res.render('evolve', { title: "Error with DB,  loop (3)" , rows: []});
                    }
                    
                    //res.render('evolve', { title: 'Pokemon GO Evolve Report', rows: rows , data: d });
                });
            } catch(exception){
                res.render('evolve', { title: "Error with DB, loop (2)" , rows: []});
            }
        });
    } catch(exception) {
        res.render('evolve', { title: "Error with DB, loop (1)" , rows: []});
    }
});
router.get('/', function(req, res, next) {
    res.send("Missing userId argument");
});
        
module.exports = router;
