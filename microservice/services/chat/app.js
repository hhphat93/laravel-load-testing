var express = require('express');
const mongoose = require('mongoose');
const { Schema } = mongoose;

/**
 * Server
 */

var app = express();
var server = app.listen(3000, function () {
    var host = server.address().address
    var port = server.address().port

    console.log("Ung dung Node.js dang lang nghe tai dia chi: http://%s:%s", host, port)

})

/**
 * Database
 */

main().catch(err => console.log(err));

async function main() {
    await mongoose.connect('mongodb://chat_user:123456@chat-mongo:27017/chat');


}

/**
 * API
 */
const messageSchema = new Schema({
    user_id: { type: String, default: 'test_user' },
    content: { type: String, default: 'test content' },
    created_at: { type: Date, default: Date.now },
    deleted_at: { type: Date, default: null },
});

const messageModel = mongoose.model('messages', messageSchema);

app.get('/', function (req, res) {
    res.send('Hello World');
})


app.get('/messages', async function (req, res) {
    let messages = await messageModel.find().exec();

    res.send(messages)
})

app.get('/messages/:messageId', async function (req, res) {
    let message = await messageModel.find({ user_id: req.params.messageId }).exec();

    res.send(message)
})

app.post('/messages', async function (req, res) {
    let model = new messageModel()
    model.user_id = Math.random()
    model.content = (Math.random() + 1).toString(36).substring(7);
    model.save()

    res.send(model)
})

/**
 * Test database
 */
async function testStoreMessage() {

    const messageSchema = new Schema({
        user_id: { type: String, default: 'test_user' },
        content: { type: String, default: 'test content' },
        created_at: { type: Date, default: Date.now },
        deleted_at: { type: Date, default: null },
    });

    const model = mongoose.model('messages', messageSchema);

    const doc = new model();
    await doc.save(); // Throws "document must have an _id before saving"
}