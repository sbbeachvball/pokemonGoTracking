var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

/* GET home page. */
router.get('/', function(req, res, next) {
    var userId = req.params.userId;
    //select pdex,name,userEvCandy as candy from pokemon left join userCandy on pdex = userEvBase where userId = 1 and pdex in ( select evBase from pokemon group by evBase );
    var query = 'select * from user';
    try {
        db.all(query,[ ], function(err,rows) {
            res.render('index', { title: 'Pokemon GO Evolve Tracking - Pokemon Candy', rows: rows });
        });
    } catch(exception) {
        res.render('index', { title: "Error with DB" });
    }
    // should query db for users and index
    //res.render('index', { title: 'Pokemon GO Evolve Tracking - Index' });
});
        
module.exports = router;
