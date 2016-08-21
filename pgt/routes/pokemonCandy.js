var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

/* GET home page. */
router.get('/:userId', function(req, res, next) {
    var userId = req.params.userId;
    //select pdex,name,userEvCandy as candy from pokemon left join userCandy on pdex = userEvBase where userId = 1 and pdex in ( select evBase from pokemon group by evBase );
    var query = 'select pdex as userPdex,name as userPname,userEvCandy as userCount,userId from pokemon left join userCandy on pdex = userEvBase ';
    query += 'where userId = ? and pdex in ( select evBase from pokemon group by evBase ) order by userPname';
    try {
        db.all(query,[ userId ], function(err,rows) {
        // need to break the table into chunks
        var tableChunks = [];
        var len = rows.length;
        var pertable = 27;
        var i = 0;
        for (i=0; i < len; i+=pertable){
            tableChunks.push(rows.slice(i,i+pertable));
        }
        res.render('pokemonCandy', { title: 'Pokemon GO Evolve Tracking - Pokemon Candy', tableChunks: tableChunks, rows: rows });
    });
    } catch(exception) {
        console.log("Error with DB, need to reload page");
    }
});
router.get('/', function(req, res, next) {
    res.send("Missing userId argument");
});
        
module.exports = router;