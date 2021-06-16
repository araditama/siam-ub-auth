# SIAM UB Authentication

Authentifikasi menggunakan akun SIAM UB pada repo ini menggunakan metode scraping untuk mendapatkan informasi dari web [SIAM UB](https://siam.ub.ac.id) dan memberikan respon berupa JSON.
Untuk melihat informasi lebih lanjut tentang web scraper dan interface yang digunakan dapat dilihat pada [@FriendsOfPHP/Goutte](https://github.com/FriendsOfPHP/Goutte).

## Installation

Menambahkan ``siam-ub-auth`` sebagai dependency pada file composer.json :

```
composer require araditama/siam-ub-auth
```

## Usage

Membuat instansiasi AuthSIAM :
```php
use Araditama\AuthSIAM\AuthSIAM;

$auth = new AuthSIAM;
```

Melakukan request authentifikasi dengan menggunakan method ``auth()`` dengan masukkan parameter berupa array :
```php
// contoh array dari credentials yang akan diproses
$data = [
  'nim' => '15515020xxxxxx',
  'password' => 'secret'
];

// memanggil method auth dari objek yang telah dibuat dengan method GET
$result = $auth->auth($data);
```

Melakukan authentifikasi dengan result daftar jadwal kuliah juga dapat dilakukan dengan menggunakan method ``authWithSchedule()``.
Melakukan authentifikasi dengan result Kartu Hasil Studi juga dapat dilakukan dengan menggunakan method ``authWithGpa()``.

## Result
Jika NIM dan password benar menggunakan method ``auth()``:
```json
{
  "data": {
    "nim": "15515020xxxx",
    "nama": "Lorem Ipsum",
    "fakultas": "Ilmu Komputer",
    "jurusan": "Teknik Informatika",
    "prodi": "Teknik Informatika"
  },
  "msg": "success",
  "token": "UpKefb2As1eIRJm7bGKhjlX6K84JZI"
}
```

Jika NIM dan password benar menggunakan method ``authWithSchedule()``:
```json
{
  "data": {
    "nim": "15515020xxxx",
    "nama": "Lorem Ipsum",
    "fakultas": "Ilmu Komputer",
    "jurusan": "Teknik Informatika",
    "prodi": "Teknik Informatika",
    "jadwal":[
      {
        "hari": "Senin",
        "jam": "10:20 - 11:59",
        "kelas": "B",
        "kode": "CIT62004",
        "matkul": "Pengantar Sistem Operasi",
        "thn_kurikulum": "2020",
        "dosen": "Wibisono Sukmo Wardhono, M.T.",
        "ruang": "Gedung E PTIIK - E2.8",
      },
      {
        "hari": "Senin",
        "jam": "12:50 - 14:29",
        "kelas": "B",
        "kode": "CIT62002",
        "matkul": "Pemrograman Lanjut",
        "thn_kurikulum": "2020",
        "dosen": "Bayu Rahayudi, ST., MT.",
        "ruang": "Lab Pembelajaran Gedung G - G1.3",
      }
    ],
    "token": "UpKefb2As1eIRJm7bGKhjlX6K84JZI"
  },
  "msg": "success",
}
```

Jika NIM dan password benar menggunakan method ``authWithGpa()``:
```json
{
  "data": {
    "nim": "15515020xxxx",
    "nama": "Lorem Ipsum",
    "fakultas": "Ilmu Komputer",
    "jurusan": "Teknik Informatika",
    "prodi": "Teknik Informatika",
    "gpa":[
      {
        "no": "1",
        "kode_matkul": "MPK60007",
        "matkul": "Bahasa Indonesia ",
        "sks": "2",
        "nilai": "A",
        "detail_nilai": [
          {
            "no": "1",
            "tipe_nilai": "Absensi",
            "nilai_ke": "1",
            "nilai": "80",
          },
          {
            "no": "2",
            "tipe_nilai": "Quiz",
            "nilai_ke": "2",
            "nilai": "82",
          }
        ]
      }
    ],
    "token": "UpKefb2As1eIRJm7bGKhjlX6K84JZI"
  },
  "msg": "success",
}
```

Jika NIM atau passowd salah :
```json
{
  "msg": "NIM atau password salah"
}
```

Jika parameter input tidak valid :
```json
{
  "msg": "Invalid."
}
```

## Disclaimer
Proses authentifikasi ini menggunakan metode scraping, artinya semua data dan aset tetap milik Universitas Brawijaya.
Penyalahgunaan dari penggunaan library ini oleh pihak pengembang lain menjadi tanggungjawab pihak tersebut.

## License
Read [MIT License](LICENSE)
