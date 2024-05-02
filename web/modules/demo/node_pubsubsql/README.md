# node_pubsubsql
node.js PubSubSql client library

# Connecting

Minimal connection is without any options supplied. In this case, connection is attempted on default path: `localhost:7777`.
``` javascript
var pss = require('pubsubsql'),
    client = pss.createClient();
```

## Connection options
Options are supplied to `createClient` method as an object.

Supported options:

- **path** Complete server endpoint in format `host:port`, e.g. `127.0.0.1:7777`
- **host** Server host, e.g. `127.0.0.1`
- **port** Server port, e.g. `7777`
- **family** Connection type, either `IPv6` or `IPv4`. Default is autodetected from `host`.


Extended example:

``` javascript
var pss = require('pubsubsql'),
    client = pss.createClient({
        path: "127.0.0.1:7777",
        family: "IPv4"
    });
```

# Running commands

All commands are ran using `query` command which accepts two parameters: command itself and callback function.
Callback function is triggered when server responds to the query. In case of subscription, callback is called 
repeatedly every time new data is published.

Function example:

``` javascript
client.query('tag Stocks MarketCap', function(err, response) {
    console.log("GOT RESPONSE for tag:", response, err);
});
```

# Subscribing to data

The following example sets up client connection and once the connection is ready it subscribes to changes in `Stocks` table
when `MarketCap` value is `MEGA CAP`.

``` javascript
var pss = require('pubsubsql'),
    client = pss.createClient();

client.on('error', function(err){
    console.error('Got error:', err.message);
});

client.on('end', function(){
    console.log('CONNECTION ENDED');
});

client.on('ready', function() {
    console.log('CONNECTION READY');

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
})
```
