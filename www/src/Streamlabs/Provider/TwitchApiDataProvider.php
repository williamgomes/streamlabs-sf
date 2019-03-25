<?php

namespace Streamlabs\Provider;

use Depotwarehouse\OAuth2\Client\Twitch\Provider\Twitch;
use Symfony\Component\HttpFoundation\Request;

/**
 * Twitch oAuth Client
 *
 * Class TwitchApiDataProvider
 * @package Streamlabs\Provider
 */
class TwitchApiDataProvider
{
    /** @var Twitch */
    protected $twitchProvider;

    /**
     * TwitchApiDataProvider constructor.
     * @param string $redirectUrl
     */
    public function __construct($redirectUrl = "")
    {
        $this->twitchProvider = new TwitchProvider([
            'clientId' => getenv('TWITCH_CLIENT_ID'),
            'clientSecret' => getenv('TWITCH_CLIENT_SECRET'),
            'redirectUri' => $redirectUrl,
        ]);
    }

    /**
     * @return string
     */
    public function getAuthUrl()
    {
        return $this->twitchProvider->getAuthorizationUrl();
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getUserData(Request $request)
    {
        try {
            if (!empty($request)) {
                $accessToken = $this->twitchProvider->getAccessToken('authorization_code', array(
                    'code' => $request->get('code')
                ));
                $userData = $this->twitchProvider->getResourceOwner($accessToken);
                $userDataArray = $userData->toArray();

                return $userDataArray;
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}