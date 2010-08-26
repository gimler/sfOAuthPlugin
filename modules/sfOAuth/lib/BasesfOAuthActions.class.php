<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 * @package    sfOAuthPlugin
 * @subpackage module
 * @author     Gordon Franke <info@nevalon.de>
 * @version    SVN: $Id$
 */
class BasesfOAuthActions extends sfActions
{
  public function preExecute()
  {
    // get provider configs
    $oAuthConfig = sfConfig::get('app_sf_oauth_plugin_provider');

    // check if provider exist
    $provider = $this->getRequest()->getParameter('provider');
    $this->forward404Unless(isset($oAuthConfig[$provider]));

    // check if provider is enabled
    $oAuthProviderConfig = $oAuthConfig[$provider];
    $this->forward404Unless($oAuthProviderConfig['enabled']);

    // create provider oauth wrapper class
    $class = 'sfOAuth' . sfInflector::camelize($provider);
    $this->sfOAuth = new $class(
      $provider,
      $oAuthProviderConfig,
      $this->getContext()->getRouting(),
      $this->getUser()
    );
  }

  public function executeAuthorize($request)
  {
    $this->getUser()->setAttribute('referer', $request->getReferer());

    // redirect to authorize url
    $this->redirect($this->sfOAuth->getAuthorizeUrl());
  }

  public function executeAccess($request)
  {
    // check protocol version
    switch($this->sfOAuth->config['protocol'])
    {
      case 2:
        $token = $request->getParameter('code');
        break;
      case 1:
      default:
        $token = $request->getParameter('oauth_token');
    }
    $this->sfOAuth->requestAccessToken($token);

    $email = $this->sfOAuth->getEmailAddress();

    // check if user exist
    $sfGuardUser = Doctrine_Core::getTable('sfGuardUser')
      ->retrieveByUsernameOrEmailAddress($email);
    //TODO: add better error handling no 404 redirect to login with user flash message;
    //TODO: add auto register event
    $this->forward404Unless($sfGuardUser);

    // sign in user
    $this->getUser()->signin($sfGuardUser);

    // always redirect to a URL set in app.yml
    // or to the referer
    // or to the homepage
    $signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url', $this->getUser()->getReferer($request->getReferer()));

    return $this->redirect('' != $signinUrl ? $signinUrl : '@homepage');
  }
}
