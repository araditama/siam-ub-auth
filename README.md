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
        "hari":"Sabtu",
        "jam":"07:00 - 09:00",
        "kode":"UBU4001",
        "matkul":"Skripsi",
        "ruang":"Gedung F FILKOM - F2.7"
      },
      {
        "hari":"Sabtu",
        "jam":"10:00 - 12:00",
        "kode":"UBU4002",
        "matkul":"Praktek Kerja Lapangan",
        "ruang":"Gedung F FILKOM - F2.3"
      }
    ]
  },
  "msg": "success",
  "token": "UpKefb2As1eIRJm7bGKhjlX6K84JZI"
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
