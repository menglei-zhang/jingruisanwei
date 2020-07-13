<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/6/10
 * Time: 15:36
 */

namespace app\api\controller;

class Form extends Base
{
    public function collectFormId(){
        $data = input('post.');
        if(isset($data['formId']) && trim($data['formId'])){
            $temp = [
                'form_id' => $data['formId'],
              	'add_time' => time()
            ];
          	
            model('Form') -> isUpdate(false) -> allowField(true) -> save($temp);
        }
    }
}