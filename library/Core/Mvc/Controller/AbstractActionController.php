<?php

namespace Core\Mvc\Controller;

use Core\ServiceManager\ServiceManager;
use Core\Mvc\Application;

abstract class AbstractActionController implements ControllerInterface
{

    /**
     *
     * @var Core\Mvc\Application
     */
    protected $application;

    /**
     *
     * @var Core\ServiceManager\ServiceManager 
     */
    protected $serviceManager;

    public function setServiceManager(ServiceManager $sm)
    {
        $this->serviceManager = $sm;

        return $this;
    }

    public function setApplication(Application $application)
    {
        $this->application = $application;

        return $this;
    }

    public function redirect($location)
    {
        $response = $this->serviceManager->get('response');
        $response->setRedirect($location);

        $this->application->halt();
    }
    
    public function incomeNotifier()
    {
        $userService = $this->serviceManager->get('UserService');
        $authService = $this->serviceManager->get('auth');
        
        $income = $userService->fetchIncomingTransfers($authService->getUser());
        $layout = $this->application->getLayout();
        $layout->setBlock('income', $income);
    }

}
