# sermepa_payment
_sermepa payment_ integrates [Drupal Payment](https://www.drupal.org/project/payment) Module with Sermepa/RedSyS TPV.

This library is being adapted to Drupal 8, a Drupal 7 ready version can be found on the 7.x branch.

To use this library in a Drupal 8 library, some repositories should be added in `composer.json` file:

```json
"repositories": [
  {
    "type":"vcs",
    "url":"/code/libraries/sermepa_payment"
  },
  {
    "type": "git",
    "url": "https://github.com/simplelogica/sermepa_php"
  }
],
"require": {
  "drupal/sermepa_payment": "dev-master"
},
```

## Credits
* This module uses the gateway classes implemented by [Facine](https://github.com/facine/Sermepa) to comunicate with Sermepa TPV.
