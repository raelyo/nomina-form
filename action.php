<?php
header("Content-type: application/json");
function edad($fecha1, $fecha2) {
    if (is_string($fecha1) and is_string($fecha2)) {  
        $fecha1 = strtotime($fecha1);
        $fecha2 = strtotime($fecha2);
        $diferencia_de_fechas = $fecha2 - $fecha1;
        if ($diferencia_de_fechas < 0)
            return 0;
	return round(($diferencia_de_fechas / (60 * 60 * 24 * 365)),3);
     }
     return 0;
}

function vacations($year){
    $base = 4; 
    if ($year == 0)
       return 0;
    $period = 2;
    for ($i = 1; $i <= $year; $i++)
       if ($i < 5)
           $base += $period;    
       else
          if (($i % 5) == 0)
             $base += $period; 
    return $base; 

}

function proporcional($fecha_ingreso, $fecha_baja){
    if (is_string($fecha_ingreso) and is_string($fecha_baja)) {  
        $fecha1 = strtotime($fecha_ingreso);
        $fecha2 = strtotime($fecha_baja);
        $y = date('Y', $fecha2); 
        $fecha_first = mktime(0,0,0,1,1,$y);
        $fecha_fin = max($fecha1,$fecha_first);
        $dias =  abs(($fecha2 - $fecha_fin )/(60 * 60 * 24)) + 1;
        return round(round($dias)/365,4);
    }  
}

function proporcional2($fecha_ingreso, $fecha_baja){
    if (is_string($fecha_ingreso) and is_string($fecha_baja)) {  
        $fecha1 = strtotime($fecha_ingreso);
        $fecha2 = strtotime($fecha_baja);
        
        $fecha_first = mktime(0,0,0,date('m',$fecha1),date('d',$fecha1),date('Y', $fecha2));
        if ($fecha_first >= $fecha2)
              $fecha_l = mktime(0,0,0,date('m',$fecha1),date('d',$fecha1),date('Y', $fecha2)-1);
        else 
            $fecha_l = $fecha_first;
        $dias =  abs(($fecha2 - $fecha_l )/(60 * 60 * 24));
        return round(($dias/365),7);
        
    }  
}

function search($value, $matriz, $column){
    foreach ($matriz as $k => $array){
          if ($array[2]){        
		  if ($array[1] <= $value and $value <= $array[2] ){
		       return $array[$column];
		  }
          }else {
                  if ($array[1] <= $value)
                       return $array[$column]; 
          }
          
    }
    return 0;
}


function calculo($data){
       include 'config.php';
       $c8 = $data['c8']; //fecha_ingreso
       $c9 = $data['c9']; //fecha_baja
       $c11 = $data['c11'];//salario_diario
       $c19 = $data['c19']; //dias aguinaldo
       $c13 = $data['c13']; //saldo vacaciones anios anteriores
       $c14 = $data['c14']; //dias pendientes de pago ordinario
       $c16 = $data['c16']; //area geografica
       $c21 = $data['c21']; //prima vacacional
       $c17 = $salario_minimo[$c16]; //Salario minimo  
       $result['c17'] = $c17;
       $c15 = $data['c15']; //calcular?
       $c10 = edad($c8, $c9);//antiguedad
       $result['c10'] = $c10;
       $c12 = round($c11*$salario_diario_const,3); //salario diario integrado
       $result['c12'] = $c12; 
       $c20 = vacations(ceil($c10));//dias vacaciones
       $result['c20'] = $c20; 
       $d19 = round(proporcional($c8, $c9)*$c19,2); //dias aguinaldo proporcionales
       $d20 = round(proporcional2($c8, $c9)*$c20,2);  // dias vacaciones proporcionales
       $result['d19'] = $d19;
       $result['d20'] = $d20;  
       $c25 = round($c11*$d19,2); //aguinaldo percepciones
       $result['c25'] = $c25;
       $d25 = min($c25,round($c17*30,2)); // aguinaldo exentos
       $result['d25'] = $d25;
       $c26 = round($c11*($d20 + $c13),2); //vacaciones percepciones
       $result['c26'] = $c26;
       $c27 = round($c26*($c21/100),2); // prima vacacional percepciones
       $result['c27'] = $c27;
       $d27 = min($c27,round($c17*15,2)); //prima vacacional excentos
       $result['d27'] = $d27;
       $c28 = round($c14 * $c11,2); // ordinario pendiente
       $result['c28'] = $c28;
       $c29 = $c25 + $c26 + $c27 + $c28; //subtotal finiquito
       $result['c29'] = $c29; 
       $d29 =  $d25 +  $d27; //subtotal finiquito excentos
       $result['d29'] = $d29;
       $c31 = 0;
       $c32 = 0;
       $c33 = 0;
       if ($c15!= 1)
           $c31 = $c11 * 90; //tres meses de salario
       if ($c15!= 1 || $c10 >= 15) 
           $c32 = $c10 * 12 * min($c12,$c17*2);//prima de antiguedad
       if ($c15 == 3)
           $c33 = 20*$c12*$c10; //20 dias por anio de servicio
       $c34 = $c31 + $c32 + $c33; //subtotal liquidacion
       $d34 = min($c34,round(90*$c17*round($c10,0),2)); //subtotal liquidacion extentos
       $c36 = $c29 + $c34; //total percepciones
       $d36 = $d29 + $d34; //total percepciones exentos
       $result['c31'] = $c31; 
       $result['c32'] = $c32;
       $result['c33'] = $c33;
       $result['c34'] = $c34;
       $result['d34'] = $d34;
       $result['c36'] = $c36;
       $result['d36'] = $d36;                                  
       $l88 = $c26 + $c28;//vacaciones y pendientes
       $l89 = $d20 + $c13 + $c14; // dias de vacaciones y pendientes

       //Impuesto liquidacion
       $l30 = $c36 - $d36 - $l88 ; // gravado 
       $l31 = round($l30/365*$gravado_men_const,2);//gravado mensualizado
       $l32 = $c11 * $gravado_men_const;//salario mensual
       $l33 = $l31 + $l32;  // promedio mensual 
       
       //impuesto de salario mensualtabla art 113
       $o35 = search($l32,$matriz_art113,1);//limite inferior
       $o36 = $l32 - $o35; //excednte
       $o37 = search($l32,$matriz_art113,4);//tasa
       $o38 = $o37 * $o36; //impuesto marginal
       $o39 = search($l32,$matriz_art113,3);//cuota fija
       $o40 = $o38 + $o39; //isr promedio mensual
       
       //impuesto de promedio mensual
       $o44 = search($l33,$matriz_art113,1);//cuota fija
       $o45 = $l33 - $o44; //excedente
       $o46 = search($l33,$matriz_art113,4);//tasa
       $o47 = $o45 * $o46;//impuesto marginal
       $o48 = search($l33,$matriz_art113,3);//cuota fija
       $o49 = $o47 + $o48; // isr promedio mensual
       
       //impuesto liquidacion
       $l34 = $o40; //impuesto de salario
       $l35 = $o49; //impuesto de promedio
       $l36 = max($l35-$l34,0); //diferencia de impuestos
       if ($l31<=0) 
          $l37 = 0;
       else 
          $l37 = $l36/$l31; // proporcion
       $l38 = round($l30*$l37,2); //impuesto    
 
       $l92 = $l88;//percepciones gravadas         
       $l93 = max($l89,1);//dias de periodo de pago
       $l94 = $gravado_men_const;//factor mensual;
       $l95 = round($l92/$l93*$l94,2);//gravadas promedio mensual 
       // tabla articulo 113
       $l97 = search($l95,$matriz_art113,1);//limite inferior
       $l98 = $l95-$l97; //excedente
       $l99 = search($l95,$matriz_art113,4);//tasa;  
       $l100=$l98 * $l99; // impuesto marginal
       $l101= search($l95,$matriz_art113,3);//cuota fija 
       $l102= $l100 + $l101; //ISR promedio mensual
       $l104= $gravado_men_const;//factor mensual
       $l105 = $l89;//dias de periodo de pago
       $l106 = round($l102/$l104*$l105,2);//ISR del periodo
       
       //subsidio al empleado
       $l109 = $l95;//gravadas promedio mensual 
       if ($l88 <= 0)
            $l111 = 0;
       else
            $l111 = search($l88,$matriz_subemp,3);//tabla subsidio al empleado
       $l113 = $gravado_men_const; //factor mensual
       $l114 = $l89;//dias de periodo de pago
       $l115 = round($l111/$l113*$l114,2); // 

       $c38 = $l38; //isr de pagos por separacion
       $c39 = $l106;//isr de vacaciones y ordinarios
       $c40 = $l115;//  SUBE de vacaciones y ordinarios
       
       $c42 = $c36-$c38-$c39+$c40;//neto a recibir      
       $result['c38'] = $c38;
       $result['c39'] = $c39;
       $result['c40'] = $c40;
       $result['c42'] = $c42;                  
       return $result;
}

?>

