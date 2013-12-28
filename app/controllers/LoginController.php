<?php

class LoginController extends ControllerBase
{

    public function indexAction()
    {
    	$response = $this->di->get('response');
		
		try {
			$client = new Evernote\Client(array(
				'consumerKey' => $this->config->evernote->consumerKey,
				'consumerSecret' => $this->config->evernote->consumerSecret,
				'sandbox' => $this->config->evernote->sandbox
			));
		} catch (OAuthException $e) {
			$this->log->error("Error initialize evernote client: ".$e->getMessage());
		}
		
		if (isset($_GET['oauth_verifier'])) {
			$this->session->set('EvernoteRequestTokenVerifier', $_GET['oauth_verifier']);
			$accessToken = $client->getAccessToken(
				$this->session->get('EvernoteRequestToken'),
				$this->session->get('EvernoteRequestTokenSecret'),
				$_GET['oauth_verifier']
			);
			if ($accessToken) {
				$this->session->set['EvernoteAccessToken'] = $accessToken['oauth_token'];
				$this->log->info('sccessToken: '.print_r($accessToken, true));
				
				$accessToken = $accessToken['oauth_token'];
				$client = new Evernote\Client(array(
					'token' => $accessToken,
					'sandbox' => $this->config->evernote->sandbox
				));
				$userStore = $client->getUserStore();
				if ($userStore) {
					$user = $userStore->getUser();
					$this->log->info($user->id);
					$this->log->info($user->username);
					return true;
				} else {
					$this->log->error('Failed to get user: '.print_r($accessToken, true));
				}
            } else {
                $this->log->error('Failed to obtain token credentials.');
            }
		} else {
			$requestToken = $client->getRequestToken($this->config->evernote->callback);
			if ($requestToken) {
				$this->session->set('EvernoteRequestToken', $requestToken['oauth_token']);
				$this->session->set('EvernoteRequestTokenSecret', $requestToken['oauth_token_secret']);
				
				return $response->redirect($client->getAuthorizeUrl($requestToken['oauth_token']), true);
			} else {
				$this->log->error('Failed to obtain temporary credentials.');
			}
		}
		
		$response->setStatusCode(500, "Internal Server Error");
		$response->setContent("500 Internal Server Error");
		return $response->send();
    }

}

