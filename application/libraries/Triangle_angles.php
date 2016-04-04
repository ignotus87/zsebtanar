<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Triangle_angles {

	// Class constructor
	function __construct() {

		$CI =& get_instance();
		$CI->load->helper('maths');
		$CI->load->helper('language');
		$CI->load->helper('draw');
		
		return;
	}

	// Define triangle angle based on two given angles
	function Generate($level) {

		// Define random angles
		$angles = $this->GetAngles($level);

		// Define angle type
		$options 	= array('belső', 'külső');
		$types[0] 	= $options[rand(0,1)];
		$types[1] 	= $options[rand(0,1)];
		$types[2] 	= $options[rand(0,1)];

		$question = 'Egy $ABC$ háromszög $A$ csúcsnál lévő <b>'.$types[0].'</b> szöge $'
			.($types[0] == 'belső' ? $angles[0] : strval(180-$angles[0])).'°$-os, '
			.'$B$ csúcsnál lévő <b>'.$types[1].'</b> szöge $'
			.($types[1] == 'belső' ? $angles[1] : strval(180-$angles[1])).'°$-os. '
			.'Hány fokos a háromszög $C$ csúcsnál lévő <b>'.$types[2].'</b> szöge?';

		$correct 	= ($types[2] == 'belső' ? $angles[2] : 180-$angles[2]);
		$solution 	= '$'.$correct.'°$-os';
		$hints 		= $this->Hints($angles, $types);

		return array(
			'question' 	=> $question,
			'correct' 	=> $correct,
			'solution'	=> $solution,
			'hints'		=> $hints
		);
	}

	// Define random angles
	function GetAngles($level) {

		if ($level <= 3) { // 30°, 60°, 90° ...

			$num1 = rand(1,4);
			$num2 = rand(1,5-$num1);
			$num3 = 6-($num1+$num2);
			$angles[0] = $num1 * 30;
			$angles[1] = $num2 * 30;
			$angles[2] = $num3 * 30;

		} elseif ($level <= 6) { // 10°, 20°, 30° ...

			$num1 = rand(1,16);
			$num2 = rand(1,17-$num1);
			$num3 = 18-($num1+$num2);
			$angles[0] = $num1 * 10;
			$angles[1] = $num2 * 10;
			$angles[2] = $num3 * 10;

		} else { // 1°, 2°, 3° ...

			$num1 = rand(1,178);
			$num2 = rand(1,179-$num1);
			$num3 = 180-($num1+$num2);
			$angles[0] = $num1;
			$angles[1] = $num2;
			$angles[2] = $num3;

		}
		$angles[0] = 120;
		$angles[1] = 30;
		$angles[2] = 30;
		return $angles;
	}

	function Hints($angles, $types) {

		$nodes = ['A', 'B', 'C'];

		$svg[] = 'Rajzoljunk egy háromszöget! (Nem fontos, hogy valósághű legyen.)';
		$svg[] = $this->DrawTriangle();

		$hints[] = $svg;
		$svg = [];

		$outer0 = 180-$angles[0];
		$svg[] = 'Az $A$ csúcsnál lévő <b>'.$types[0].'</b> szög $'.($types[0]=='belső' ? $angles[0] : $outer0).'°$.';		
		if ($types[0] == 'belső') {
			$svg[] = 'Jelöljük ezt az ábrán:';
			$svg[] = $this->DrawTriangle($angles[0]);
		} else {
			$svg[] = 'Tudjuk, hogy a külső és belső szög összege $180°$, ezért a <b>belső</b> szög $180°-'.$outer0.'°='.$angles[0].'°$. Jelöljük ezt az ábrán:';
			$svg[] = $this->DrawTriangle($angles[0], $outer0);
		}

		$hints[] = $svg;
		$svg = [];
		
		$outer1 = 180-$angles[1];
		$svg[] = 'A $B$ csúcsnál lévő <b>'.$types[1].'</b> szög $'.($types[1]=='belső' ? $angles[1] : $outer1).'°$.';
		if ($types[1] == 'belső') {
			$svg[] = 'Jelöljük ezt az ábrán:';
			$svg[] = $this->DrawTriangle($angles[0], 0, $angles[1]);
		} else {
			$svg[] = 'Tudjuk, hogy a külső és belső szög összege $180°$, ezért a <b>belső</b> szög $180°-'.$outer1.'°='.$angles[1].'°$. Jelöljük ezt az ábrán:';
			$svg[] = $this->DrawTriangle($angles[0], 0, $angles[1], $outer1);
		}

		$hints[] = $svg;
		$svg = [];

		$svg[] = 'Tudjuk, hogy a háromszög belső szögeinek összege $180°$. Ezért a $C$ csúcsnál lévő <b>belső</b> szöget a következőképpen számolhatjuk ki:$$180°-'.$angles[0].'°-'.$angles[1].'°='.$angles[2].'°$$';
		$svg[] = 'Jelöljük ezt az ábrán:';
		$svg[] = $this->DrawTriangle($angles[0], 0, $angles[1], 0, $angles[2]);

		$hints[] = $svg;
		$svg = [];
		
		if ($types[2] == 'belső') {

			$svg[] = 'Tehát a $C$ csúcs <b>belső</b> szöge <span class="label label-success">$'.$angles[2].'°$</span>.';

			$hints[] = $svg;
			$svg = [];

		} else {

			$outer2 = 180-$angles[2];
			$svg[] = 'Tudjuk, hogy a külső és belső szög összege $180°$, ezért a $C$ csúcsnál lévő <b>külső</b> szög $180°-'.$outer2.'°='.$angles[2].'°$. Jelöljük ezt az ábrán:';
			$svg[] = $this->DrawTriangle($angles[0], 0, $angles[1], 0, $angles[2], $outer2);
			$hints[] = $svg;
			$svg = [];

			$svg[] = 'Tehát a $C$ csúcs <b>külső</b> szöge <span class="label label-success">$'.$outer2.'°$</span>.';
			$hints[] = $svg;
			$svg = [];
		}

		return $hints;
	}

	// Draw triangle (svg)
	function DrawTriangle($Ca=NULL,$Caa=NULL,$Cb=NULL,$Cbb=NULL,$Cc=NULL,$Ccc=NULL) { // captions (c) for inner (a) and outer (aa) angles

		$width 		= 400;
		$height 	= 300;
		$color1 	= '#F2F2F2';
		$color2 	= 'black';
		$padding	= 30;

		$arc_length = 70;

		$arc_radius_inner 	= 40;
		$arc_radius_outer1 	= 37;
		$arc_radius_outer2 	= 43;

		// Outer points
		$AAx = $padding;
		$AAy = $height - $padding;

		$BBx = $width - $padding;
		$BBy = $height - $padding;

		$CCx = $width * 3/4;
		$CCy = $padding;

		// Inner points
		$Ax = $AAx + $arc_length;
		$Ay = $AAy;

		$Bx = $BBx - $arc_length;
		$By = $BBy;

		$ratio = 0.77;
		$Cx = $Ax + ($CCx - $Ax) * $ratio;
		$Cy = $CCy + ($Ay - $CCy) * (1-$ratio);

		$svg = '<div class="img-question text-center">
					<svg width="'.$width.'" height="'.$height.'">';
					
		// $svg .= '<rect width="'.$width.'" height="'.$height.'" fill="black" fill-opacity="0.2" />';

		$svg .= DrawLine($Ax, $Ay, $Bx, $By);
		$svg .= DrawLine($Ax, $Ay, $Cx, $Cy);
		$svg .= DrawLine($Bx, $By, $Cx, $Cy);

		// Nodes
		$svg .= DrawText($Ax, $height-5, '$A$', 15);
		$svg .= DrawText($Bx, $height-5, '$B$', 15);
		$svg .= DrawText($Cx-10, $Cy-5, '$C$', 15);

		// Arc
		if ($Ca) { // caption for A inner
			$svg .= DrawArc($Ax, $Ay, $Bx, $By, $Cx, $Cy, $arc_radius_inner, 25, 0, $Ca);
		}
		if ($Caa) { // caption for A outer
			$svg .= DrawLine($Ax, $Ay, $AAx, $AAy);
			$svg .= DrawArc($Ax, $Ay, $Cx, $Cy, $AAx, $AAy, $arc_radius_outer1);
			$svg .= DrawArc($Ax, $Ay, $Cx, $Cy, $AAx, $AAy, $arc_radius_outer2, 5, 10, $Caa);
		}
		if ($Cb) { // caption for B inner
			$svg .= DrawArc($Bx, $By, $Cx, $Cy, $Ax, $Ay, $arc_radius_inner, 25, 0, $Cb);
		}
		if ($Cbb) { // caption for B outer
			$svg .= DrawLine($Bx, $By, $BBx, $BBy);
			$svg .= DrawArc($Bx, $By, $BBx, $BBy, $Cx, $Cy, $arc_radius_outer1);
			$svg .= DrawArc($Bx, $By, $BBx, $BBy, $Cx, $Cy, $arc_radius_outer2, 25, 10, $Cbb);
		}
		if ($Cc) { // caption for C inner
			$svg .= DrawArc($Cx, $Cy, $Ax, $Ay, $Bx, $By, $arc_radius_inner, 25, 20, $Cc);
		}
		if ($Ccc) { // caption for C outer
			$svg .= DrawLine($Cx, $Cy, $CCx, $CCy);
			$svg .= DrawArc($Cx, $Cy, $Bx, $By, $CCx, $CCy, $arc_radius_outer1);
			$svg .= DrawArc($Cx, $Cy, $Bx, $By, $CCx, $CCy, $arc_radius_outer2, 25, 5, $Ccc);
		}

		$svg .= '</svg></div>';

		return $svg;
	}
}

?>