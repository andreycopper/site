<?php
namespace Controllers;

use Entity\File;
use Entity\Message;
use Entity;
use Entity\User;
use System\Crypt;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Tests extends Controller
{
    protected function actionDefault()
    {
    }

    protected function actionGenerate()
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);// Только чтение данных

        $spreadsheet = $reader->load(DIR_FILES . DIRECTORY_SEPARATOR . 'names.xlsx');

        $sheetsCount = $spreadsheet->getSheetCount();// Количество листов
        $data0 = $spreadsheet->getSheet(0)->toArray();
        $data1 = $spreadsheet->getSheet(1)->toArray();

        $res = [];
        $names = [];
        $index = 201;
        $user = new User();
        $crypt = new Crypt();

        foreach ($data0 as $item0):
            $res[] = $item0;
            $names[] = $item0[0];
        endforeach;

        foreach ($data1 as $item1):
            $res[] = $item1;
            $names[] = $item1[0];
        endforeach;

        file_put_contents(
            DIR_FILES . DIRECTORY_SEPARATOR . 'names.json',
            json_encode($names, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT)
        );

        shuffle($res);
        //var_dump($res);die;
        var_dump($names);die;

//        foreach ($res as $item) {
//            $crypt->generatePair()->save($index);
//            //$crypt->load($index);
//
//            $user->id = $index;
//            $user->login = mb_strtolower($item[0]);
//            $user->password = password_hash('5Rfklbyf&*', PASSWORD_DEFAULT);
//            $user->genderId = intval($item[1]);
//            $user->publicKey = $crypt->getPublicKey();
//            $user->privateKey = $crypt->getPrivateKey();
//            $user->name = $crypt->encryptByPrivateKey($item[0]);
//            $user->email = $crypt->encryptByPrivateKey(mb_strtolower($item[0]) . '@gmail.com');
//            $user->save();
//
//            var_dump($user);
//            var_dump($crypt->decryptByPublicKey($user->name));
//            var_dump($crypt->decryptByPublicKey($user->email));
//
//            $index++;
//        }
    }

    protected function actionCrypt()
    {
        $user = User::factory(['id' => 352]);
        $crypt = (new Crypt())->load($user->getId());

        var_dump($crypt->decryptByPublicKey($user->getName()));
        var_dump($crypt->decryptByPublicKey($user->getEmail()));
        var_dump($user);
    }
}
