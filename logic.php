<?php

    define('DEFAULT_THUMBNAIL', 'images/default_banner.jpg');
    $startupChannel = null;
    $api = NULL;

    if(isset($_GET['q']) && $_GET['q'] != '/') {
        $startupChannel = substr($_GET['q'], 1);
    } elseif(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '/') {
        $startupChannel = substr($_SERVER['REQUEST_URI'], 1);
    }

    function getTwitchStream(&$broadcaster) {
        global $api;

        if(!$api)
            $api = curl_init();

       if ($api) {
           curl_setopt($api, CURLOPT_HTTPGET, true);
           curl_setopt($api, CURLOPT_URL, "https://api.twitch.tv/kraken/streams/{$broadcaster['twitch']}");
           curl_setopt($api, CURLOPT_TIMEOUT, 5);
           curl_setopt($api, CURLOPT_HEADER, false);
           curl_setopt($api, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($api, CURLOPT_FOLLOWLOCATION, true);
           curl_setopt($api, CURLOPT_SSL_VERIFYPEER, false);
           if ($raw = curl_exec($api)) {
               if ($resp = json_decode($raw)) {
                    if(isset($resp->_links)) {
                        if(isset($resp->stream)) {
                            $broadcaster['online'] = true;
                            $broadcaster['thumbnail'] = $resp->stream->preview;
                            $broadcaster['game'] = $resp->stream->game;
                            $broadcaster['topic'] = $resp->stream->channel->status;
                            return true;
                        }
                    }
               }
           }
       }

       $broadcaster['online'] = false;
       return false;
    }

    function getTwitchChannel(&$broadcaster) {
        global $api;

        if(!$api)
            $api = curl_init();

       if ($api) {
           curl_setopt($api, CURLOPT_HTTPGET, true);
           curl_setopt($api, CURLOPT_URL, "https://api.twitch.tv/kraken/channels/{$broadcaster['twitch']}");
           curl_setopt($api, CURLOPT_TIMEOUT, 5);
           curl_setopt($api, CURLOPT_HEADER, false);
           curl_setopt($api, CURLOPT_RETURNTRANSFER, true);
           curl_setopt($api, CURLOPT_FOLLOWLOCATION, true);
           curl_setopt($api, CURLOPT_SSL_VERIFYPEER, false);
           if ($raw = curl_exec($api)) {
               if ($resp = json_decode($raw)) {
                    if(isset($resp->_id)) {
                        if(isset($resp->video_banner)) {
                            $broadcaster['thumbnail'] = $resp->video_banner;
                            $broadcaster['game'] = $resp->game;
                            $broadcaster['topic'] = $resp->status;
                            return true;
                        }
                    }
               }
           }
       }

       $broadcaster['thumbnail'] = DEFAULT_THUMBNAIL;
       $broadcaster['game'] = null;
       $broadcaster['topic'] = '';
       return false;
    }

    function hasCache(&$broadcaster) {
        if(file_exists("./cache/{$broadcaster['twitch']}")) {
            if(time() - filemtime("./cache/{$broadcaster['twitch']}") <= 300) { // caches expire every 5 minutes
                $broadcaster = unserialize(file_get_contents("./cache/{$broadcaster['twitch']}"));
                return true;
            }
        }

        return false;
    }

    function updateCache(&$broadcaster) {
        file_put_contents("./cache/{$broadcaster['twitch']}", serialize($broadcaster));
    }

    $broadcastersOnline = array();
    $broadcastersOffline = array();

    $broadcasters = array(
        array(
            'twitch' => 'aureusknightstv',
            'twitter' => 'aureusknights',
            'name' => 'Aureus Knights TV',
        ),
        array(
            'twitch' => 'okaria',
            'twitter' => 'okaria',
            'name' => 'Okaria Dragon'
        ),
        array(
            'twitch' => 'gagtech',
            'twitter' => 'Gagtech',
            'name' => 'Gagtech'
        ),
        array(
            'twitch' => 'chux81',
            'twitter' => '',
            'name' => 'Chux'
        ),
        array(
            'twitch' => 'ryath',
            'twitter' => '',
            'name' => 'Ryath'
        )
    );

    foreach($broadcasters as &$broadcaster) {
        if(!$broadcaster) continue;

        if(!hasCache($broadcaster)) {
            if(!getTwitchStream($broadcaster)) {
                getTwitchChannel($broadcaster);
            }

            updateCache($broadcaster);
        }

        if($broadcaster['online']) {
            $broadcastersOnline[] = $broadcaster;

            if($startupChannel == null) {
                $startupChannel = (object)$broadcaster;
            }
        } else {
            $broadcastersOffline[] = $broadcaster;
        }

        if(is_string($startupChannel) && $startupChannel == $broadcaster['twitch']) {
            $startupChannel = (object)$broadcaster;
        }
    }

    if(!is_object($startupChannel)) {
        if(isset($broadcastersOnline[0])) {
            $startupChannel = (object)$broadcastersOnline[0];
        } else {
            $startupChannel = (object)$broadcastersOffline[0];
        }
    }

    $broadcasters = array_merge($broadcastersOnline, $broadcastersOffline);
    foreach($broadcasters as &$broadcaster) {
        $broadcaster = (object)$broadcaster;
    }

    unset($broadcastersOnline);
    unset($broadcastersOffline);
