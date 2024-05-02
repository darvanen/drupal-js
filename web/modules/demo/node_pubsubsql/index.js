var PubSubSqlClient = require('./lib/client.js').PubSubSqlClient;


var createClient = function () {
    return new PubSubSqlClient();
}

exports.createClient = createClient;
