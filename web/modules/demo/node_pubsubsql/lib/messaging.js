
exports.send_command = function(command, cb) {

    if (!this.ready || !this.connected) {
        this.on_error(new Error("Cannot send command, not connected/ready!"));
        return;
        // TODO sending a command when not connected should add command to offline queue and send once ready!
    }

    // TODO check if command is really a qualified string command!

    console.log("\n\nSending command ", command);

    // initialize header
    var header = new Buffer(8);
    header.fill(0);

    var msg_id = ++this.lastMessageId;
    header.writeUInt32BE(command.length, 0);
    header.writeUInt32BE(msg_id, 4);

    this.message_buffer[msg_id] = {
        cmd: command,
        callback: cb,
        data: ""
    };

    this.stream.write(header);
    this.stream.write(command);

    console.log("Sent message!");
};

exports.subscribe = function(command, cb) {
    this.send_command(command, cb);
};

exports.handle_action = function(msg_id, response_object) {
    switch(response_object.action) {
        case "subscribe":
            if (!response_object.pubsubid) {
                this.emit_error("[library] MISSING PUBSUBID on 'subscribe' event");
                break;
            }
            this.pubsubs[response_object.pubsubid] = {
                callback: this.message_buffer[msg_id].callback
            };

            // dispatch confirmation
            if (msg_id === 0) {
                this.emit_error("Got SUBSCRIBE without a destination msg_id: " + JSON.stringify(response_object));
            } else if (!this.message_buffer[msg_id]) {
                this.emit_error("Got SUBSCRIBE for an unkown msg_id: " + JSON.stringify(response_object));
            } else {
                this.message_buffer[msg_id].callback(null, response_object);
            }
            break;

        case "add":
        case "insert":
            if (!response_object.pubsubid) {
                this.emit_error("[library] MISSING PUBSUBID on 'add' event");
                break;
            }
            if (!this.pubsubs[response_object.pubsubid]) {
                this.emit_error("UNWANTED PUBLISH with data:" + JSON.stringify(response_object));
                break;
            }
            this.pubsubs[response_object.pubsubid].callback(null, response_object);
            break;
        default:
            console.log("UKNOWN MESSAGE:", response_object);
    };
};

exports.handle_complete_data = function(msg_id, data) {
    var obj = {};
    try {
        obj = JSON.parse(data);
    } catch (e) {
        console.error("[pubsubsql_client] error on JSON decode:", e);
        obj = {
            status: "err",
            msg: "[pubsubsql_client] error on JSON decode: " + e
        };
    }

    if (obj.status === "err") {
        if (msg_id > 0) {
            this.message_buffer[msg_id].callback(new Error(obj.msg));
        } else if (msg_id === 0 && !!obj.pubsubid && !!this.pubsubs[obj.pubsubid]) {
            this.pubsubs[obj.pubsubid].callback(new Error(obj.msg));
        } else {
            // don't know whom the error belongs to...
            this.emit_error(obj.msg);
        }
        return;
    }

    if (obj.status === "ok" && !!obj.action) {
        this.handle_action(msg_id, obj);
    } else {
        // no action... log and call callback
        console.log("[library] UNKNOWN RESPONSE:", obj);
        this.message_buffer[msg_id].callback(null, data);
    }
};

exports.stream_reply_parser = function(buffer) {
    if (this.stream_buffer === null) {
        this.stream_buffer = buffer;
    } else {
        this.stream_buffer = Buffer.concat([this.stream_buffer, buffer], this.stream_buffer.length + buffer.length);
    }

    var offset = 0;

    while (1) {
        var size = this.stream_buffer.readUInt32BE(offset),
            msg_id = this.stream_buffer.readUInt32BE(offset + 4);

        offset += 8;

        if ((this.stream_buffer.length - offset) < size) {
            // message not complete, need to wait more
            this.stream_buffer = this.stream_buffer.slice(offset + size);
            break;
        }

        var msg = this.stream_buffer.slice(offset, offset + size).toString('utf8');
        this.handle_complete_data(msg_id, msg);

        if ((this.stream_buffer.length - offset) === size) {
            // complete buffer read, reset buffer
            this.stream_buffer = null;
            break;
        }
        offset += size;
    };
};
