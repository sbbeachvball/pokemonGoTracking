var express = require('express');
var router = express.Router();

/* GET home page. */
router.get('/', function(req, res, next) {
    // should query db for users and index
    res.render('index', { title: 'Pokemon GO Evolve Tracking' });
});
        
module.exports = router;
