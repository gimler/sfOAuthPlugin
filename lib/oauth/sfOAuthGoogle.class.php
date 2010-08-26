<?php

/**
 * sfOAuthGoogle is the sfOAuth wrapper class for google
 *
 * @package    sfOAuthPlugin
 * @subpackage oauth
 * @author     Gordon Franke <info@nevalon.de>
 */
class sfOAuthGoogle extends sfOAuth
{
  protected $oAuth = null, $email = null;

  protected function getOAuth()
  {
    if ($this->oAuth === null)
    {
      $this->oAuth = new OAuth(
        $this->config['consumer_key'],
        $this->config['consumer_secret'],
        OAUTH_SIG_METHOD_HMACSHA1,
        OAUTH_AUTH_TYPE_URI
      );
    }

    return $this->oAuth;
  }

  public function getAuthorizeUrl()
  {
    try
    {
      $oAuth = $this->getOAuth();

      // build query params
      $queryParams = array(
        'oauth_callback' => $this->routing->generate('oauth_access', array('provider' => $this->provider), true)
      );
      if (isset($this->config['scope']))
      {
        $queryParams['scope'] = $this->config['scope'];
      }

      // get request token
      $query = http_build_query($queryParams);
      $requestTokenInfo = $oAuth->getRequestToken($this->config['request_url'] . '?' . $query);

      // save oauth token secret in session
      $this->user->setAttribute('oauth_token_secret', $requestTokenInfo['oauth_token_secret']);

      // build query params
      $queryParams = array(
        'oauth_token'    => $requestTokenInfo['oauth_token'],
      );

      // build query string
      $query = http_build_query($queryParams);

      return $this->config['authorize_url'] . '?' . $query;
    }
    catch(OAuthException $e)
    {
      //TODO: error loging
      var_dump($oAuth->getLastResponseInfo());
      var_dump($oAuth->getLastResponse());
      die();
    }
  }

  public function requestAccessToken($token)
  {
    try
    {
      $oAuth = $this->getOAuth();

      // set request token
      $oAuth->setToken(
        $token,
        $this->user->getAttribute('oauth_token_secret')
      );

      // get access token
      $accessTokenInfo = $oAuth->getAccessToken($this->config['access_url']);

      // set access token
      $oAuth->setToken(
        $accessTokenInfo['oauth_token'],
        $accessTokenInfo['oauth_token_secret']
      );

      // set auth type
      $oAuth->setAuthType(OAUTH_AUTH_TYPE_AUTHORIZATION);
    }
    catch(OAuthException $e)
    {
      //TODO: error loging
#        throw $e;
      var_dump($oAuth->getLastResponseInfo());
      var_dump($oAuth->getLastResponse());
      die();
    }
  }

  public function getEmailAddress()
  {
    if ($this->email === null)
    {
      $oAuth = $this->getOAuth();

      // get email address
      $oAuth->fetch($this->config['email_url']);
      parse_str($oAuth->getLastResponse(), $emailData);

      $this->email = $emailData['email'];
    }

    return $this->email;
  }
}