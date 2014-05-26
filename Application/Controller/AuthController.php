<?php

namespace Application\Controller;

use Core\Mvc\Controller\AbstractActionController;
use Core\Captcha\SimpleCaptcha as Captcha;

class AuthController extends AbstractActionController
{
    /** @var Application\Service\AuthService */
    protected $auth;

    public function init()
    {
        $this->auth = $this->serviceManager->get('auth');
    }
    
    public function loginAction()
    {
        if ($this->auth->isAuthenticated())
            $this->redirect('/');
        
        $layout = $this->application->getLayout();
        $mobile = $this->application->getOption('mobileInterface');
        if ($mobile) {
            $layout->setLayout('Login.mobile');
        } else {
            $layout->setLayout('Login');
        }
        
        $request = $this->serviceManager->get('request');
        $return  = array(
            'mobile' => $mobile,
            'rates'  => $this->serviceManager->get('config')->rates->toArray()
        );
        
        if ($request->isPost()) {
            $login    = $request->getPost('login');
            $password = $request->getPost('password');
            $captcha  = $request->getPost('captcha');

            $return['login'] = $login;
            
            /**
             * Disable captcha on mobile interface
             */
            
            if (!$mobile) {
                if (!isset($_SESSION['captcha']['code']) || ($captcha != $_SESSION['captcha']['code'])) {
                    $return['captchaError'] = true;
                    return $return;
                }
            }
            
            $result = $this->auth->authenticate($login, $password);
            if ($result == 1) {
                $this->redirect('/');
            } elseif ($result == -1) {
                $return['loginError'] = true;
            } elseif ($result == 0) {
                $return['pwdError'] = true;
            }
        }
        
        
        
        return $return;
    }
    
    public function logoutAction()
    {
        $this->auth->logout();
        
        $this->redirect('/');
    }
    
    public function changePasswordAction()
    {
        if (!$this->auth->isAuthenticated())
            $this->redirect('/');
        
        $request = $this->serviceManager->get('request');
        
        if ($request->isPost()) {
            $password = $request->getPost('password');
            $confirm  = $request->getPost('confirm');
            
            $error = null;
            
            if (empty($password)) {
                $error = 'passwordError';
            } elseif ($password != $confirm) {
                $error = 'confirmError';
            }
            
            if ($error) {
                return array(
                    $error => true
                );
            }
            
            $this->auth->changePassword($password);
            $this->redirect('/');
        }
    }
    
    public function captchaAction()
    {
        $this->serviceManager->get('response')->setHeader('Content-type', 'image/gif');
        $this->application->setOption('disableView', true)
                          ->setOption('disableLayout', true);
        
        $options = array(
            'font' => 'public/fonts/chiller.ttf'
        );
        
        $captcha = new Captcha($options);
        $code    = $captcha->generateRandCode();

        $_SESSION['captcha'] = array('code' => $code);
        
        $captcha->printImage($code);
        
    }

}