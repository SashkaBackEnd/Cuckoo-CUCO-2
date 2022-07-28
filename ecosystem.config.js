module.exports = {
    apps : [{
        name: "cuckoo-socket",
        script: "./socket.js",
        out_file: "./socket.log",
        autorestart: true,
        restart_delay: 5000,
    }]
}
