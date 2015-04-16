<?php
session_start();
error_reporting(E_ALL);
include("PFBC/Form.php");
if($_POST and isset($_POST["form"])) {
        if(Form::isValid($_POST["form"])) {
                include("action.php");
                $result = calculo($_POST);
		$jsondata['data']=$result;
                $jsondata['status']='OK';
                $json = json_encode($jsondata);
                echo $json;
	}
	else
		Form::renderAjaxErrorResponse($_POST["form"]);
	exit();

}	
include("header.php");
?>

<h1>C&aacute;lculo de Finiquito/Liquidaci&oacute;n</h1>
 <div id='content'>
<h1>Datos del Empleado</h1>
	
<?php
$form = new Form("ajax", 400);
$form->configure(array(
	"ajax" => 1,
	"ajaxCallback" => "parseJSONResponse",
        "view" => new View_Grid(array(2, 1, 2,2,1,2,2,2,1,2,1,2,1,2,1,1,1,2,2)),
        "resourcesPath" => 'PFBC/Resources',
        "prevent" => array('focus'),

    ));
$form->addElement(new Element_Hidden("form", "ajax"));

$form->addElement(new Element_Date("Fecha Ingreso:", "c8", array(
	"class" => "texto",
        "validation" => new Validation_Date,
        "required" => 1,
        "jQueryOptions" => array('changeYear'=>true,'changeMonth'=>true),  
)));
$form->addElement(new Element_Date("Fecha Baja:", "c9", array(
        "required" => 1,
        "validation" => new Validation_Date,
	"class" => "texto",
        "jQueryOptions" => array('changeYear'=>true,'changeMonth'=>true),  
)));
$form->addElement(new Element_Textbox("Antiguedad (Años):", "c10", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Salario Diario:", "c11", array(
        "validation" => new Validation_Numeric,
        "required" => 1,
	"class" => "texto"
)));
$form->addElement(new Element_Textbox("Salario Diario Integrado:", "c12", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_Textbox("Saldo vacaciones años anteriores (Dias):", "c13", array(
        "validation" => new Validation_Numeric,
        "required" => 1,
	"class" => "texto"
)));

$form->addElement(new Element_Textbox("Dias pendientes de pago ordinario(Dias):", "c14", array(
        "validation" => new Validation_Numeric,
        "required" => 1,
	"class" => "texto"
)));
$options_1 = array('1'=>'Solo finiquito','2'=>'Liquidación','3'=>'Liquidación + 20 días');

$options_g = array('a'=>'A','b'=>'B','c'=>'C');
$form->addElement(new Element_Select("Calcular:", "c15", $options_1,
          array("class" => "texto")
        ));
$form->addElement(new Element_Select("Área geografica:", "c16", $options_g,
          array("class" => "texto")
        ));

$form->addElement(new Element_Textbox("Salario minimo:", "c17", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_HTMLExternal('<h1>Prestaciones</h1>'));

$form->addElement(new Element_Textbox("Dias de aguinaldo:", "c19", array(
        "validation" => new Validation_Numeric,
        "required" => 1,
	"class" => "texto"
)));

$form->addElement(new Element_Textbox("Proporcionales:", "d19", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Dias Vacaciones:", "c20", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("&nbsp;", "d20", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_Textbox("Prima vacacional (%):", "c21", array(
        "validation" => new Validation_Numeric,
        "required" => 1,
	"class" => "texto"
)));
$form->addElement(new Element_Button('Enviar'));

$form->addElement(new Element_HTMLExternal('<h1>Finiquito</h1>'));
$form->addElement(new Element_HTMLExternal('<div style="float:left;"><h2>Percepciones</h2></div>'));
$form->addElement(new Element_HTMLExternal('<div style="float:left; padding-left:150px;"><h2>Exentos</h2></div>'));
$form->addElement(new Element_HTMLExternal('<div style="clear:both;"></div>'));

$form->addElement(new Element_Textbox("Aguinaldo:", "c25", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_Textbox("&nbsp;", "d25", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Vacaciones:", "c26", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Prima Vacacional:", "c27", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("&nbsp;", "d27", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Ordinario Pendiente:", "c28", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Subtotal Finiquito:", "c29", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("&nbsp;", "d29", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_HTMLExternal('<h1>Liquidacion</h1>'));
$form->addElement(new Element_Textbox("Tres meses de salario", "c31", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Prima de antigüedad", "c32", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("20 Días por año de servicio", "c33", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("Subtotal Liquidación", "c34", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("&nbsp;", "d34", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_HTMLExternal('<div style="height:30px;"></div>'));
$form->addElement(new Element_Textbox("Total de percepciones", "c36", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("&nbsp;", "d36", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_HTMLExternal('<div style="height:30px;"></div>'));
$form->addElement(new Element_Textbox("(-) ISR de pagos por separación:", "c38", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->addElement(new Element_Textbox("(-) ISR de Vacaciones y Ordinario:", "c39", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_Textbox("(+) SUBE de Vacaciones y Ordinario:", "c40", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));

$form->addElement(new Element_HTMLExternal('<div style="height:30px;"></div>'));
$form->addElement(new Element_Textbox("Neto a Recibir:", "c42", array(
        "readonly" => "readonly",
        "class" => "texto_readonly" 
       )));
$form->render();

?>

<script type="text/javascript">
	function parseJSONResponse(data) {
                
		var form = document.getElementById("ajax");
		if(data.status == "OK") {
			var result = data.data;
			form.c10.value = result.c10;
                        form.c12.value = result.c12;
                        form.c17.value = result.c17;
                        form.c20.value = result.c20;
                        form.d19.value = result.d19;
                        form.d20.value = result.d20;
                        form.c25.value = result.c25;        
                        form.d25.value = result.d25;
                        form.c26.value = result.c26;
                        form.c27.value = result.c27;
                        form.d27.value = result.d27;
                        form.c28.value = result.c28; 
                        form.c29.value = result.c29;
                        form.d29.value = result.d29;
                        form.c31.value = result.c31;
                        form.c32.value = result.c32;
                        form.c33.value = result.c33;      
                        form.c34.value = result.c34;
                        form.d34.value = result.d34;
                        form.c36.value = result.c36;
                        form.d36.value = result.d36;
                        form.c38.value = result.c38;
                        form.c39.value = result.c39;
                        form.c40.value = result.c40;                                 
                        form.c42.value = result.c42;                                   
           }
	}
$(document).ready(function(){
   $('#ajax-element-3').focus();
});

</script>
  

</div>
<?php

include("footer.php");

?>

