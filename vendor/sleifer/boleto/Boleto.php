<?php
namespace sleifer\boleto;

use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorJPG;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Picqer\Barcode\BarcodeGeneratorSVG;

class Boleto{

    private $barcode;
    private $type;
    private $value;
    private $due_date = null;
    private $error;

    const BOLETO = 0;
    const CONVENIO = 1; 
    const TYPE_SVG = 0;
    const TYPE_JPG = 1;
    const TYPE_PNG = 2;
    const TYPE_HTML = 3;

    function __construct($barcode){
        if(!BoletoValidator::valida($barcode)){
            $this->error = 'Código de barras inválido!';
            return;
        }

        $barcode = str_replace(['.', '-', ' '], '', $barcode);
        if($barcode[0] == 8){
            $this->formatConvenio($barcode);
            $this->type = self::CONVENIO;
        }else{
            $this->formatBoleto($barcode);
            $this->type = self::BOLETO;
        }
    }

    private function formatBoleto($barcode){
        $this->setValue(substr($barcode, 37, 10));
        $this->setDueDate(substr($barcode, 33, 4));
        $pos = [9, 20, 31];
        $split = str_split($barcode);
        foreach($pos as $i){
            unset($split[$i]);
        }
        $barcode = implode($split);
        $this->barcode = substr($barcode, 0, 4) . substr($barcode, 29, 15) . substr($barcode, 4, 25);
    } 

    private function formatConvenio($barcode){
        $pos = [11, 23, 35, 47];
        $split = str_split($barcode);
        foreach($pos as $i){
            unset($split[$i]);
        }
        $this->barcode = implode($split);
        $this->setValue(substr($this->barcode, 4, 11));
    }

    private function setValue($value){
        $val = ltrim($value, 0);
        if(empty($val)){
            $this->value = null;
        }else{
            $this->value = substr_replace(ltrim($value, 0), '.', -2, 0);
        }
    }

    private function setDueDate($days){
        if($days == '0000'){
            $this->due_date = null;
        }else{
            $this->due_date =  date('Y-m-d', strtotime($days . " days",strtotime('1997-10-07')));
        }
    }

    public function getValue(){
        return $this->value;
    }

    public function getDueDate(){
        return $this->due_date;
    }

    public function getType(){
        return $this->type;
    }

    public function getError(){
        return $this->error;
    }

    public function getBarCode($width = 2, $height = 50, $type = self::TYPE_SVG){
        switch ($type) {
            case 0:
                $barcode = new BarcodeGeneratorSVG();
                break;            
            case 1:
                $barcode = new BarcodeGeneratorJPG();
                break;
            case 2:
                $barcode = new BarcodeGeneratorPNG();
                break;
            case 3:
                $barcode = new BarcodeGeneratorHTML();
                break;
        }
        return $barcode->getBarcode($this->barcode, $barcode::TYPE_INTERLEAVED_2_5, $width, $height);
    }

}