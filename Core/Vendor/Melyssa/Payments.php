<?php
namespace Melyssa;

use Models\PedidosModel;
use Models\ClientesModel;
use Models\PlanosModel;

class Payments
{
    private $pid;
    
    private $paymentMethod;
    
    private $paymentUrl;
    
    private $clientId;
    
    private $clientName;
    
    private $clientMail;
    
    private $clientAreaCode;
    
    private $clientPhone;
    
    private $productName;
    
    private $productPrice;
    
    private $productQuantity = 1;
    
    private $redirectUrl;
    
    public function __construct($pid, $redirectUrl, $paymentMethod = 'pagseguro')
    {
        if(null === $pid){
            throw new Exception("Unable to create an empty request !");
        }else{
            $this->pid = $pid;
            $this->paymentMethod = $paymentMethod;
            $this->redirectUrl = $redirectUrl;
            // Trabalhando com o pedido:
            $this->fetchProduct();
            $this->fetchClient();
            $this->makeRequisition();
        }
    }
    
    private function fetchProduct()
    {
        $productsModel = new PedidosModel();
        $product = $productsModel->getById($this->pid);
        $this->clientId = $product['cliente'];
        // Carregando o plano:
        $planosModel = new PlanosModel();
        $plano = $planosModel->getById($product['plano']);
        $this->productName = $plano['nome'];
        $html = new Html\Utils();
        $this->productPrice = str_replace(',', '.', $html->priceNumber($plano['preco']));
        return true;
    }
    
    private function fetchClient()
    {
        $clientsModel = new ClientesModel();
        $client = $clientsModel->getById($this->clientId);
        $this->clientName = $client['nome'];
        $this->clientMail = $client['email'];
        // Transalting the phone number:
        $this->clientAreaCode = substr($client['telefone'], 0, 2);
        $this->clientPhone = substr($client['telefone'], 2);
        return true;
    }
    
    private function makeRequisition()
    {
        require_once(VENDOR_PATH . 'PagSeguro/PagSeguroLibrary.php');
        $payment = new \PagSeguroPaymentRequest();
        $payment->setCurrency('BRL');
        $payment->addItem('0001', $this->productName, $this->productQuantity, $this->productPrice);
        $payment->setReference($this->pid);
        $payment->setShippingType(3);
        $payment->setsender($this->clientName, $this->clientMail, $this->clientAreaCode, $this->clientPhone);
        $payment->setRedirectUrl($this->redirectUrl);
        $credentials = \PagSeguroConfig::getAccountCredentials();
        if(defined('ALLOW_PAYMENT_REQUEST') AND ALLOW_PAYMENT_REQUEST === true){
            $this->paymentUrl = $payment->register($credentials);
        }else{
            $this->paymentUrl = '/loja/checkout/pid/' . $this->pid . '/continue/ok/';
        }
    }
    
    public function getPaymentUrl()
    {
        return $this->paymentUrl;
    }
}