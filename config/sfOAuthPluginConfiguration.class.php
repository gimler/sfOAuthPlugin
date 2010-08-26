<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfOAuthPlugin configuration.
 * 
 * @package    sfOAuthPlugin
 * @subpackage config
 * @author     Gordon Franke <info@nevalon.de>
 * @version    SVN: $Id$
 */
class sfOAuthPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    $this->dispatcher->connect('routing.load_configuration', array('sfOAuthRouting', 'listenToRoutingLoadConfigurationEvent'));
  }
}
