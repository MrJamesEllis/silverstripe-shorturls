# ShortURL
A Silverstripe module for shortening and expandening URLs.
Currently plugs into Goo.gl

## Requirements
Per composer.json

## Provider
Get a provider something like so:

```
<?php
use use Codem\ShortURL\Base as ShortURLBase;
function get_provider($provider_name) {
	// returns extension of Codem\ShortURL\Base or throws a Codem\ShortURL\ShortURLException
	return ShortURLBase::provider($provider_name);
}
```

### Googl
If your short url is marked by Googl as anything other than OK, a Codem\ShortURL\ShortURLException will be thrown.
More: https://developers.google.com/url-shortener/v1/getting_started#expand

### Proxy
If you are stuck behind a proxy, in your bootstrap:
```
putenv("HTTP_PROXY=http://host/path:port");
```
The library will pick this up.

Alternatively, extend the relevant provider and implement your own getProxy(), which must return an array contain the keys 'host', 'port' that CURLOPT_PROXY* options can understand:
```
$proxy = [
	'host' => 'https://host/path', // url optionally with a :port if no 'port' provided
	'port' => PORT,// integer port
	'user' => 'username', // '' if no auth
	'pass' => 'pass', // '' if no auth
]
```

### Configuration
Stuff this in your project YAML:

```
Codem\ShortURL\Googl:
  api_key: 'xxxxxxx'
```

## LICENSE

Per composer.json
