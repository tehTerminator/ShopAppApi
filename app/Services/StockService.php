<?php

namespace App\Services;

use App\Constants\HttpCodes;
use App\Models\StockUsage;
use Illuminate\Support\Facades\Auth;
use App\Models\Stock;
use Exception;
use Illuminate\Support\Facades\DB;

class StockService {

    public static function useStockItem($stockItemId, $quantity, string $narration) {
        DB::beginTransaction();

        try {
            $stockItem = Stock::find($stockItemId);
            $stockItem->decreaseQuantity($quantity);
    
            StockUsage::create([
                'stock_item_id' => $stockItemId,
                'user_id' => Auth::user()->id, 
                'quantity' => $quantity,
                'narration' => $narration,
                'balance' => $stockItem->quantity
            ]);

            DB::commit();
            return response()->json(['message' => 'Successfully Added Stock Usage']);
        } catch(Exception $ex) {
            DB::rollBack();
            return response('Unable to Save Usage', HttpCodes::INTERNAL_SERVER_ERROR['code']);
        }
    }

    public static function replainishStockItem($stockItemId, $quantity, string $narration) {
        DB::beginTransaction();

        try {
            $stockItem = Stock::find($stockItemId);
            $stockItem->increaseQuantity($quantity);
    
            StockUsage::create([
                'stock_item_id' => $stockItemId,
                'user_id' => Auth::user()->id, 
                'quantity' => $quantity,
                'narration' => $narration,
                'balance' => $stockItem->quantity
            ]);

            DB::commit();
            return response()->json(['message' => 'Successfully Added Stock Usage']);
        } catch(Exception $ex) {
            DB::rollBack();
            return response('Unable to Save Usage', HttpCodes::INTERNAL_SERVER_ERROR['code']);
        }
    }

    private createStockUsage($stockItemId, $quantity, string $narration) {
        
    }

    public function __construct() {}
}