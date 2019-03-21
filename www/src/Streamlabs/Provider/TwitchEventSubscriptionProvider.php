<?php

namespace Streamlabs\Provider;


use GuzzleHttp\Client;

class TwitchEventSubscriptionProvider
{

    /**
     * @param string $callbackUrl
     * @param string $topicUrl
     * @param int $leaseSecond
     * @return array('statusCode', 'reasonPhrase')
     */
    public static function subscribeToEvent($callbackUrl = '', $topicUrl = '', $leaseSecond = 0)
    {
        try {
            $twitchWebhook = "https://api.twitch.tv/helix/webhooks/hub";
            $data = array(
                'hub.callback' => $callbackUrl,
                'hub.mode' => 'subscribe',
                'hub.topic' => $topicUrl,
                'hub.lease_seconds' => $leaseSecond,
            );
            $dataString = json_encode($data);

            $client = new Client();
            $request = $client->post($twitchWebhook, array(
                'headers' => array(
                    'Client-ID' => getenv('TWITCH_CLIENT_ID'),
                    'content-type' => 'application/json',
                    'content-length' => strlen($dataString),
                ),
                'json' => $data
            ));

            $response = array(
                'statusCode' => $request->getStatusCode(),
                'reasonPhrase' => $request->getReasonPhrase(),
            );

            return $response;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @param string $callbackUrl
     * @param string $topicUrl
     * @param int $leaseSecond
     * @return array('statusCode', 'reasonPhrase')
     */
    public static function unsubscribeToEvent($callbackUrl = '', $topicUrl = '')
    {
        try {
            $twitchWebhook = "https://api.twitch.tv/helix/webhooks/hub";
            $data = array(
                'hub.callback' => $callbackUrl,
                'hub.mode' => 'unsubscribe',
                'hub.topic' => $topicUrl,
                'hub.lease_seconds' => 0,
            );
            $dataString = json_encode($data);

            $client = new Client();
            $request = $client->post($twitchWebhook, array(
                'headers' => array(
                    'Client-ID' => getenv('TWITCH_CLIENT_ID'),
                    'content-type' => 'application/json',
                    'content-length' => strlen($dataString),
                ),
                'json' => $data
            ));

            $response = array(
                'statusCode' => $request->getStatusCode(),
                'reasonPhrase' => $request->getReasonPhrase(),
            );

            return $response;
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}