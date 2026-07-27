const ftp = require("basic-ftp");
const path = require("path");
require("dotenv").config();

const fs = require("fs");

function requiredEnv(name) {
    const value = process.env[name];

    if (!value) {
        throw new Error(`Missing required environment variable: ${name}`);
    }

    return value;
}

async function uploadDir(client, localDir, remoteDir, ignores = []) {
    await client.ensureDir(remoteDir);
    const entries = fs.readdirSync(localDir, { withFileTypes: true });

    for (let entry of entries) {
        if (ignores.includes(entry.name)) continue;

        const localPath = path.join(localDir, entry.name);
        const remotePath = `${remoteDir}/${entry.name}`;

        if (entry.isDirectory()) {
            await uploadDir(client, localPath, remotePath, ignores);
        } else {
            console.log(`Uploading: ${remotePath}`);
            await client.uploadFrom(localPath, remotePath);
        }
    }
}

async function deploy() {
    const client = new ftp.Client();
    client.ftp.verbose = false;
    try {
        console.log("Connecting to FTP...");
        await client.access({
            host: process.env.FTP_SERVER || "ftpupload.net",
            user: requiredEnv("FTP_USERNAME"),
            password: requiredEnv("FTP_PASSWORD"),
            secure: false
        });

        console.log("Connected! Ensuring /htdocs exists...");
        await client.ensureDir("/htdocs");

        console.log("Starting full upload (skipping node_modules and .git). This WILL take 5-10 minutes...");
        
        const ignores = ['.git', 'node_modules', 'tests', '.github', 'scripts', '.env.example', 'README.md', 'database_export.sql', 'production.env'];
        await uploadDir(client, path.join(__dirname, ".."), "/htdocs", ignores);
        
        console.log("Uploading dynamic production .env...");
        let envContent = fs.readFileSync(path.join(__dirname, "../.env.example"), "utf8");
        envContent = envContent.replace("APP_ENV=local", "APP_ENV=production");
        envContent = envContent.replace("APP_DEBUG=true", "APP_DEBUG=false");
        envContent = envContent.replace("APP_URL=http://localhost", `APP_URL=${requiredEnv("PRODUCTION_APP_URL")}`);
        envContent = envContent.replace("DB_CONNECTION=sqlite", "DB_CONNECTION=mysql");
        envContent = envContent.replace("DB_HOST=127.0.0.1", `DB_HOST=${requiredEnv("PRODUCTION_DB_HOST")}`);
        envContent = envContent.replace("DB_DATABASE=gov", `DB_DATABASE=${requiredEnv("PRODUCTION_DB_DATABASE")}`);
        envContent = envContent.replace("DB_USERNAME=root", `DB_USERNAME=${requiredEnv("PRODUCTION_DB_USERNAME")}`);
        envContent = envContent.replace("DB_PASSWORD=", `DB_PASSWORD=${requiredEnv("PRODUCTION_DB_PASSWORD")}`);
        
        const tempEnvPath = path.join(__dirname, "../production.env");
        fs.writeFileSync(tempEnvPath, envContent);
        await client.uploadFrom(tempEnvPath, "/htdocs/.env");
        fs.unlinkSync(tempEnvPath);

        console.log("Upload finished successfully!");
    } catch (err) {
        console.error("Deploy failed:", err);
    }
    client.close();
}

deploy();
