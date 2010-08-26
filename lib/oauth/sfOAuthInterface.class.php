<?php

/**
 * sfOAuth interface
 *
 * @package    sfOAuthPlugin
 * @subpackage oauth
 * @author     Gordon Franke <info@nevalon.de>
 */
interface sfOAuthInterface {
  public function getAuthorizeUrl();
  public function requestAccessToken($token);
  public function getEmailAddress();
}