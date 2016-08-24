var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

////////////////////////////////////////////////////////////////////////////////
// Pass in the raw data and build up the data for display
////////////////////////////////////////////////////////////////////////////////
function evolveCrunch(d){
    d.pData = [];
    d.singles = [];
    d.doubles = [];
    d.attached = "Attached New Data";
    // loop over the user candy data
    for(var i=0; i < d.userCandy.length; i++){
        var valid = true;
        var n = {};
        n.candies = d.userCandy[i].userEvCandy;
        n.pokemonEvolves = [];
        n.pokemonEvolvesStr2 = '';
        n.class = '';
        n.maxEvLevel = 0;
        var evolveAnOption = false;
        for(var j=0; j < d.pokemon.length; j++){
            // check if this pokemon is in the right evolve chain
            if( d.pokemon[j].evBase == d.userCandy[i].userEvBase ){
                n.pokemonEvolves.push(d.pokemon[j].name);
                
                // get max evolution level (that should probably have been done in db
                if( d.pokemon[j].evLevel > n.maxEvLevel) { n.maxEvLevel = d.pokemon[j].evLevel; }
                
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
                
                // this checks to see if any of the evolves are an option
                if( d.pokemon[j].evCandy > 0 && n.candies > d.pokemon[j].evCandy ){
                    evolveAnOption = true;
                }
            }
        }
        if ( ! evolveAnOption ) { valid = false; }
        if ( n.candies == 0 ){ valid = false; }
        
        /// the string is no longer used, but we still need this conditional as a check
        if ( n.maxEvLevel == 0){
            valid = false;
        }
        else if( n.maxEvLevel == 1){
            n.class = 'single-evolve';
            if ( valid) { d.singles.push(n); }
        }
        else if( n.maxEvLevel == 2){
            n.class = 'double-evolve';
            if ( valid) { d.doubles.push(n); }
        }
        else {
            n.class = 'error-evolve';
        }
         
        
        // only push this onto the array if we are still valid
        if (valid) {
            d.pData.push(n);
        }
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
                            //console.log(globalData);
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
