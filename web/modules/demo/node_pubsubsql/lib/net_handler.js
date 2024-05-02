var net = require('net');

exports.stream_create = function () {
    var self = this;

    // On a reconnect destroy the former stream and retry
    if (this.stream) {
        this.stream.removeAllListeners();
        this.stream.destroy();
    }

	this.stream = net.createConnection(this.connection_options);

    this.stream.once('connect', function () {
        self.on_connect();
    });

    this.stream.on('data', function (buffer_from_socket) {
        // The buffer_from_socket.toString() has a significant impact on big chunks and therefor this should only be used if necessary
        console.log('Net read ' + self.address + ' id ' + self.connection_id); // + ': ' + buffer_from_socket.toString());

        self.stream_reply_parser(buffer_from_socket);
    });

    this.stream.on('error', function (err) {
        self.on_error(err);
    });

    this.stream.once('close', function () {
        self.connection_gone('close');
    });

    this.stream.once('end', function () {
        self.connection_gone('end');
    });
};

exports.on_ready = function () {
    this.ready = true;
    this.emit('ready');
};

exports.on_connect = function () {
    console.log('Stream connected to:' + this.address + ', conn id: ' + this.connection_id);

    this.connected = true;
    this.ready = false;
    this.connections += 1;
    this.emitted_end = false;
    if (this.options.socket_nodelay) {
        this.stream.setNoDelay();
    }
    this.stream.setKeepAlive(this.options.socket_keepalive);
    this.stream.setTimeout(0);

    if (typeof this.auth_pass === 'string') {
        this.do_auth();
    } else {
        this.emit('connect');
        // TODO intialize retry vars, do ready_check
        this.on_ready();
    }
};
