# CaptchaAPI
A PocketMine-MP Virion to easily do captchas within Minecraft:Bedrock / Pocket Edition

# Usage:
Installation is easy, you may get a compiled phar [here](https://poggit.pmmp.io/ci/UIPlugins/CaptchaAPI/~) or integrate the virion itself into your plugin.

This virion is purely object oriented. So, to use it you'll just have to import the `CaptchaDialog` object.

## Basic Usage:
### Import the classes
You'll need to import this class in order to easily use it within our code.
```php
<?php

use CortexPE\CaptchaAPI\CaptchaDialog;
```
### Construct the `CaptchaDialog` object
Use the pre-set constants available on `CaptchaDialog` or you can provide your own for the length

```php
$form = new CaptchaDialog(CaptchaDialog::CAPTCHA_TYPE_ALPHANUMERIC, CaptchaDialog::CAPTCHA_LENGTH_MODERATE);
```
### Sending the `CaptchaDialog` form
Send the captha dialog to the player with
```php
$player->sendForm($form);
```
Basic usage is easy as 1-2-3! :tada:
### More functionality
Adding more function could be done by setting the "success callback" and the "failure callback" in such a way like this:
```php
$form->setSuccessCallable(function (Player $player){
	// do stuff when the captcha is successfully solved
});
$form->setFailureCallable(function (Player $player){
	// do stuff when the captcha failed to be solved
});
```
### Persistence
Want the form to stay on screen if they entered the wrong captcha or tried to exit it?

This could easily be done by setting the 'persistent' status.
```php
$form->setPersistent(true);
```

-----
**This API was made with :heart: by CortexPE, Enjoy!~ :3**