<?php

namespace Streamlabs\Bundle\TwitchBundle\Controller;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Streamlabs\Bundle\TwitchBundle\Forms\AddFavoriteStreamerType;
use Streamlabs\Entities\Users;
use Streamlabs\Entities\UserToStreamer;
use Streamlabs\Provider\TwitchApiDataProvider;
use Streamlabs\Provider\TwitchEventSubscriptionProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventSubscriptionController extends Controller
{
    /**
     * @Route("/twitch/webhook/follows", name="twitch_webhook_follows")
     * @Method({"GET","POST"})
     */
    public function webhookFollowsAction(Request $request)
    {
        if ($request->isMethod('GET')) {
            if ($request->query->has('hub_challenge')) {
                $response = new Response(
                    $request->query->get('hub_challenge'),
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                );
                $response->send();
            }
        }

        if ($request->isMethod('POST')) {
            file_put_contents($this->get('kernel')->getRootDir() . '/request.txt', $request->getContent() . "\n", FILE_APPEND);
        }
    }
}
