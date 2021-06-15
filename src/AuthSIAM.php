<?php

namespace Araditama\AuthSIAM;

use Goutte\Client;
use Illuminate\Support\Str;

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

            $token = Str::random(32);
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
    public function authWithGpa($credentials)
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

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/khs.php');

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
            $dataDetail = $cr->filter('input[class="thickbox"]')->each(function ($result) {
                return $result->attr('alt');
            });

            $gpa = [];
            for ($i = 0; $i < sizeof($dataDetail); $i++) {
                $result = explode(" ", $data[$i]);

                $index = $result[0];
                $kodeMatkul = $result[1];
                $matkul = "";
                for ($j = 3; $j < sizeof($result) - 2; $j++) {
                    $matkul .= $result[$j] . " ";
                }
                $sks = $result[sizeof($result) - 2];
                $nilai = $result[sizeof($result) - 1];

                $cr = $cl->request('GET', 'https://siam.ub.ac.id/' . $dataDetail[$i]);
                $dataGpa = $cr->filter('tr')->each(function ($result) {
                    return $result->text();
                });
                $detailGpa = [];
                for ($z = 0; $z < sizeof($dataGpa) - 1; $z++) {
                    $tempDetailGpa = explode(" ", $dataGpa[$z + 1]);
                    $tipe_nilai = $tempDetailGpa[1];
                    for ($k = 2; $k < sizeof($tempDetailGpa) - 2; $k++) {
                        $tipe_nilai .= " " . $tempDetailGpa[$k];
                    }
                    $detailGpa[$z] = [
                        'no' => $tempDetailGpa[0],
                        'tipe_nilai' => $tipe_nilai,
                        'nilai_ke' => $tempDetailGpa[sizeof($tempDetailGpa) - 2],
                        'nilai' => $tempDetailGpa[sizeof($tempDetailGpa) - 1]
                    ];
                }

                $gpa[$i] = [
                    'no' => $index,
                    'kode_matkul' => $kodeMatkul,
                    'matkul' => $matkul,
                    'sks' => $sks,
                    'nilai' => $nilai,
                    'detail_nilai' => $detailGpa
                ];
            }

            $token = Str::random(32);
            $response = [
                'data' => [
                    'nim' => $dataDiri[0],
                    'nama' => $dataDiri[1],
                    'fakultas' => substr($dataDiri[2], 19),
                    'jurusan' => substr($dataDiri[3], 7),
                    'prodi' => substr($dataDiri[4], 13),
                    'gpa' => $gpa,
                    'token' => $token
                ],
                'msg' => 'success'
            ];
            // dd($response);
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

            $jadwal = [];
            for ($i = 0; $i < sizeof($data); $i++) {
                $string = htmlentities($data[$i], 0, 'utf-8');
                $result = str_replace("&nbsp;", "", $string);
                $result = str_replace("  ", " ", $result);
                $result = explode(" ", $result);
                // dd($result);
                $hari = $result[0];
                $jam = $result[1] . " - " . $result[3];
                $kelas = $result[4];
                $kodeMatkul = $result[5];
                $matkul = $result[6];
                $indexTemp = 7;
                while (!str_contains($result[$indexTemp], '20')) {
                    $matkul .= " " . $result[$indexTemp];
                    $indexTemp++;
                }
                $tahunKurikulum = $result[$indexTemp++];
                $dosen = $result[$indexTemp++];
                while (!str_contains($result[$indexTemp], "Gedung") && !str_contains($result[$indexTemp], "Lab")) {
                    $dosen .= " " . $result[$indexTemp];
                    $indexTemp++;
                }
                $ruang = $result[$indexTemp++];
                for ($j = $indexTemp; $j < sizeOf($result); $j++) {
                    $ruang .= " " . $result[$j];
                }

                $jadwal[$i] = [
                    'hari' => $hari,
                    'jam' => $jam,
                    'kelas' => $kelas,
                    'kode' => $kodeMatkul,
                    'matkul' => $matkul,
                    'thn_kurikulum' => $tahunKurikulum,
                    'dosen' => $dosen,
                    'ruang' => $ruang
                ];
            }

            $token = Str::random(32);
            $response = [
                'data' => [
                    'nim' => $dataDiri[0],
                    'nama' => $dataDiri[1],
                    'fakultas' => substr($dataDiri[2], 19),
                    'jurusan' => substr($dataDiri[3], 7),
                    'prodi' => substr($dataDiri[4], 13),
                    'jadwal' => $jadwal,
                    'token' => $token
                ],
                'msg' => 'success'
            ];
            dd($response);
            return response()->json($response, 200);
        }
    }
}
