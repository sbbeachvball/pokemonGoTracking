var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

// select userPdex,p.name,userCount from userHas left join pokemon as p on pdex = userPdex where userId = 1 ;


/* GET home page. */
router.get('/', function(req, res, next) {
        db.all('select userPdex,p.name as userPname,userCount from userHas left join pokemon as p on pdex = userPdex where userId = 1;', function(err,rows) {
            // need to break the table into chunks
            var tableChunks = [];
            var len = rows.length;
            var pertable = 32;
            var i = 0;
            for (i=0; i < len; i+=pertable){
                tableChunks.push(rows.slice(i,i+pertable));
            }
            //tableChunks.push(rows.slice(i,len));
            //console.log(tableChunks);
            res.render('index', { title: 'Pokemon GO Evolve Tracking', tableChunks: tableChunks, rows: rows });
        });
});
        
module.exports = router;
