<?php

namespace Araditama\AuthSIAM;

use Goutte\Client;

class AuthSIAM
{
    public function auth($credentials)
    {
        if (!isset($credentials['nim']) || !isset($credentials['password'])) {
            $response = [
                'msg' => 'Invalid.'
            ];
            return response()->json($response, 400);
        }

        $cl = new Client();

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/');
        $form = $cr->selectButton('Masuk')->form();
        $cr = $cl->submit($form, array('username' => $credentials['nim'], 'password' => $credentials['password']));

        $cek = $cr->filter('small.error-code')->each(function ($result) {
            return $result->text();
        });

        if (isset($cek[0])) {
            $response = [
            'msg' => 'NIM atau password salah'
         ];
            return response()->json($response, 200);
        } else {
            $data = $cr->filter('div[class="bio-info"] > div')->each(function ($result) {
                return $result->text();
            });

            $token = str_random(32);
            $response = [
            'data' => [
                'nim' => $data[0],
                'nama' => $data[1],
                'fakultas' => substr($data[2], 19),
                'jurusan' => substr($data[3], 7),
                'prodi' => substr($data[4], 13)
            ],
            'msg' => 'success',
            'token' => $token
         ];
            return response()->json($response, 200);
        }
    }
    public function authWithSchedule($credentials)
    {
        if (!isset($credentials['nim']) || !isset($credentials['password'])) {
            $response = [
                'msg' => 'Invalid.'
            ];
            return response()->json($response, 400);
        }

        $cl = new Client();

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/');
        $form = $cr->selectButton('Masuk')->form();
        $cr = $cl->submit($form, array('username' => $credentials['nim'], 'password' => $credentials['password']));

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/class.php');

        $cek = $cr->filter('small.error-code')->each(function ($result) {
            return $result->text();
        });

        if (isset($cek[0])) {
            $response = [
                    'msg' => 'usrname or passwd salah'
                ];
            return response()->json($response, 200);
        } else {
            $dataDiri = $cr->filter('div[class="bio-info"] > div')->each(function ($result) {
                return $result->text();
            });
            $data = $cr->filter('tr[class="text"]')->each(function ($result) {
                return $result->text();
            });

            $jadwal;
            for ($i = 0; $i < sizeof($data); $i++) {
                $result = str_replace('        ', '*', $data[$i]);

                $hari = substr($result, 1, strpos($result, '*', 1)-1);

                $jam = substr($result, strpos($result, '*', 1));
                $hasilJam = substr($jam, 1, strpos($jam, '*', 1)-1);

                $kodeMatkul = substr($jam, strpos($jam, '*', 1)+2);
                $hasilKode = substr($kodeMatkul, 1, strpos($kodeMatkul, '*', 1)-1);

                $matkul = substr($kodeMatkul, strpos($kodeMatkul, '*', 1)+3);
                $hasilMatkul = substr($matkul, 1, strpos($matkul, '*', 1)-1);

                $ruang = substr($matkul, strpos($matkul, '*', 1)+12);
                $hasilRuang = substr($ruang, 1, strpos($ruang, '*', 1)-1);

                $jadwal[$i] = [
                        'hari' => $hari,
                        'jam' => $hasilJam,
                        'kode' => $hasilKode,
                        'matkul' => $hasilMatkul,
                        'ruang' => $hasilRuang
                    ];
            }

            $token = str_random(32);
            $response = [
                    'data' => [
                        'nim' => $dataDiri[0],
                        'nama' => $dataDiri[1],
                        'fakultas' => substr($dataDiri[2], 19),
                        'jurusan' => substr($dataDiri[3], 7),
                        'prodi' => substr($dataDiri[4], 13),
                        'jadwal' => $jadwal
                    ],
                    'token' => $token,
                    'msg' => 'success'
                ];
            return response()->json($response, 200);
        }
    }
}
