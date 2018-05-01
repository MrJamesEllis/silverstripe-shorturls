# Silverstripe Short URL handling

A Silverstripe 3.x module for shortening and expanding URLs.

> Note: currently only plugs into Goo.gl, which is in the process of being deprecated by Google

## Requirements
Per composer.json

## Provider
Get a provider something like so:

```
use Codem\ShortURL\Base;
...
$provider = Base::provider('Googl');
$url = "https://example.com";
$short_url = $provider->shorten($url);
// https://goo.gl/buxgs
$long_url = $provider->expand($short_url);
// https://example.com/
```

### Googl
If your short url is marked by Googl as anything other than OK, a ```Codem\ShortURL\ShortURLException``` will be thrown.
More: https://developers.google.com/url-shortener/v1/getting_started#expand

### Proxy
If you are stuck behind a proxy, in your bootstrap:
```
putenv("HTTP_PROXY=http://host/path:port");
```
The module will pick this up and use it in the cURL request.

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
Providers come configured in ```_config/config.yml``` and you can override these value with your own local YML config, if required.

Add your provider API config, e.g for Googl, the api_key retrieved from your Google Cloud Console project:
```
Codem\ShortURL\Googl:
  api_key: 'xxxxxxx'
```

## LICENSE

Per composer.json
