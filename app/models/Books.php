<?php

class Books extends ModelBase
{
	public function searchBooksFromAmazon($keywords) {
		if (!is_array($keywords)) {
			$keywords = array($keywords);
		}
		$awsec = new AmazonECommerceService($this->config->amazon);
		$awsec->itemSearch();
	}
}