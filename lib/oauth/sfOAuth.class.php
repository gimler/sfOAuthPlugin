<?php

/**
 * Abstract sfOAuth class for oauth wrapper classes
 *
 * @package    sfOAuthPlugin
 * @subpackage oauth
 * @author     Gordon Franke <info@nevalon.de>
 */
abstract class sfOAuth implements sfOAuthInterface
{
  public $config;

  protected $provider, $user, $routing;

  public function  __construct($provider, array $config, sfRouting $routing, sfUser $user)
  {
    $this->provider = $provider;
    $this->config   = $config;
    $this->routing  = $routing;
    $this->user     = $user;
  }
}
