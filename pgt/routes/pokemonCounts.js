var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

/* GET home page. */
router.get('/:userId', function(req, res, next) {
    var userId = req.params.userId;
    var query = 'select userHas.userId,userPdex,p.name as userPname,userCount from userHas ';
    query += 'left join pokemon as p on pdex = userPdex where userId = ? order by userPname';
    db.all(query,[ userId ], function(err,rows) {
        // need to break the table into chunks
        var tableChunks = [];
        var len = rows.length;
        var pertable = 27;
        var i = 0;
        for (i=0; i < len; i+=pertable){
            tableChunks.push(rows.slice(i,i+pertable));
        }
        res.render('pokemonCounts', { title: 'Pokemon GO Evolve Tracking - Pokemon Counts', tableChunks: tableChunks, rows: rows });
    });
});
router.get('/', function(req, res, next) {
    res.send("Missing userId argument");
});
        
module.exports = router;
