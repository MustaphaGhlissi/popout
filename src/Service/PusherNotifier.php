<?php
/**
 * Created by PhpStorm.
 * User: Hp
 * Date: 28/03/2018
 * Time: 12:18
 */

namespace App\Service;


use Pusher\Pusher;

class PusherNotifier
{
    private $appId = "499727";
    private $appSecret = "adcda4ee74630b8fce7d";
    private $appKey = "d80d0591430d6d277bb2";
    private $pusher;
    private $options = array(
        'cluster' => 'eu',
        'encrypted' => true
    );

    public function __construct(bool $prod, string $appKey, string $appId, string $appSecret,array $options = null)
    {
        if($prod)
        {
            $this->appId = $appId;
            $this->appKey = $appKey;
            $this->appSecret = $appSecret;
        }
        if($options)
        {
            $this->options = $options;
        }
        $this->pusher = new Pusher($this->appKey,$this->appSecret,$this->appId, $this->options);
    }

    public function notify(string $channelName, string $eventName, array $data)
    {
        $this->pusher->trigger($channelName, $eventName, $data);
    }
}