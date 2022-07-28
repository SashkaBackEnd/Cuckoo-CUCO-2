var sha256 = require('js-sha256');
const env = require('dotenv').config();
const WebSocket = require('ws');
const amqp = require('amqplib/callback_api');
const wss = new WebSocket.Server({port: process.env.SOCKET_LOCAL_PORT, clientTracking: true});
const mysql = require('mysql');
const fs = require('fs');
const ini = require('ini');
const url = require('url');

function sendToConsole(message) {
    let date_ob = new Date();
    let date = ("0" + date_ob.getDate()).slice(-2);
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    let year = date_ob.getFullYear();
    let hours = date_ob.getHours();
    let minutes = date_ob.getMinutes();
    let seconds = date_ob.getSeconds();
    console.log(year + "-" + month + "-" + date + " " + hours + ":" + minutes + ":" + seconds + ": " + message);
}

sendToConsole("Start server");
// функция проверки авторизации
const checkAuth = (socketToken) => new Promise(function (resolve, reject) {

    // Создаем подключение к БД
    var con = mysql.createConnection({
        host: process.env.DB_HOST,
        user: process.env.DB_USERNAME,
        password: process.env.DB_PASSWORD,
        database: process.env.DB_DATABASE,
        socketPath: process.env.DB_SOCKET_PATH
    });

    // Подключаемся к БД
    con.connect(function (err) {
        if (err) throw err;
    });

    // запрос строк с переданными id пользователя и токеном
    let query = "SELECT id FROM users WHERE api_token = ? AND deleted_at IS NULL";
    con.query(query, [sha256(socketToken)],
        function (error, result) {
            // возвращаем true, если ошибок нет и строка найдена, false - во всех остальных случаях
            if (!error && result !== undefined && result.length === 1) {
                resolve(result[0].id);
            } else {
                reject(false);
            }
        }
    );
    // Отключаемся от БД
    con.end();
});

// Список подключившийся пользователь - экземпляр вебсокета
var users = new Map();

// Список подключившийся пользователь - пользователь с которым открыт диалог
var dialogWith = new Map();

// Действия при подключении пользователя к сокет-серверу
wss.on('connection', function connection(ws, request) {
    // указываем, что сокет живой
    ws.isAlive = true;

    // помечаем сокет живым при получении ответа 'pong'
    ws.on('pong', heartbeat);

    // действия при получении сообщения через сокет
    ws.on('message', function incoming(message) {

    });

    // Парсим url по которому происходит соединение
    var parsedUrl = url.parse(request.url, true);
    // Получаем GET-параметры из url
    var token = parsedUrl.query['t'];

    // Аутентификация пользователя если есть все необходимые данные
    if (typeof token === 'string') {
        checkAuth(token).then(
            response => {
                if (!response) {

                    // Разрываем соединение если не удалось пройти аутентификацию
                    ws.close(4403, 'unauthorized');
                } else {
                    // Добавляем экземпляр сокета в список пользователь-массив сокетов
                    let userId = parseInt(response);
                    let userConnections = users.get(userId);
                    if (userConnections === undefined) {
                        users.set(userId, [ws])
                    } else {
                        userConnections.push(ws);
                        users.set(userId, userConnections)
                    }
                }
            },
            // Разрываем соединение если не удалось пройти аутентификацию
            () => ws.close(4403, 'unauthorized'));
    } else {
        // Разрываем соединение если не хватает данныз для аутентификации
        ws.close(4403, 'unauthorized');
    }
});

// Подключаемсяк серверу RabbitMQ
amqp.connect('amqp://localhost', function (error0, connection) {
    if (error0) {
        throw error0;
    }
    // Создаем канал
    connection.createChannel(function (error1, channel) {
        if (error1) {
            throw error1;
        }
        // Задаем название очереди
        var queue = 'socketCuckoo';

        // Создаем очередь, durable - хранить ли очередь при отключении сервера RabbitMQ
        channel.assertQueue(queue, {
            durable: false
        });

        // Начинаем прослушивать очередь
        channel.consume(queue, function (msg) {
            // При получении сообщения парсим его из json-формата
            let messageText = msg.content;
            users.forEach((wsArray, id) => {
                wsArray.forEach((ws) => {
                    try {
                        ws.send(messageText.toString())
                    } catch (e) {
                        sendToConsole(e.message);
                    }
                })
            });
        }, {
            // Подтверждение об обработке сообщения для сервера RabbitMQ не требуется
            noAck: true
        });
    });
});

// Функция заглушка для проверки живое ли соединение
function noop() {
}

// делаем текущее соединение живым
function heartbeat() {
    this.isAlive = true;
}

// Проверяем все текущие соединения на активность, если не активно то закрываем и удаляем из спиской
const interval = setInterval(function ping() {
    users.forEach((wsArray, id) => {
        wsArray.forEach((ws) => {
            if (ws.isAlive === false) {
                sendToConsole("No pong for socket of " + id + ". Connection will be closed");
                users.delete(id);
                dialogWith.delete(id);
                return ws.terminate();
            }
            ws.isAlive = false;
            ws.ping(noop);
        })
    });
}, 5000);


