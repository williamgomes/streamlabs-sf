<?php

namespace Streamlabs\Bundle\TwitchBundle\Controller;

use GuzzleHttp\Client;
use Streamlabs\Bundle\TwitchBundle\Forms\AddFavoriteStreamerType;
use Streamlabs\Entities\Users;
use Streamlabs\Entities\UserToStreamer;
use Streamlabs\Helper\UserSessionHelper;
use Streamlabs\Provider\TwitchApiDataProvider;
use Streamlabs\Provider\TwitchEventSubscriptionProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/twitch", name="twitch_default")
     * @Method("GET")
     * @Template("TwitchBundle:Default:index.html.twig")
     */
    public function indexAction(Request $request)
    {
        $redirectUrl = $request->getScheme() . '://' . $request->getHttpHost() . $this->generateUrl('twitch_default');
        $session = new Session();
        $twitchProvider = new TwitchApiDataProvider($redirectUrl);
        $authorizationUrl = $twitchProvider->getAuthUrl();

        try {
            if (true == UserSessionHelper::checkIfUserSessionExist()) {
                $session->getFlashBag()->add('success', 'Logged in successfully');
                return $this->redirectToRoute('twitch_streamer');
            }

            if ($request->isMethod('GET') && $request->get('code') != '') {
                $userDataArray = $twitchProvider->getUserData($request);
                $result = $this->handleUserData($userDataArray['data'][0]);

                if ($result) {
                    UserSessionHelper::setUserSession($userDataArray['data'][0]);

                    $session->getFlashBag()->add('success', 'Logged in successfully');
                    return $this->redirectToRoute('twitch_streamer');
                } else {
                    $session->getFlashBag()->add('error', 'Logged failed. Try again.');
                }
            }
        } catch (\Exception $exception) {
            if ('dev' === $this->container->getParameter('kernel.environment')) {
                $session->getFlashBag()->add('error', $exception->getMessage());
            }
        }


        return array(
            'authorizationUrl' => $authorizationUrl
        );
    }

    /**
     * @Route("/twitch/streamer", name="twitch_streamer")
     * @Method({"GET","POST"})
     * @Template("TwitchBundle:Default:twitchStreamer.html.twig")
     */
    public function twitchStreamerAction(Request $request)
    {
        $userToStreamer = new UserToStreamer();
        $form = $this->createAddFavStreamerForm($userToStreamer);

        try {
            $session = new Session();
            if (false === UserSessionHelper::checkIfUserSessionExist()) {
                $session->getFlashBag()->add('error', 'Please login to continue.');
                return $this->redirectToRoute('twitch_default');
            }

            $oUser = $this->getDoctrine()->getRepository(Users::class)->findOneBy(array(
                'twitch_id' => $session->get('twitch_id'
                )));

            if ($request->isMethod(Request::METHOD_POST)) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $streamerData = $this->checkGivenStreamerName($form->get('streamer_name')->getData());
                    if ($streamerData) {
                        $this->handleUserToStreamerData($oUser, $streamerData, $request);
                    }
                }
            }
        } catch (\Exception $exception) {
            if ('dev' === $this->container->getParameter('kernel.environment')) {
                $session->getFlashBag()->add('error', $exception->getMessage());
            }
        }

        return array(
            'form' => $form->createView()
        );
    }


    /**
     * @Route("/twitch/streamer/watch", name="twitch_streamer_watch")
     * @Method({"GET"})
     * @Template("TwitchBundle:Default:twitchStreamerWatch.html.twig")
     */
    public function twitchStreamerWatchAction(Request $request)
    {
        try {
            $session = new Session();
            if (false === UserSessionHelper::checkIfUserSessionExist()) {
                $session->getFlashBag()->add('error', 'Please login to continue.');
                return $this->redirectToRoute('twitch_default');
            }

            $oUser = $this->getDoctrine()->getRepository(Users::class)->findOneBy(array(
                'twitch_id' => $session->get('twitch_id'
                )));
            $oUserToStreamer = $this->getDoctrine()->getRepository(UserToStreamer::class)->findOneBy(array('user_id' => $oUser));

            if ($oUserToStreamer instanceof UserToStreamer) {
                return array(
                    'streamerLogin' => $oUserToStreamer->getStreamerName()
                );
            }
        } catch (\Exception $exception) {
            if ('dev' === $this->container->getParameter('kernel.environment')) {
                $session->getFlashBag()->add('error', $exception->getMessage());
            }
        }

        return array(
            'streamerLogin' => ''
        );

    }


    /**
     * @Route("/twitch/logout", name="logout")
     * @Method({"GET"})
     */
    public function logoutAction(Request $request)
    {
        try {
            $session = new Session();
            $oUser = $this->getDoctrine()->getRepository(Users::class)->findOneBy(array(
                'twitch_id' => $session->get('twitch_id'
                )));
            $oUserToStreamer = $this->getDoctrine()->getRepository(UserToStreamer::class)->findOneBy(array(
                'user_id' => $oUser
            ));

            //unsubscribe unused webhook subscription
            $twitchEventSub = $this->container->get('twitch_event_subscription_provider');
            $topicUrl = 'https://api.twitch.tv/helix/users/follows?first=1&to_id=' . $oUserToStreamer->getStreamerId();
            $twitchEventSub->unsubscribeToEvent('twitch_webhook_follows', $topicUrl);

            UserSessionHelper::unsetUserSession();
            $session->getFlashBag()->add('success', 'Logout was successful.');
        } catch (\Exception $exception) {
            if ('dev' === $this->container->getParameter('kernel.environment')) {
                $session->getFlashBag()->add('error', $exception->getMessage());
            }
        }

        return $this->redirectToRoute('twitch_default');
    }


    /**
     * @param UserToStreamer $userToStreamer
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createAddFavStreamerForm(UserToStreamer $userToStreamer)
    {
        return $this->createForm(AddFavoriteStreamerType::class, $userToStreamer, array(
            'action' => $this->generateUrl('twitch_streamer'),
            'method' => 'POST',
        ));
    }

    /**
     * create new user if data do not exist
     * if user exist, just update his/her last login time
     * @param array $userData
     * @return bool
     * @throws \Exception
     */
    public function handleUserData($userData = array())
    {
        try {
            if (!empty($userData)) {
                $oUser = $this->getDoctrine()->getRepository(Users::class)->findOneBy(array('twitch_id' => $userData['id']));

                if ($oUser instanceof Users) {
                    $oUser->setLastLogin(new \DateTime());
                } else {
                    $oUser = new Users();
                    $oUser->setTwitchId($userData['id']);
                    $oUser->setTwitchLogin($userData['login']);
                    $oUser->setTwitchDisplayName($userData['display_name']);
                    $oUser->setTwitchBroadcasterType($userData['broadcaster_type']);
                    $oUser->setLastLogin(new \DateTime());
                    $oUser->setCreatedAt(new \DateTime());
                }

                $em = $this->getDoctrine()->getManager();
                $em->persist($oUser);
                $em->flush();

                return true;
            }

            return false;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * check if the given streamer exist and if so get his/her details
     * @param string $streamerName
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function checkGivenStreamerName($streamerName = "")
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://api.twitch.tv/helix/users?login=' . $streamerName, [
                'headers' => [
                    'client-id' => getenv('TWITCH_CLIENT_ID'),
                    'accept' => 'application/vnd.twitchtv.v5+json'
                ]
            ]);

            $jsonResponse = $response->getBody()->getContents();
            $arrResponse = json_decode($jsonResponse, true);
            $arrResponse = $arrResponse['data'];
            if (count($arrResponse) > 0) {
                return $arrResponse[0];
            }

            return false;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * as per requirement user can add his/her favorite `streamer`
     * so if user never added his/her favorite streamer, just add him
     * if user did add his/her favorite streamer, just update if his/her favorite streamer changed
     *
     * @param Users $user
     * @param array $streamerData
     * @param Request $request
     * @throws \Exception
     */
    public function handleUserToStreamerData(Users $user, $streamerData = array(), Request $request)
    {
        try {
            $oUserToStreamer = $this->getDoctrine()->getRepository(UserToStreamer::class)->findOneBy(array('user_id' => $user));
            if ($oUserToStreamer instanceof UserToStreamer) {
                if ($oUserToStreamer->getStreamerId() != $streamerData['id']) {
                    //unsubscribe old favorite streamer webhook
                    $callbackUrl = $request->getScheme() . '://' . $request->getHttpHost() . $this->generateUrl('twitch_webhook_follows');
                    $topicUrl = 'https://api.twitch.tv/helix/users/follows?first=1&to_id=' . $oUserToStreamer->getStreamerId();
                    TwitchEventSubscriptionProvider::unsubscribeToEvent($callbackUrl, $topicUrl);

                    //update new favorite streamer data
                    $oUserToStreamer->setStreamerId($streamerData['id']);
                    $oUserToStreamer->setStreamerName($streamerData['login']);
                    $oUserToStreamer->setStreamerDisplayName($streamerData['display_name']);
                    $oUserToStreamer->setUpdatedAt(new \DateTime());
                }
            } else {
                $oUserToStreamer = new UserToStreamer();
                $oUserToStreamer->setStreamerId($streamerData['id']);
                $oUserToStreamer->setStreamerName($streamerData['login']);
                $oUserToStreamer->setStreamerDisplayName($streamerData['display_name']);
                $oUserToStreamer->setUserId($user->getId());
                $oUserToStreamer->setUpdatedAt(new \DateTime());
                $oUserToStreamer->setCreatedAt(new \DateTime());
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($oUserToStreamer);
            $em->flush();

            //subscribe backend webhook event to listen events for the streamer
            $twitchEventSub = $this->container->get('twitch_event_subscription_provider');
            $topicUrl = 'https://api.twitch.tv/helix/users/follows?first=1&to_id=' . $oUserToStreamer->getStreamerId();
            $twitchEventSub->subscribeToEvent('twitch_webhook_follows', $topicUrl);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
