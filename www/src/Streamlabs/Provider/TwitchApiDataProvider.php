<?php

namespace Streamlabs\Provider;

use Depotwarehouse\OAuth2\Client\Twitch\Provider\Twitch;
use Symfony\Component\HttpFoundation\Request;

class TwitchApiDataProvider
{
    /** @var Twitch */
    protected $twitchProvider;

    /**
     * TwitchApiDataProvider constructor.
     */
    public function __construct()
    {
        $this->twitchProvider = new TwitchProvider([
            'clientId' => getenv('TWITCH_CLIENT_ID'),
            'clientSecret' => getenv('TWITCH_CLIENT_SECRET'),
            'redirectUri' => getenv('TWITCH_REDIRECT_URL'),
        ]);
    }

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