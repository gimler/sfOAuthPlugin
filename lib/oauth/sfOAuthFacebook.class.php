<?php

/**
 * sfOAuthFacebook is the sfOAuth wrapper class for facebook
 *
 * @package    sfOAuthPlugin
 * @subpackage oauth
 * @author     Gordon Franke <info@nevalon.de>
 */
class sfOAuthFacebook extends sfOAuth
{
  protected $profile = null, $accessToken = null;

  public function getAuthorizeUrl()
  {
    // build query params
    $queryParams = array(
      'client_id'    => $this->config['consumer_key'],
      'redirect_uri' => $this->routing->generate('oauth_access', array('provider' => $this->provider), true),
      'scope'        => $this->config['scope']
    );

    // build query string
    $query = http_build_query($queryParams);

    return $this->config['authorize_url'] . '?' . $query;
  }

  public function requestAccessToken($token)
  {
    // build query params for access token
    $queryParams = array(
      'client_id'     => $this->config['consumer_key'],
      'client_secret' => $this->config['consumer_secret'],
      'code'          => $token,
      'redirect_uri'  => $this->routing->generate('oauth_access', array('provider' => $this->provider), true)
    );
    $query = http_build_query($queryParams);

    // get the access token
    $accessTokenInfo = file_get_contents($this->config['access_url'] . '?' . $query);
    parse_str($accessTokenInfo, $accessTokenInfoValues);

    $this->accessToken = $accessTokenInfoValues;
  }

  protected function getAccessToken()
  {
    if ($this->accessToken === null)
    {
      throw new sfException('You must first request the access token with `requestAccessToken()`');
    }

    return $this->accessToken['access_token'];
  }

  protected function getProfile()
  {
    if ($this->profile === null)
    {
      // build query params for profile page
      $queryParams = array(
        'access_token'  => $this->getAccessToken(),
        'fields'        => 'email'
      );
      $query = http_build_query($queryParams);
      $profileData = json_decode(file_get_contents($this->config['profile_url'] . '?' . $query));

      $this->profile = $profileData;
    }

    return $this->profile;
  }

  public function getEmailAddress()
  {
    $profile = $this->getProfile();

    return $profile->email;
  }
}
