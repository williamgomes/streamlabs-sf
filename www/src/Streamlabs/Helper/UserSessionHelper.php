<?php

namespace Streamlabs\Helper;

use Symfony\Component\HttpFoundation\Session\Session;

class UserSessionHelper
{

    /**
     * set a user session depending on provided $userData
     *
     * @param array $userData
     * @return bool
     * @throws \Exception
     */
    public static function setUserSession($userData = array())
    {
        try {
            if (!empty($userData)) {
                $session = new Session();
                $session->set('twitch_id', $userData['id']);
                $session->set('twitch_login', $userData['login']);
                $session->set('twitch_display_name', $userData['display_name']);

                return true;
            }

            return false;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * check if user session exist or not
     *
     * @return bool
     * @throws \Exception
     */
    public static function checkIfUserSessionExist()
    {
        try {
            $session = new Session();
            if (
                $session->get('twitch_id') &&
                $session->get('twitch_login') &&
                $session->get('twitch_display_name')
            ) {
                return true;
            }

            return false;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * unset user session if exist
     *
     * @return bool
     * @throws \Exception
     */
    public static function unsetUserSession()
    {
        try {
            if (true === self::checkIfUserSessionExist()) {
                $session = new Session();
                $session->remove('twitch_id');
                $session->remove('twitch_login');
                $session->remove('twitch_display_name');
            }

            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}