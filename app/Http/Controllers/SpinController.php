<?php

namespace App\Http\Controllers;

use App\Models\Hadiah;
use App\Models\Vocer;
use Illuminate\Http\Request;

class SpinController extends Controller
{
    public function spin(Request $request){
        $validatedData = $request->validate([
            'vocer' => ['required'],
        ]);

        $check_vocer = Vocer::with('hadiahs')->where('code',$request->vocer)->first();

        if($check_vocer == true and $check_vocer->player_name != null )
        {
            if($check_vocer->status == 1){
                if($check_vocer->id_hadiah != null){
                    return response()->json([
                        'message' => 'vocer telah digunakan dan telah claim hadiah'
                    ]);
                }else{
                    return response()->json([
                        'message' => 'vocer telah digunakan'
                    ]);
                }
            }else{
                
                if($check_vocer->id_hadiah == null){
                    //array vocer zonk
                    $hadiahs = Hadiah::select('nama')->orderBy('id','asc')->get();


                    Vocer::where('code',$request->vocer)->update([
                        'status' => 1
                    ]);
                    return response()->json([
                        'message' => 'Maaf '.$check_vocer->player_name.' anda belum beruntung, silahkan coba lagi !',
                        'array_hadiah' => count($hadiahs)
                    ]);
                }else{

                    $data = [];
                    $hadiah_array = Hadiah::select('nama')->orderby('id','asc')->get();
                    foreach($hadiah_array as $hadiah){
                        array_push($data,$hadiah->nama);
                    }
                    $array_hadiah = array_search($check_vocer->hadiahs->nama, $data);
                
                     Vocer::where('code',$request->vocer)->update([
                        'status' => 1
                    ]);
                    return response()->json([
                        'message' => 'Selamat '.$check_vocer->player_name.' mendapatkan '.$check_vocer->hadiahs->nama,
                        'array_hadiah' => $array_hadiah,
                    ]);
                }
            }
        }else{
            return response()->json([
                'message' => 'vocer invalid'
            ]);
        }

    
     

    }

    public function get_hadiah(Request $request){
        $data = [];

        $hadiahs = Hadiah::select('nama','image')->orderBy('id','asc')->get();
        
        foreach($hadiahs as $hadiah){
           
            $data_array = [
                'nama' => $hadiah->nama,
                'image' => $hadiah->image
            ];
                array_push($data,$data_array);
        }

        array_push($data,['nama' => 'zonk',
                          'image' => 'https://img.freepik.com/premium-vector/comic-speech-bubble-with-zonk-text_530597-456.jpg?w=740'
                         ]);

        return response()->json(['data' => $data,
                                 'message' => 'succes get data'
                                ]);
    }
}
