CREATE DATABASE IF NOT EXISTS rak_buku;
USE rak_buku;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  PASSWORD VARCHAR(255),
  ROLE ENUM('admin', 'user') NOT NULL
);

INSERT INTO users (username, PASSWORD, ROLE) VALUES
('admin', '$2y$10$TrkQc/BoTxITVsJu72djPu5huVwKFnX.BpMRTT9pPTMo.4PU1mwtS', 'admin');

INSERT INTO users (username, PASSWORD, ROLE) VALUES
('user', '$2y$10$2x2nNht9xZMb8n4duiNDEAustdbRmJdsMT377oFmKAFORduEFWT1.', 'user');

CREATE TABLE IF NOT EXISTS buku (
  id INT AUTO_INCREMENT PRIMARY KEY,
  judul VARCHAR(100),
  penulis VARCHAR(100),
  tahun_terbit YEAR,
  kategori VARCHAR(50),
  sinopsis TEXT,
  stok INT,
  cover VARCHAR(100) DEFAULT 'default.png'
);

INSERT INTO buku (judul, penulis, tahun_terbit, kategori, sinopsis, stok, cover) VALUES
('Laskar Pelangi', 'Andrea Hirata', 2005, 'Novel', 'Kisah perjuangan anak-anak di Belitung dalam meraih pendidikan dan mimpi.', 10, 'Laskar Pelangi.jpeg'),
('Bumi Manusia', 'Pramoedya Ananta Toer', 1980, 'Sastra', 'Novel sejarah yang mengangkat kehidupan Minke dan ketidakadilan pada masa kolonial.', 8, 'Bumi Manusia.jpeg'),
('Atomic Habits', 'James Clear', 2018, 'Pengembangan Diri', 'Panduan membangun kebiasaan kecil yang berdampak besar dalam hidup sehari-hari.', 12, 'Atomic Habits.jpeg'),
('Rich Dad Poor Dad', 'Robert Kiyosaki', 1997, 'Keuangan', 'Buku populer tentang pola pikir finansial, aset, dan pentingnya literasi keuangan.', 7, 'Rich Dad Poor Dad.jpeg'),
('Filosofi Teras', 'Henry Manampiring', 2018, 'Motivasi', 'Pengenalan stoikisme untuk membantu menghadapi masalah dengan lebih tenang dan rasional.', 9, 'Filosofi Teras.jpeg');

CREATE TABLE IF NOT EXISTS rating (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_buku INT NOT NULL,
    rating INT NOT NULL,   
    komentar TEXT,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (id_user, id_buku),
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (id_buku) REFERENCES buku(id) ON DELETE CASCADE
);