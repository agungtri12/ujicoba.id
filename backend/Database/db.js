import mysql2 from "mysql2/promise";

// Corrected pool creation
const db = mysql2.createPool({
    host: "localhost",
    user: "root",
    password: "",
    database: "anime_database"
});

// Test connection function
async function testConnection() { // Ensure this name matches across files
    try {
        const connection = await db.getConnection();
        console.log("Koneksi Anda Berhasil");
        connection.release();
    } catch (error) {
        console.error("Koneksi Terputus", error);
    }
}

async function query(command, values) {
    try {
        const [result] = await db.query(command, values ?? []);
        return result;
    } catch (error) {
        console.error(error);
    }
}

export { db, testConnection, query }; // Corrected export names
