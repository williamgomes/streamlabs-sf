<?php

namespace Streamlabs\Provider;


use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RequestStack;

class TwitchEventSubscriptionProvider
{
    /** @var int
     *This can be set to any higher value
     */
    const WEBHOOK_LEASE_SECONDS = 600;

    /** @var string  */
    const TWITCH_WEBHOOK_URI = "https://api.twitch.tv/helix/webhooks/hub";

    /** @var Router  */
    private $router;

    /** @var RequestStack  */
    private $requestStack;

    /**
     * TwitchEventSubscriptionProvider constructor.
     * @param Router $router
     * @param RequestStack $requestStack
     */
    public function __construct(Router $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * @param string $routeName
     * @param string $topicUrl
     * @return array
     * @throws \Exception
     */
    public function subscribeToEvent($routeName = '', $topicUrl = '')
    {
        try {
            $request = $this->requestStack->getCurrentRequest();
            $callbackUrl = $request->getScheme() . '://' . $request->getHttpHost() . $this->router->getGenerator()->generate($routeName);
            $data = array(
                'hub.callback' => $callbackUrl,
                'hub.mode' => 'subscribe',
                'hub.topic' => $topicUrl,
                'hub.lease_seconds' => self::WEBHOOK_LEASE_SECONDS,
            );
            $dataString = json_encode($data);

            $client = new Client();
            $request = $client->post(self::TWITCH_WEBHOOK_URI, array(
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
            throw $exception;
        }
    }

    /**
     * @param string $routeName
     * @param string $topicUrl
     * @return array
     * @throws \Exception
     */
    public function unsubscribeToEvent($routeName = '', $topicUrl = '')
    {
        try {
            $request = $this->requestStack->getCurrentRequest();
            $callbackUrl = $request->getScheme() . '://' . $request->getHttpHost() . $this->router->getGenerator()->generate($routeName);
            $data = array(
                'hub.callback' => $callbackUrl,
                'hub.mode' => 'unsubscribe',
                'hub.topic' => $topicUrl,
                'hub.lease_seconds' => 0,
            );
            $dataString = json_encode($data);

            $client = new Client();
            $request = $client->post(self::TWITCH_WEBHOOK_URI, array(
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
            throw $exception;
        }
    }
}