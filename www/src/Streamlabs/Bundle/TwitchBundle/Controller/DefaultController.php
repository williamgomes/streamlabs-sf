<?php

namespace Streamlabs\Bundle\TwitchBundle\Controller;

use Streamlabs\Provider\TwitchApiDataProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Exception\InvalidResourceException;

class DefaultController extends Controller
{
    /**
     * @Route("/twitch", name="twitch_default")
     * @Method("GET")
     * @Template("TwitchBundle:Default:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $twitchProvider = new TwitchApiDataProvider();
        $authorizationUrl = $twitchProvider->getAuthUrl();
        if ($request->isMethod('GET') && $request->get('code') != '') {
            $userDataArray = $twitchProvider->getUserData($request);

            echo 'code';
            var_dump($userDataArray);
        }

        return array(
            'authorizationUrl' => $authorizationUrl,
            'userData' => $userDataArray
        );
    }

    /**
     * @Route("/twitch/response", name="twitch_response")
     * @Method("GET")
     * @Template("TwitchBundle:Default:twitchResponse.html.twig")
     */
    public function twitchResponseAction(Request $request)
    {
        if ($request->isMethod('GET') && isset($request)) {
            $twitchProvider = new TwitchApiDataProvider();
            $userDataArray = $twitchProvider->getUserData($request);

            return array(
                'userData' => $userDataArray
            );
        } else {
            throw new InvalidResourceException();
        }
    }
}
