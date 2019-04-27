<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// |  liuguoge <liuguogen_vip@163.com>
// +----------------------------------------------------------------------
namespace api\v1\admin;

use \think\Cache;
class userMake  {

    public static $expire = '2592000';//60*60*24*30

    public static $systemToken = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        

    static public function make($adminId, $data)
    {

        $userKey = md5(self::$systemToken.$adminId);
        $randomId = self::str_random(32);
        $token = md5(sha1($adminId.implode('', $data).$randomId)).$userKey;


        $value = json_encode(['id'=>$adminId]);
        Cache::set($userKey,$value,time()+self::$expire);
        return $token;
    }

   static public  function check($token)
    {
        $userKey = substr($token, 32, 64);

        if( !$userKey )
        {
            return false;
        }

        $userData =  Cache::get($userKey);
        if( ! $userData )
        {
            return false;
        }

        $data = json_decode($userData, true);
        if( !$data  )
        {
            Cache::rm($userKey);
            return false;
        }
        else
        {
            $value = json_encode(['id'=>$data['id']]);
            Cache::set($userKey,$value,time()+self::$expire);
        }
        return $data['id'];
    }

    static public function delete($token)
    {

        $userKey = substr($token, 32, 64);
        return Cache::rm($userKey);
    }

    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param  int  $length
     * @return string
     *
     * @throws \RuntimeException
     */
   static private  function str_random($length = 16)
    {
        if (function_exists('openssl_random_pseudo_bytes'))
        {
            $bytes = openssl_random_pseudo_bytes($length * 2);

            if ($bytes === false)
            {
                throw new \RuntimeException('Unable to generate random string.');
            }

            return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
        }
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
    
}


