<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\StockMovement;

class ApiController extends Controller
{
    public function addProduct(Request $request){
        $return = [];
        $status_code = 201;
        $product = new Product;

        try {
            $product->name = trim($request->name);
            $product->sku = trim($request->sku);
            if($product->save() && is_numeric($request->qtd)){
                # entrada de estoque inicial
                $stkm = new StockMovement;
                $stkm->sku = trim($product->sku);
                $stkm->qtd = trim($request->qtd);
                $stkm->save();

                $return = ["message" => "produto criado com sucesso"];
            }
            
        } catch (Exception $e) {
            $return = ["message" => "erro ao criar produto"];
            $status_code = 404;
        }
        
        return response()->json($return, $status_code);
    }

    public function moveStock(Request $request){
        $return = [];
        $status_code = 200;

        $produto = Product::firstWhere('sku',$request->sku);

        if(isset($produto)){
            # movimento de produto
            $stkm = new StockMovement;
            $stkm->sku = trim($request->sku);
            $stkm->qtd = trim($request->qtd);
            try {
                $stkm->save();
                $return = ["message" => "movimentação de produto realizada"];
            } catch (Exception $e) {
                $return = ["message" => "movimentação não realizada"];
                $status_code = 404;
            }
        }else{
            $return = ["message" => "produto não encontrado"];
            $status_code = 404;
        }
        
        return response()->json($return, $status_code);
    }

    public function historyStock(){
        $products = StockMovement::get()->toJson(JSON_PRETTY_PRINT);
        return response($products, 200);
    }
}
