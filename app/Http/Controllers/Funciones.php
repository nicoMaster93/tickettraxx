<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class Funciones extends Controller
{
    public static function sendFailedResponse($errors)
    {
        throw ValidationException::withMessages($errors);
    }
    
    public static function subirBase64($base64_string, $output_file)
    {
        $base64_string = str_replace(' ', '+', $base64_string);
        if(strpos($base64_string, ",")!==false){
            $base64 = explode(',', $base64_string);
            $base64_string = $base64[1];
        }        
        Storage::disk('local')->put($output_file, base64_decode($base64_string));
        return $output_file;
    }
    public static function resizeImage($directorio, $nombre, $prefijo, $ancho, $alto)
    {   
        $rutaImagenOriginal = $directorio.$nombre;
        $tamanio = getimagesize($rutaImagenOriginal);
        $width_gen = $tamanio[0];
        $height_gen = $tamanio[1];

        if ($width_gen >= $height_gen) {
            $alto = ($ancho / $width_gen) * $height_gen;
        } else {
            $ancho = ($alto / $height_gen) * $width_gen;
        }

        $image_type = mime_content_type($rutaImagenOriginal);
        if ($image_type == "image/gif") {
            $img_original = imagecreatefromgif($rutaImagenOriginal);
            imagealphablending($img_original, false);
            imagesavealpha($img_original, true);
        }
        if ($image_type == "image/jpeg") {
            $img_original = imagecreatefromjpeg($rutaImagenOriginal);
        }
        if ($image_type == "image/png") {
            $img_original = imagecreatefrompng($rutaImagenOriginal);
            imagealphablending($img_original, false);
            imagesavealpha($img_original, true);
        }

        $max_ancho = $ancho;
        $max_alto = $alto;
        list($ancho, $alto) = getimagesize($rutaImagenOriginal);
        $x_ratio = $max_ancho / $ancho;
        $y_ratio = $max_alto / $alto;
        if (($ancho <= $max_ancho) && ($alto <= $max_alto)) { //Si ancho 
            $ancho_final = $ancho;
            $alto_final = $alto;
        } elseif (($x_ratio * $alto) < $max_alto) {
            $alto_final = ceil($x_ratio * $alto);
            $ancho_final = $max_ancho;
        } else {
            $ancho_final = ceil($y_ratio * $ancho);
            $alto_final = $max_alto;
        }
        $tmp = imagecreatetruecolor($ancho_final, $alto_final);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);
        imagecolortransparent($tmp);
        imagedestroy($img_original);
        
        $nRuta = $directorio.$prefijo."_".$nombre;
        imagepng($tmp, $nRuta, 0);        
        unlink($rutaImagenOriginal);
        return $nRuta;
    }


    public function sendPush($title, $body, $registration_ids = array(), $data = array(), $type = "cliente") {
       
        $notification = array('title' =>$title , 'text' => $body, 'sound' => 'default');
	    $responsePush = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'key='.Config::get('services.fmc_'.$type.'.key')
        ])->post("https://fcm.googleapis.com/fcm/send",[
            "registration_ids" => $registration_ids,
            "notification" => $notification,
            'data' => $data
        ]);
        return $responsePush;        
	}

    public function closeWindow($msj = ""){
        return view('close')->with('message', $msj);
    }
}
