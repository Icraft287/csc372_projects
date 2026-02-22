/**
 * Author: Isaac Craft
 * Date: February 22, 2026
 * Description: A simple static file server built with Node.js's built-in
 *              'http' and 'fs' modules. Serves HTML, CSS, JS, images, and
 *              other static assets from a local 'public' folder.
 *              Handles clean URLs (e.g. /about → /about.html),
 *              query-string stripping, and custom 404 pages.
 *
 * AI Usage: Comments and structure reviewed/assisted with Claude.
 */

const http = require('http');
const fs = require('fs');
const path = require('path');

// Port the server will listen on
const PORT = 3000;



// Maps file extensions to their corresponding HTTP Content-Type header values.
// Falls back to 'application/octet-stream' for unknown file types.

const contentTypes = {
    '.html': 'text/html',
    '.css': 'text/css',
    '.js': 'application/javascript',
    '.json': 'application/json',
    '.png': 'image/png',
    '.jpg': 'image/jpeg',
    '.jpeg': 'image/jpeg',
    '.gif': 'image/gif',
    '.svg': 'image/svg+xml',
    '.ico': 'image/x-icon'
};


// Reads a file from disk and writes it to the HTTP response.
// Automatically sets the correct Content-Type based on the file extension.
// Responds with 500 if the file cannot be read.
function serveStaticFile(res, filePath) {
    const ext = path.extname(filePath);

    // Look up the MIME type; default to binary stream if extension is unknown
    const contentType = contentTypes[ext] || 'application/octet-stream';

    fs.readFile(filePath, (err, data) => {
        if (err) {
            // File could not be read — respond with a 500 server error
            res.writeHead(500, { 'Content-Type': 'text/plain' });
            res.end('500 Internal Server Error');
            return;
        }

        // File read successfully — send it with the appropriate Content-Type
        res.writeHead(200, { 'Content-Type': contentType });
        res.end(data);
    });
}


// Handles all incoming requests by resolving the URL to a file in /public,
// then serving that file or returning a 404 if it doesn't exist.

const server = http.createServer((req, res) => {


    // Strip query strings (e.g. /page?ref=foo → /page),
    // lowercase the path for case-insensitive matching,
    // and remove any trailing slash (except for root "/").
    let urlPath = req.url.split('?')[0].toLowerCase();
    if (urlPath !== '/' && urlPath.endsWith('/')) {
        urlPath = urlPath.slice(0, -1);
    }

    
    // Determine which file in /public corresponds to the requested URL path.
    let filePath;
    if (urlPath === '/') {
        // Root request → serve the homepage
        filePath = path.join(__dirname, 'public', 'index.html');
    } else if (path.extname(urlPath) !== '') {
        // URL already has a file extension (e.g. /styles.css) → serve directly
        filePath = path.join(__dirname, 'public', urlPath);
    } else {
        // Clean URL with no extension (e.g. /about) → map to /about.html
        filePath = path.join(__dirname, 'public', urlPath + '.html');
    }

    
    // Before reading, verify the resolved file actually exists on disk.
    // If it doesn't, serve the custom 404 page (or a fallback inline message).
    fs.access(filePath, fs.constants.F_OK, (err) => {
        if (err) {
            // File not found — attempt to serve the custom 404 page
            const notFoundPath = path.join(__dirname, 'public', '404.html');
            res.writeHead(404, { 'Content-Type': 'text/html' });
            fs.readFile(notFoundPath, (err, data) => {
                if (err) {
                    // 404 page itself is missing — fall back to an inline message
                    res.end('<h1>404 - Page Not Found</h1>');
                } else {
                    res.end(data);
                }
            });
        } else {
            // File exists — serve it
            serveStaticFile(res, filePath);
        }
    });
});


// Start the Server

server.listen(PORT, () => {
    console.log(`Server running at http://localhost:${PORT}`);
});