<?php

namespace Source\Models;
use Source\Conn\DataLayer;

/**
 * Description of Category
 *
 * @author maxdata
 */
class Ad extends DataLayer{
    
    
    public function select(array $forms): void
    {    
        
        if($this->getToken($forms['token'])){
            
            $count = $this->db()->from('anuncio')
                ->where('anuncio_lixeira')->is('0')
                ->andWhere('anuncio_status')->is($forms['status'])    
                ->select()
                ->all();
            
            $result = $this->db()->from('anuncio')
                ->where('anuncio_lixeira')->is('0')
                ->andWhere('anuncio_status')->is($forms['status'])    
                ->limit($forms['limit'])
                ->offset($forms['offset']) 
                ->select()
                ->all();
            
            if($result){
                foreach($result as $r){
                    unset($r->anuncio_lixeira);
                    $r->anuncio_foto = URL_LOJA.'/'.$r->anuncio_foto;
                    $rows['data'][] = $r;
                }
                
                $this->call(
                    '200',
                    'Sucesso',
                    '',
                    "success",
                    "Registros encontrados"
                )->back(["retorno" => $rows, "registros" => count($count)]);
                return;
            }
            
            $this->call(
                '201',
                'Ops',
                '',
                "ops",
                "Sem registros encontrados"
            )->back(["count" => 0]);
            return;
        }
    }
    
}
