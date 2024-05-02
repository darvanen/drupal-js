var pss = require('pubsubsql'),
    client = pss.createClient(),
    async = require('async');

client.on('error', function(err){
    console.error('Got error:', err.message);
});

client.on('end', function(){
    console.log('CONNECTION ENDED');
});

client.on('ready', function() {
    console.log('CONNECTION READY');

    async.waterfall([
        function(cb) {
            client.query('key Stocks Ticker', function(err, response) {
                console.log("GOT RESPONSE ON key:", response, err);
                cb();
            });
        }, function(cb) {
            client.query('tag Stocks MarketCap', function(err, response) {
                console.log("GOT RESPONSE ON tag:", response, err);
                cb();
            });
        }, function(cb) {
            client.query("subscribe * from Stocks where MarketCap = 'MEGA CAP'", function(err, response) {
                if (err) {
                    console.error("[subscription] error:", err);
                    return;
                }
                if (response.action === 'subscribe') {
                    console.log('[subscription] subscription confirmed!');
                } else if (response.action === 'add'){
                    console.log('[subscription] initial data!', response);
                } else if (response.action === 'insert'){
                    console.log('[subscription] new data!', response);
                }
            });

            cb();
        }
    ]);
})

