# Model Data Berbasis RDF/XML pada Pariwisata Kota Yogyakarta

<p align="center">
  <img src="https://img.shields.io/badge/Web%20Semantik-UTS-5D4037?style=for-the-badge" alt="UTS Web Semantik">
  <img src="https://img.shields.io/badge/Data-RDF%2FXML-8A6D3B?style=for-the-badge" alt="RDF/XML">
  <img src="https://img.shields.io/badge/Backend-PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
</p>

<p align="center">
  <strong>Implementasi Web Semantik untuk Representasi dan Penyajian Data Pariwisata Kota Yogyakarta</strong>
</p>

---

## Informasi Proyek

| Informasi          | Keterangan                                |
| :----------------- | :---------------------------------------- |
| **Nama**           | Muhammad Ihsan Anwar                      |
| **NIM**            | 251402044                                 |
| **Mata Kuliah**    | Web Semantik                              |
| **Kelas**          | B                                         |
| **Dosen Pengampu** | Annisa Fadhillah Pulungan, S.Kom., M.Kom. |
| **Jenis Proyek**   | Ujian Tengah Semester (UTS)               |

## Tentang Proyek

Proyek ini mengimplementasikan konsep **Web Semantik** dalam pemodelan data pariwisata Kota Yogyakarta menggunakan _Resource Description Framework_ (RDF) dengan format serialisasi RDF/XML.

Data pariwisata direpresentasikan sebagai kumpulan _resource_ yang saling terhubung melalui relasi semantik. Setiap objek wisata memiliki informasi terstruktur, seperti lokasi, kategori, pengelola, fasilitas, jam operasional, dan deskripsi.

Data RDF/XML kemudian diproses menggunakan PHP dan ditampilkan melalui website sehingga informasi pariwisata dapat diakses secara terstruktur berdasarkan URI masing-masing resource.

## Tujuan

- Menerapkan konsep Web Semantik dalam pemodelan data pariwisata.
- Mengidentifikasi resource utama dan resource pendukung beserta URI-nya.
- Merepresentasikan hubungan antarresource menggunakan RDF Triple.
- Menyusun dan menyimpan representasi data dalam format RDF/XML.
- Mengembangkan website yang membaca dan menampilkan informasi berdasarkan data RDF.
- Mendukung akses resource melalui URI secara langsung.

## Cakupan Data Pariwisata

Proyek ini mencakup tujuh objek wisata di Kota Yogyakarta dan sekitarnya.

| No. | Objek Wisata            | Informasi yang Dimodelkan                             |
| :-: | :---------------------- | :---------------------------------------------------- |
|  1  | Malioboro               | Lokasi, kategori, pengelola, fasilitas, dan |
|  2  | Keraton Yogyakarta      | Lokasi, kategori, pengelola, fasilitas, dan |
|  3  | Taman Sari              | Lokasi, kategori, pengelola, fasilitas, dan |
|  4  | Alun-Alun Kidul         | Lokasi, kategori, pengelola, fasilitas, dan |
|  5  | Museum Sonobudoyo       | Lokasi, kategori, pengelola, fasilitas, dan |
|  6  | Gembira Loka Zoo        | Lokasi, kategori, pengelola, fasilitas, dan |
|  7  | Taman Pintar Yogyakarta | Lokasi, kategori, pengelola, fasilitas, dan |

_Catatan: Cakupan administratif dan nilai atribut setiap objek mengikuti data yang didefinisikan dalam berkas RDF._

## Teknologi yang Digunakan

<p>
  <img src="https://img.shields.io/badge/RDF-Resource%20Model-5D4037?style=flat-square" alt="RDF">
  <img src="https://img.shields.io/badge/RDF%2FXML-Data%20Serialization-8A6D3B?style=flat-square" alt="RDF/XML">
  <img src="https://img.shields.io/badge/PHP-Data%20Processing-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/HTML-Page%20Structure-E34F26?style=flat-square&logo=html5&logoColor=white" alt="HTML">
  <img src="https://img.shields.io/badge/CSS-Interface%20Styling-1572B6?style=flat-square&logo=css3&logoColor=white" alt="CSS">
</p>

| Teknologi   | Peran                                                                  |
| :---------- | :--------------------------------------------------------------------- |
| **RDF**     | Merepresentasikan resource dan hubungan antardata dalam bentuk triple. |
| **RDF/XML** | Menyimpan dan menyerialisasikan data RDF menggunakan sintaks XML.      |
| **PHP**     | Membaca, memproses, dan menyajikan data RDF/XML pada website.          |
| **HTML**    | Membentuk struktur halaman website.                                    |
| **CSS**     | Mengatur tata letak, tipografi, warna, dan tampilan antarmuka.         |

## Konsep Pemodelan RDF

Data dimodelkan menggunakan struktur **RDF Triple**, yang terdiri atas tiga komponen utama:

- **Subject** — resource yang sedang dideskripsikan.
- **Predicate** — properti atau hubungan yang menghubungkan resource dengan objeknya.
- **Object** — resource atau nilai yang menjadi tujuan hubungan.

Secara konseptual, struktur tersebut dapat direpresentasikan sebagai berikut:

```text
Subject     : Malioboro
Predicate   : memilikiKategori
Object      : WisataBelanja
```

Contoh tersebut hanya menggambarkan konsep triple. Nama resource, properti, dan nilainya harus disesuaikan dengan definisi aktual dalam berkas RDF/XML proyek.

## Struktur Direktori

```text
.
├── index.php
├── style.css
├── Pariwisata_Kota_Yogyakarta.rdf
└── README.md
```

| Berkas                           | Fungsi                                                                                  |
| :------------------------------- | :-------------------------------------------------------------------------------------- |
| `index.php`                      | Memproses URI yang diminta, membaca data RDF/XML, dan menampilkan informasi pariwisata. |
| `style.css`                      | Mengatur desain visual dan tata letak website.                                          |
| `Pariwisata_Kota_Yogyakarta.rdf` | Menyimpan data pariwisata dalam format RDF/XML.                                         |
| `README.md`                      | Menyediakan dokumentasi proyek, teknologi, struktur data, dan mekanisme akses.          |

## Akses Website dan URI Resource

Website proyek dapat diakses melalui:

**[wisatayogyakarta.neoverse.my.id](https://wisatayogyakarta.neoverse.my.id/)**

Setiap resource memiliki URI yang digunakan sebagai identitas unik dalam representasi data semantik. Contoh URI resource pengelola:

https://wisatayogyakarta.neoverse.my.id/pengelola/pemerintah-kota-yogyakarta

### Mekanisme Resolusi URI

Akses resource dilakukan melalui mekanisme berikut:

1. Pengguna membuka URI resource melalui browser.
2. Konfigurasi `.htaccess` meneruskan permintaan ke `index.php`.
3. Aplikasi mengidentifikasi URI yang diminta.
4. Aplikasi mencocokkan URI dengan resource yang terdapat dalam data RDF/XML.
5. Informasi resource ditampilkan pada halaman website.

Parameter `?uri=` juga didukung sebagai alternatif akses (_fallback_), sehingga URI resource dapat diproses melalui mekanisme tersebut apabila diperlukan.

## Fitur Utama

- **Pemodelan Data Semantik** — data pariwisata direpresentasikan menggunakan RDF.
- **Serialisasi RDF/XML** — data disimpan dalam format terstruktur berbasis XML.
- **Relasi Antarresource** — informasi dihubungkan melalui properti RDF.
- **Akses Berbasis URI** — resource dapat diidentifikasi dan diminta melalui URI.
- **Integrasi Website** — data RDF/XML diproses dengan PHP dan disajikan melalui antarmuka web.

---

<p align="center">
  <strong>UTS Web Semantik · Kelas B</strong><br>
  <sub>Model Data Berbasis RDF/XML pada Pariwisata Kota Yogyakarta</sub>
</p>
