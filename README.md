# WIA-udang
Cityzen

Cityzen adalah website pelaporan warga untuk bidang transportasi, fasilitas umum, dan infrastruktur. Warga dapat melaporkan kerusakan lewat foto, lalu memantau penanganannya sampai selesai dan memberi penilaian kepuasan.

---

Alur Penggunaan
1. Warga mengunggah laporan beserta foto kerusakan.
2. Pemerintah memverifikasi dan mengonfirmasi laporan.
3. Laporan diproses dan progres perbaikan dapat dipantau.
4. Warga mengonfirmasi bahwa perbaikan sudah selesai.
5. Warga memberi rating tingkat kepuasan terhadap penanganan.

---

Tech Stack
| Frontend | HTML, CSS, JavaScript |
| Desain UI/UX | Figma |
| Dokumentasi | Microsoft Word |

---

Cara Menjalankan Project

Project ini masih berupa website statis, jadi tidak perlu install dependency apa pun.
 Prasyarat:
- Browser modern (Chrome, Firefox, Edge)
- Git

Langkah-langkah
### Prasyarat 
- [PHP](https://www.php.net/) (sesuai versi di `composer.json`) 
- [Composer](https://getcomposer.org/) 
- [Node.js & npm](https://nodejs.org/) 
- Git

### Langkah-Langkah
```bash
# 1. Clone repository
git clone https://github.com/dipawpaw/WIA-udang.git
cd WIA-udang

# 2. Install dependency 
composer install 
npm install

# 3. Salin file environment dan generate app key cp .env.example .env php artisan key:generate 

# 4. Jalankan project (gunakan 2 terminal) php artisan serve 
npm run dev 
``` 

Buka `http://127.0.0.1:8000` di browser.

---

Progress Saat Ini
Landing page + halaman login

---

Status
Project masih dalam tahap pengembangan (First Draft Submission, MIA 2026).
