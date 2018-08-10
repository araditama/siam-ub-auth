<?php

namespace Araditama\AuthSIAM;

use Goutte\Client;

class AuthSIAM
{
    public function auth($credentials)
    {
        if(!isset($credentials['nim']) || !isset($credentials['password'])){
            $response = [
                'msg' => 'Invalid.'
            ];
            return response()->json($response, 400);
        }

        $cl = new Client();

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/');
        $form = $cr->selectButton('Masuk')->form();
        $cr = $cl->submit($form, array('username' => $credentials['nim'], 'password' => $credentials['password']));

        $cr = $cl->request('GET', 'https://siam.ub.ac.id/krs.php');
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

            $token = str_random(30);
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
}
