import 'dotenv/config';
import express from 'express';
import { GoogleGenerativeAI } from "@google/generative-ai";

const app = express();
app.use(express.json());
app.use(express.static('public')); // Tempat file HTML nanti

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

// INSTRUKSI KHUSUS (System Instruction) agar AI hanya bahas Penajam Paser Utara
const systemInstruction = "Anda adalah asisten lokal Penajam Paser Utara (PPU). Tugas Anda HANYA menjawab pertanyaan seputar Penajam Paser Utara (wisata, sejarah, pemerintahan, IKN di Sepaku, dll). Jika user bertanya di luar topik PPU, jawablah dengan sopan bahwa Anda hanya ahli dalam informasi seputar Penajam Paser Utara.";

app.post('/chat', async (req, res) => {
  try {
    const { message } = req.body;
    // Gunakan model gemini-2.0-flash sesuai daftar Anda
    const model = genAI.getGenerativeModel({ 
      model: "gemini-2.0-flash",
      systemInstruction: systemInstruction 
    });

    const result = await model.generateContent(message);
    const response = await result.response;
    res.json({ text: response.text() });
  } catch (error) {
    res.status(500).json({ error: error.message });
  }
});

app.listen(8000, () => console.log('Server lari di http://localhost:8000'));