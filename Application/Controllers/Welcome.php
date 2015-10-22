<?php
    namespace Controllers;
    
    use Melyssa\Mvc\Controller;
    
    class Welcome extends Controller
    {
        public function indexAction()
        {
            $this->view("Index");
        }
    }