# sermepa_payment
_sermepa payment_ integrates [Drupal Payment](https://www.drupal.org/project/payment) Module with Sermepa/RedSyS TPV.

This library is being adapted to Drupal 8, a Drupal 7 ready version can be found on the 7.x branch.

To use this library in a Drupal 8 library, some repositories should be added in `composer.json` file:

```json
"repositories": [
  {
    "type":"git",
    "url":"https://github.com/simplelogica/sermepa_payment"
  },
  {
    "type": "git",
    "url": "https://github.com/CommerceRedsys/sermepa"
  }
],
"require": {
  "drupal/sermepa_payment": "dev-master"
},
```

**IMPORTANT**: due to Drupal's new repository semantic versioning, this module is only compatible with projects using new repository.

If you are using `https://packagist.drupal-composer.org` , you must use this project in commit `d564859e2776c635edb7d564a23d7db52b8988bc` or before.

More information [here](https://www.drupal.org/node/2822344).

## Credits
* This module uses the gateway classes implemented by [Facine](https://github.com/facine/Sermepa) to comunicate with Sermepa TPV.
