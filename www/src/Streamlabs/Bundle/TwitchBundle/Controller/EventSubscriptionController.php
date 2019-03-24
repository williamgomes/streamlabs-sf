<?php

namespace Streamlabs\Bundle\TwitchBundle\Controller;

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
    }
}
