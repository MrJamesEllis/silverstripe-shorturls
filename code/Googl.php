<?php
namespace Codem\ShortURL;
use Codem\ShortURL\Base as Base;
use Codem\ShortURL\ShortURLException as ShortURLException;
class Googl extends Base {

	protected $endpoint = "https://www.googleapis.com/urlshortener/v1/url";

	protected function api_key() {
		return \Config::inst()->get('Codem\ShortURL\Googl', 'api_key');
	}

	public function shorten($long_url) {
		$headers = array(
			"Content-Type: application/json",
		);
		$post_fields = array('longUrl' => $long_url);
		$post_body = json_encode($post_fields);
		$response = $this->doRequest($this->endpoint, "POST", $headers, $post_body);
		if($response) {
			$decoded = json_decode($response, FALSE);
			if(empty($decoded->id)) {
				throw new ShortURLException("No short url returned from {$this->endpoint}");
			}
			return $decoded->id;
		}
		throw new ShortURLException("Empty response returned from {$this->endpoint}");
	}

	public function expand($short_url) {
		if(empty($short_url)) {
			throw new ShortURLException("Provide a valid short_url as an argument");
		}
		$url = $this->endpoint . "?shortUrl=" . urlencode($short_url);
		$response = $this->doRequest($url, "GET");
		if($response) {
			$decoded = json_decode($response, FALSE);
			switch($decoded->status) {
				case "REMOVED":
				case "MALWARE";
					throw new ShortURLException("URL has been marked as {$decoded->status}");
					break;
				case "OK":
					return $decoded->longUrl;
					break;
				default:
					throw new ShortURLException("Unhandled status {$decoded->status}");
					break;
			}
		}
	}

	public function analytics($short_url) {
		if(empty($short_url)) {
			throw new ShortURLException("Provide a valid short_url as an argument");
		}
		$url = $this->endpoint . "?project=FULL&shortUrl=" . urlencode($short_url);
		$response = $this->doRequest($url, "GET");
		if($response) {
			// for now, just return analytics struct
			return json_decode($response, FALSE);
		}
		throw new ShortURLException("Failed to get analytics for {$short_url}");
	}

}
