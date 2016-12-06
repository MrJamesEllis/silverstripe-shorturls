<?php
namespace Codem\ShortURL;
class ShortURLException extends Exception {}
abstract class Base {

	protected $endpoint = "";
	protected $user_agent = "CodemShortURL/0.1";

	abstract public function shorten($long_url);
	abstract public function expand($short_url);
	abstract public function analytics($short_url);
	abstract protected function api_key();

	final public static function provider($name) {
		$class = "Codem\ShortURL\{$name}";
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
				'port' => isset($parts['port']) ? (int)$parts['port'] : FALSE,
				'user' => (isset($parts['user']) ? $parts['user'] : ""),
				'pass' => (isset($parts['pass']) ? $parts['pass'] : ""),
			);
		}
		return FALSE;
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
			curl_setopt($curl, CURLOPT_USERAGENT, $this->user_agent);
			curl_setopt_array($curl, array(
				CURLOPT_SSL_VERIFYHOST => TRUE,
				CURLOPT_SSL_VERIFYPEER => TRUE
			));
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
			switch($method) {
					case "POST":
						curl_setopt($curl, CURLOPT_POST, TRUE);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $post_fields);
						break;
					case "GET":
						curl_setopt($curl, CURLOPT_HTTPGET, TRUE);
						break;
			}
			curl_setopt($curl, CURLOPT_URL, $url);
			$response = curl_exec($ch);
			curl_close($ch);
			return $response;
		} catch (Exception $e) {
		}
		return FALSE;
	}

}
