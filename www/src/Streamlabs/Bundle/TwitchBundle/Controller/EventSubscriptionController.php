<?php

namespace Streamlabs\Bundle\TwitchBundle\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Streamlabs\Bundle\TwitchBundle\Forms\AddFavoriteStreamerType;
use Streamlabs\Entities\Users;
use Streamlabs\Entities\UserToStreamer;
use Streamlabs\Provider\TwitchApiDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Exception\InvalidResourceException;

class EventSubscriptionController extends Controller
{
    /**
     * @Route("/twitch/subscribe", name="twitch_subscribe")
     * @Method("GET")
     * @Template("TwitchBundle:EventSubscription:subscribeEvent.html.twig")
     */
    public function subscribeEventAction(Request $request)
    {
        try {
//            $client = new Client();
//            $response = $client->post('https://api.twitch.tv/helix/webhooks/hub', array(
//                RequestOptions::JSON => array(
//                    'hub.callback' => 'http://localhost:5050/twitch/subscribe/webhook',
//                    'hub.mode' => 'subscribe',
//                    'hub.topic' => 'https://api.twitch.tv/helix/streams?user_id=71672341',
//                    'hub.lease_seconds' => 60,
//                ),
//                'headers' => [
//                    'client-id' => getenv('TWITCH_CLIENT_ID'),
//                    'content-type' => 'application/json'
//                ]
//            ));
//
//            $jsonResponse = $response->getBody()->getContents();
//            $arrResponse = json_decode($jsonResponse, true);
//            $arrResponse = $arrResponse['data'];
//            var_dump($jsonResponse);
//            if (count($arrResponse) > 0) {
//                return $arrResponse[0];
//            }

            $data = array(
                'hub.callback' => 'http://localhost:5050/twitch/subscribe/webhook',
                'hub.mode' => 'subscribe',
                'hub.topic' => 'https://api.twitch.tv/helix/streams?user_id=179065918',
                'hub.lease_seconds' => 120,
            );
            $dataString = json_encode($data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.twitch.tv/helix/webhooks/hub");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Client-ID: ' . getenv('TWITCH_CLIENT_ID'),
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString)
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $server_output = curl_exec($ch);
            $info = curl_getinfo($ch);

            curl_close($ch);

            echo '<pre>';
            print_r($server_output);
            print_r($info);
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    /**
     * @Route("/twitch/subscribe/webhook", name="twitch_subscribe_webhook")
     * @Method("GET")
     * @Template("TwitchBundle:EventSubscription:subscribeWebhook.html.twig")
     */
    public function subscribeWebhookAction(Request $request)
    {
        echo $this->get('kernel')->getRootDir();
        file_put_contents($this->get('kernel')->getRootDir() . '/request.txt', $request->get('hub_challenge') . "\n", FILE_APPEND);
        file_put_contents($this->get('kernel')->getRootDir() . '/request.txt', $request->get('hub.challenge') . "\n", FILE_APPEND);
        $request_body = file_get_contents('php://input');
        file_put_contents($this->get('kernel')->getRootDir() . '/json.txt', $request_body . "\n", FILE_APPEND);
    }
}
