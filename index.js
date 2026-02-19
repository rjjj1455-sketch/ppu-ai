import 'dotenv/config'; 
import { GoogleGenerativeAI } from "@google/generative-ai";

async function checkModels() {
  const apiKey = process.env.GEMINI_API_KEY;
  
  if (!apiKey) {
    console.error("Waduh! API Key tidak terbaca. Cek file .env Anda.");
    return;
  }

  const response = await fetch(`https://generativelanguage.googleapis.com/v1beta/models?key=${apiKey}`);
  const data = await response.json();
  
  if (data.error) {
    console.error("Masalah API:", data.error.message);
  } else {
    console.log("Koneksi Sukses! Model yang tersedia:");
    console.log(data);
  }
}
checkModels();