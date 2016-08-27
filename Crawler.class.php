<?php
/*
 * Author: setni
 */

class Crawler
{
    //--------------VARIABLES
	private $fileArchi;
	private $url;
	private $results = array();
	private $sameHost;
	private $host;

    //--------------SETTERS
	public function setDepth   ($fileArchi) { $this->depth = $fileArchi; }
	public function setHost    ($host)      { $this->host = $host; }
	public function setSameHost($sameHost)  { $this->sameHost = $sameHost; }

	public function setUrl($url)
	{
		$this->url = $url;
		$this->setHost($this->getHostFromUrl($url));
	}

    //--------------CONSTRUCTORS
	public function __construct($fileArchi = null, $url = null, $sameHost = false)
	{
		if (!empty($url)) {
			$this->setUrl($url);
		}
		if (isset($fileArchi) && !is_null($fileArchi)) {
			$this->setDepth($fileArchi);
		}
		$this->setSameHost($sameHost);
	}

    //--------------PUBLIC
	public function crawler()
	{
		if (empty($this->url)) {
			throw new \Exception('Please set the URL');
		}
		$this->_crawler($this->url, $this->depth);
		return $this->results;
	}

    //--------------PRIVATE
	private function _crawler($url, $fileArchi)
	{
        
		static $seen = array();
        
		if (empty($url)) return;
        
		if (in_array($url, $this->results)) {
            
			return;
		}
        
        
        
		if ($fileArchi === 0 || isset($seen[$url])) {
			return;
		}
        
		$seen[$url] = true;
        
		$dom = new \DOMDocument('1.0');
		@$dom->loadHTMLFile($url);
        
		$this->results[] = $url;

		$links = $dom->getElementsByTagName('a');
        
		foreach ($links as $element)
		{
			if (!$href = $this->makeUrl($url, $element->getAttribute('href'))) {
                $href = $element->getAttribute('href');
				continue;
			}
			$this->_crawler($href, $fileArchi - 1);
		}

		return $url;
	}

	private function makeUrl($url, $href)
	{
		if (0 !== strpos($href, 'http'))
		{
			if (0 === strpos($href, 'javascript:') || 0 === strpos($href, '#'))
			{
				return false;
			}
			$path = '/' . ltrim($href, '/');
			if (extension_loaded('http'))
			{
				$new_href = http_build_url($url, array('path' => $path), HTTP_URL_REPLACE, $parts);
			}
			else
			{
				$parts = parse_url($url);
				$new_href = $this->makeURLFromParts($parts);
				$new_href .= $path;
			}
			// Check Relative link
			if (0 === strpos($href, './') && !empty($parts['path']))
			{
				// Check the path reality
				if (!preg_match('@/$@', $parts['path'])) {
					$path_parts = explode('/', $parts['path']);
					array_pop($path_parts);
					$parts['path'] = implode('/', $path_parts) . '/';
				}

				$new_href = $this->makeURLFromParts($parts) . $parts['path'] . ltrim($href, './');
			}
			$href = $new_href;
		}
		$href = rtrim($href, '/');
		if ($this->sameHost && $this->host != $this->getHostFromUrl($href)) {
			return false;
		}
		return $href;
	}

    private function getHostFromUrl($url)
	{
		$parts = parse_url($url);
		preg_match("@([^/.]+)\.([^.]{2,6}(?:\.[^.]{2,3})?)$@", $parts['host'], $host);
		return array_shift($host);
	}
    
	private function makeURLFromParts($parts)
	{
		$new_href = $parts['scheme'] . '://';
		if (isset($parts['user']) && isset($parts['pass'])) {
			$new_href .= $parts['user'] . ':' . $parts['pass'] . '@';
		}
		$new_href .= $parts['host'];
		if (isset($parts['port'])) {
			$new_href .= ':' . $parts['port'];
		}
		return $new_href;
	}
}
