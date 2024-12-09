import express from "express";
import cors from "cors";
import dotenv from "dotenv";
import { testConnection } from "./Database/db.js"; // Make sure this matches the exported name

dotenv.config(); // Initialize dotenv

const app = express();

app.use(cors());
app.use(express.json());

app.listen(process.env.APP_PORT,async () => {
    await testConnection();
    console.log(`Running at http:://localhost:${process.env.APP_PORT}`);
});
