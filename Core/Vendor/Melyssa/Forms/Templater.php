<?php
namespace Melyssa\Forms;

class Templater
{
    private $parsedForm;
    private $template;
    private $form;
    
    public function __construct($template, $form)
    {
        $this->template = $template;
        $this->form = $form;
    }
    
    private function parseTemplate()
    {
        $this->parsedForm = file_get_contents(VIEWS . 'FormLayouts/' . $this->template . '.php');
        // Pegamos o template e os dados para fazer o parse dos elementos:
        $fileToParse = file_get_contents(VIEWS . 'FormLayouts/' . $this->template . '.php');
        $formToReplace = $this->form;
        // Trocando as variáveis: ex de variavel do MForms => [MF:OpenForm] || [MF:InputName]
        // print_r($formToReplace);
        //die;
        foreach($formToReplace as $key => $value){
            $this->parsedForm = str_replace('[MF:'.$key.']', $value, $this->parsedForm);
        }
        
    }
    
    public function getParsedForm()
    {
        // Montamos o formulário:
        $this->parseTemplate();
        // Retornamos o formulário montado:
        return $this->parsedForm;
    }
}