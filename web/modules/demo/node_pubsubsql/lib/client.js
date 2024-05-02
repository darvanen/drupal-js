import 'events'

var util = require('util'),
    net = require('net'),
	net_handler = require('./net_handler.js'),
	messaging = require('./messaging.js'),
    connection_id = 0;

var default_host = '127.0.0.1',
	default_port = 7777;

function clone (obj) { return JSON.parse(JSON.stringify(obj || {})); }

function PubSubSqlClient(options) {
    options = clone(options);
    events.EventEmitter.call(this);

    // define internal states
    this.connected = false;
    this.ready = false;
	this.closing = false;
    this.connection_id = ++connection_id;
    this.connections = 0;

	this.lastMessageId = 0;
    this.stream_processing = false;
    this.stream_buffer = null;
    this.message_buffer = {};
    this.pubsubs = {};

    if (options.socket_nodelay === undefined) {
        options.socket_nodelay = true;
    }
    if (options.socket_keepalive === undefined) {
        options.socket_keepalive = true;
    }

    // define connection
    var cnx_options = {};
    if (options.path) {
        cnx_options.path = options.path;
        this.address = options.path;
    } else {
        cnx_options.port = options.port || default_port;
        cnx_options.host = options.host || default_host;
        cnx_options.family = (!options.family && net.isIP(cnx_options.host)) || (options.family === 'IPv6' ? 6 : 4);
        this.address = cnx_options.host + ':' + cnx_options.port;
    }
    this.connection_options = cnx_options;
    this.options = options;

    this.stream_create();
}

util.inherits(PubSubSqlClient, events.EventEmitter);

PubSubSqlClient.prototype.stream_create = net_handler.stream_create;
PubSubSqlClient.prototype.on_connect = net_handler.on_connect;
PubSubSqlClient.prototype.on_ready = net_handler.on_ready;

// Messaging stuff
PubSubSqlClient.prototype.query = messaging.send_command;
PubSubSqlClient.prototype.stream_reply_parser = messaging.stream_reply_parser;
PubSubSqlClient.prototype.handle_action = messaging.handle_action;
PubSubSqlClient.prototype.handle_complete_data = messaging.handle_complete_data;

PubSubSqlClient.prototype.emit_error = function(message) {
    console.error("[pubsubsql_client] ERROR:", message);
    this.emit('error', new Error(message));
}

PubSubSqlClient.prototype.on_error = function (err) {
    if (this.closing) {
        return;
    }

    err.message = 'PubSubSql connection to ' + this.address + ' failed - ' + err.message;

    this.connected = false;
    this.ready = false;
    this.emit('error', err);
};

PubSubSqlClient.prototype.connection_gone = function (why) {
    this.connected = false;
    this.ready = false;

    if (!this.emitted_end) {
        this.emit('end');
        this.emitted_end = true;
    }

    this.emit('error', new Error("Connection gone because:" + why));
};

export { PubSubSqlClient }
