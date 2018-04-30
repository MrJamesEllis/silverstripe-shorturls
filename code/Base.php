<?php
namespace Codem\ShortURL;
abstract class Base extends \Object {

	private static $endpoint = "";
	private static $user_agent = "CodemShortURL/0.1";

	abstract public function shorten($long_url);
	abstract public function expand($short_url);
	abstract public function analytics($short_url);
	abstract protected function api_key();

	final public static function provider($name) {
		$class = "Codem\ShortURL\\" . $name;
		if(class_exists($class)) {
			return new $class;
		}
		throw new ShortURLException("{$name} is not a valid url shortener");
	}

	protected function getProxy() {
		$proxy = getenv('HTTP_PROXY');
		if($proxy) {
			$parts = parse_url($proxy);
			return array(
				'host' => $parts['scheme'] . "://" . $parts['host'],
				'port' => isset($parts['port']) ? (int)$parts['port'] : false,
				'user' => (isset($parts['user']) ? $parts['user'] : ""),
				'pass' => (isset($parts['pass']) ? $parts['pass'] : ""),
			);
		}
		return false;
	}

	/*
	 * @param $post_fields mixed see CURLOPT_POSTFIELDS
	 */
	final protected function doRequest($url, $method = "POST", $headers = array(), $post_fields = NULL) {
		try {
			$curl = curl_init();
			$proxy = $this->getProxy();
			if($proxy) {
				curl_setopt($curl, CURLOPT_PROXY, $proxy['host']);
				if($proxy['port']) {
					curl_setopt($curl, CURLOPT_PROXYPORT, $proxy['port']);
				}
				if($proxy['user'] && $proxy['pass']) {
					curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy['user'] . ":" . $proxy['pass']);
				}
			}
			curl_setopt($curl, CURLOPT_USERAGENT, $this->config()->get('user_agent'));
			curl_setopt_array($curl, array(
				CURLOPT_SSL_VERIFYHOST => 2,
				CURLOPT_SSL_VERIFYPEER => true
			));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			switch($method) {
					case "POST":
						curl_setopt($curl, CURLOPT_POST, true);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
						break;
					case "GET":
						curl_setopt($curl, CURLOPT_HTTPGET, true);
						break;
			}
			curl_setopt($curl, CURLOPT_URL, $url);
			$response = curl_exec($curl);
			curl_close($curl);
			return $response;
		} catch (\Exception $e) {
		}
		return false;
	}

}
