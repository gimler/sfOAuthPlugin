<?php if (sfConfig::get('app_sf_oauth_plugin_enabled')): ?>
<ul>
<?php foreach(sfConfig::get('app_sf_oauth_plugin_provider') as $provider => $providerConfig): ?>
  <?php if ($providerConfig['enabled']): ?>
    <li><?php echo link_to($provider, '@oauth_authorize?provider=' . $provider) ?></li>
  <?php endif; ?>
<?php endforeach; ?>
</ul>
<?php endif; ?>