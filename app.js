const WebSocket = require('ws');
const mysql = require('mysql');

const wss = new WebSocket.Server({ port: 8080 });

const dbConnection = mysql.createConnection({
    host: 'localhost',
    user: 'MutaleMulenga',
    password: 'Javeria##2019',
    database: 'mila_milatuCase'
});

dbConnection.connect((err) => {
    if (err) {
        console.error('Error connecting to the MySQL database:', err);
        return;
    }
    console.log('Connected to the MySQL database');
});

wss.on('listening', () => {
    console.log('WebSocket server is listening on port 8080');
});

wss.on('connection', (ws, req) => {
    const clientIp = req.socket.remoteAddress;
    console.log(`Client connected from ${clientIp}`);

    ws.on('message', (message) => {
        console.log('Raw message received:', message);
        try {
            const data = JSON.parse(message);
            console.log('Parsed data:', data);

            switch (data.type) {
                case 'INIT':
                    console.log(`Initialized connection for user ${data.userId} from law firm ${data.lawFirmId}`);
                    break;
                case 'INSERT_TIME':
                    const sql = `INSERT INTO page_time_tracking 
                                 (page_url, time_spent, userId, lawFirmId, timestamp) 
                                 VALUES (?, ?, ?, ?, ?)`;
                    const values = [
                        data.pageName,
                        data.timeOnPageMs,
                        data.userId,
                        data.lawFirmId,
                        new Date()
                    ];

                    dbConnection.query(sql, values, (err, result) => {
                        if (err) {
                            console.error('Error inserting data into MySQL:', err);
                            console.error('SQL:', sql);
                            console.error('Values:', values);
                        } else {
                            console.log(`Inserted time tracking data. ID: ${result.insertId}`);
                        }
                    });

                    console.log(`Recorded time for user ${data.userId}: ${data.timeOnPageMs}ms on ${data.pageName}`);
                    break;
                case 'INSERT_ACTIVITY_TIME':
                    const sql2 = `INSERT INTO activity_time_tracking 
                                 (userId, lawFirmId, activity, time_spent_seconds, page_url, timestamp) 
                                 VALUES (?, ?, ?, ?, ?, ?)`;
                    const values2 = [
                        data.userId,
                        data.lawFirmId,
                        data.activity,
                        data.timeSpentSeconds,
                        data.pageName,
                        new Date()
                    ];

                    dbConnection.query(sql2, values2, (err, result) => {
                        if (err) {
                            console.error('Error inserting activity data into MySQL:', err);
                            console.error('SQL:', sql2);
                            console.error('Values:', values2);
                        } else {
                            console.log(`Inserted activity time tracking data. ID: ${result.insertId}`);
                        }
                    });

                    console.log(`Recorded activity time for user ${data.userId}: ${data.timeSpentSeconds}s on ${data.activity}`);
                    break;
                default:
                    console.log('Unknown message type:', data.type);
            }
        } catch (error) {
            console.error('Error processing message:', error);
        }
    });

    ws.on('close', () => {
        console.log(`Client disconnected from ${clientIp}`);
    });

    ws.on('error', (error) => {
        console.error(`WebSocket error for client ${clientIp}:`, error);
    });
});

wss.on('error', (error) => {
    console.error('WebSocket server error:', error);
});

process.on('SIGINT', () => {
    wss.close(() => {
        console.log('WebSocket server closed');
        dbConnection.end((err) => {
            if (err) {
                console.error('Error closing database connection:', err);
            } else {
                console.log('Database connection closed');
            }
            process.exit(0);
        });
    });
});

console.log('WebSocket server started. Waiting for connections...');