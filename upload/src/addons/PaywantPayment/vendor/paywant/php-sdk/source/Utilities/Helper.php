<?php

namespace Paywant\Utilities;

class Helper
{
    public static function getIPAddress()
    {
        if (getenv('HTTP_CLIENT_IP'))
        {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        elseif (getenv('HTTP_X_FORWARDED_FOR'))
        {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
            if (strstr($ip, ','))
            {
                $tmp = explode(',', $ip);
                $ip = trim($tmp[0]);
            }
        }
        else
        {
            $ip = getenv('REMOTE_ADDR');
        }

        return $ip;
    }

    public static function getPort()
    {
        return getenv('REMOTE_PORT');
    }
}
