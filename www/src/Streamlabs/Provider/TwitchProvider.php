<?php
namespace Streamlabs\Provider;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericResourceOwner;

class TwitchProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;
    private $urlAuthorize;
    private $urlAccessToken;
    private $urlResourceOwnerDetails;
    private $accessTokenMethod;
    private $accessTokenResourceOwnerId;
    private $scopeSeparator;
    private $scopes = null;
    private $responseError = 'error';
    private $responseCode;
    private $responseResourceOwnerId;

    /**
     * TwitchProvider constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $possible   = $this->getConfigurableOptions();
        $configured = array_intersect_key($options, array_flip($possible));
        foreach ($configured as $key => $value) {
            $this->$key = $value;
        }
        // Remove all options that are only used locally
        $options = array_diff_key($options, $configured);
        parent::__construct($options);
    }

    /**
     * @return array
     */
    protected function getConfigurableOptions()
    {
        return ['accessTokenMethod',
            'accessTokenResourceOwnerId',
            'scopeSeparator',
            'responseError',
            'responseCode',
            'responseResourceOwnerId',
            'scopes',
        ];
    }

    /**
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return 'https://id.twitch.tv/oauth2/authorize';
    }

    /**
     * @param array $params
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://id.twitch.tv/oauth2/token';
    }

    /**
     * @param AccessToken $token
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.twitch.tv/helix/users';
    }

    /**
     * @return array|null
     */
    public function getDefaultScopes()
    {
        return $this->scopes;
    }

    /**
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * @param ResponseInterface $response
     * @param array|string $data
     * @throws IdentityProviderException
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (!empty($data[$this->responseError])) {
            $error = $data[$this->responseError];
            $code  = $this->responseCode ? $data[$this->responseCode] : 0;
            throw new IdentityProviderException($error, $code, $data);
        }
    }

    /**
     * @param array $response
     * @param AccessToken $token
     * @return GenericResourceOwner|\League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GenericResourceOwner($response, $this->responseResourceOwnerId);
    }

    /**
     * @return array
     */
    protected function getDefaultHeaders()
    {
        return ['Client-ID' => $this->clientId, 'Accept' => 'application/vnd.twitchtv.v5+json'];
    }

    /**
     * @param null $token
     * @return array
     */
    protected function getAuthorizationHeaders($token = NULL)
    {
        return ['Authorization' => 'Bearer '.$token];
    }
}