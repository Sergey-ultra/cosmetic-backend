<?php
declare(strict_types=1);


namespace App\Http\Controllers\Api\Admin\Parser\Old;




class ParserControllerProxy
{
    private $obj;


    public function __construct(ParserController $object)
    {
        $this->obj = $object;
    }

    public function __call($method, $arguments)
    {
        try {
            return call_user_func_array([$this->obj, $method], $arguments);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Ошибка апи парсера'. $e->getMessage()
            ]);
        }
    }
}