





var express = require('express');
var router = express.Router();
var sqlite3 = require('sqlite3').verbose();
var db = new sqlite3.Database('../data/pokemon.sqlite');

/* GET home page. */
router.get('/', function(req, res, next) {
    var newUser = req.query.newUser;
    
    if ( req.query.newUser === undefined || req.query.newUser === null || req.query.newUser == '' ){
        res.render('newUser', { title: 'Pokemon GO Evolve Tracking - New User Add Failed', message: "New User Name NOT provided" });
    }
    else {
        // need to figure out what the next userId is
        // build insert query
        // build userHas and userCandy entries based on pokemon table
        // 
        try {
            db.all('select uid from user order by uid desc limit 1',[], function(err,rows){
                newuid = rows[0].uid + 1;
                console.log("new user last row: "+rows[0].uid+', nextuid: '+newuid);
                db.run('insert into user (uid,username,fullname) values (?,?,?)',[ newuid, newUser , newUser ]);
                try {
                    db.all('select pdex,evLevel from pokemon',[], function(err,rows){
                        console.log('results: '+rows.length);
                        for(var i = 0; i < rows.length; i++){
                            db.run('insert into userHas (userId, userPdex, userCount) values (?,?,?)',[ newuid, rows[i].pdex, 0 ]);
                            if ( rows[i].evLevel == 0 ){
                                db.run('insert into userCandy (userId, userEvBase, userEvCandy) values (?,?,?)',[ newuid, rows[i].pdex, 0]);
                            }
                        }
                        //res.send("new user last row: "+rows[0].uid+', nextuid: '+newuid);
                        res.render('newUser', { title: 'Pokemon GO Evolve Tracking - New User Added' , message: "New User Add Succeeded" });
                    });
                } catch(exception) {
                }
                
            });
        } catch(exception) {
        }
    }
    console.log("newUser: "+newUser);
    
    
    //select pdex,name,userEvCandy as candy from pokemon left join userCandy on pdex = userEvBase where userId = 1 and pdex in ( select evBase from pokemon group by evBase );
    //
    //var query = '';
    //try {
    //    db.all(query,[ userId ], function(err,rows) {
    //        // need to break the table into chunks
    //        var tableChunks = [];
    //        var len = rows.length;
    //        var pertable = 20;
    //        var i = 0;
    //        for (i=0; i < len; i+=pertable){
    //            tableChunks.push(rows.slice(i,i+pertable));
    //        }
    //        res.render('newUser', { title: 'Pokemon GO Evolve Tracking - New User Added' });
    //    });
    //} catch(exception) {
    //    console.log("Error with DB, need to reload page");
    //}
});
//router.get('/', function(req, res, next) {
//    console.log(req);
//    res.send("Missing newUser argument");
//});
        
module.exports = router;
