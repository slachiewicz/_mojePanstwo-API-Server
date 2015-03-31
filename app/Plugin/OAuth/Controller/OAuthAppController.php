<?php

App::uses('AppController', 'Controller');

App::import('Vendor', 'oauth2-php/lib/OAuth2');
App::import('Vendor', 'oauth2-php/lib/IOAuth2Storage');
App::import('Vendor', 'oauth2-php/lib/IOAuth2GrantCode');
App::import('Vendor', 'oauth2-php/lib/IOAuth2RefreshTokens');


class OAuthAppController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
        if (!MpUtils::is_trusted_client($_SERVER['REMOTE_ADDR'])) {
            // deny access to Paszport from untrusted clients
            throw new ForbiddenException();
        }
    }
}
