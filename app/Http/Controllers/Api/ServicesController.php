<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;

class ServicesController extends Controller
{
    use HttpResponses; 

    public function searchServices(Request $request){
        try{
            $request->validate([
                'service_name' => 'nullable|string',
                'price' => 'nullable|numeric', 
            ]);
        
            $serviceName = $request->input('service_name'); 
            $price = $request->input('price'); 
        
            $query = Service::query();
        
            if ($serviceName) {
                $query->where('name', 'LIKE', '%' . $serviceName . '%');
            }
            
            if ($price !== null) {
                $query->where('price', $price);
            }
        
            $services = $query->get(); 
        
            return $this->success( $services);
        }
        catch (\Exception $e) {
            return $this->message(['error' => 'error: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request){
        try{

            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'service_id' => 'nullable|integer|exists:services,id',
            ]);
        
            $service = Service::create([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => $request->input('price'),
            ]);
        
            return $this->message(" Service success");
        }
        catch (\Exception $e) {
            return $this->message(['error ' . $e->getMessage()], 500);
        }
    }

    public function delete($id){
        try {
            $service = Service::findOrFail($id);
            $service->delete();
            return $this->message("Delete service success");
        }
        catch (\Exception $e) {
            return $this->message(['error ' . $e->getMessage()], 500);
        }
    }
}
