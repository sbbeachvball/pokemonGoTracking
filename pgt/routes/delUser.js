var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

/* GET home page. */
router.get('/:delUser', function(req, res, next) {
    var delUser = req.params.delUser;
    try {
        db.run('delete from user where uid = ?',[ delUser ]);
        db.run('delete from userHas where userId = ?',[ delUser ]);
        db.run('delete from userCandy where userId = ?',[ delUser ]);
        res.render('delUser', { title: 'Pokemon GO Evolve Tracking' , message: "Deleted User "+delUser });
    } catch(exception) {
        res.send("Hit an exception");
    }
    ///try { 
    ///    db.all('delete from user where uid = ?',[ delUser ], function(err,rows){
    ///        try { 
    ///            db.all('delete from userHas where userId = ?',[ delUser ], function(err,rows){
    ///                try { 
    ///                    db.all('delete from userCandy where userId = ?',[ delUser ], function(err,rows){
    ///                         res.render('delUser', { title: 'Pokemon GO Evolve Tracking' , message: "Deleted User "+delUser });
    ///                    });
    ///                } catch(exception) {
    ///                    res.send('db.all('+query+') failed');
    ///                }
    ///            });
    ///        } catch(exception) {
    ///            res.send('db.all('+query+') failed');
    ///        }
    ///    });
    ///} catch(exception) {
    ///    res.send('db.all('+query+') failed');
    ///}
    ///
    console.log("delUser: "+delUser);
});
router.get('/', function(req, res, next) {
    console.log(req);
    res.send("Missing delUser argument");
});
        
module.exports = router;
