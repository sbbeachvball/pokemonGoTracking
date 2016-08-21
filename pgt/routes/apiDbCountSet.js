var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/:userId/:pdex/:count', function(req, res, next) {
    var userId = req.params.userId;
    var pdex = req.params.pdex;
    var count = req.params.count;
    var logstr = "set pokemon count: " + userId + ', pdex: '+pdex+', count: '+count
    console.log(logstr);
    res.send(logstr);
    var sqlite3 = require('sqlite3').verbose();
    var db = new sqlite3.Database('../data/pokemon.sqlite');
    db.run('update userHas SET userCount = ? where userPdex = ? and userId = ?',[ count,pdex,userId]);
});

router.get('/', function(req, res, next) {
    res.send('Missing arguments');
});
        
module.exports = router;
