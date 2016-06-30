<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exponencialis_egyenlet2 {

	// Class constructor
	function __construct() {

		$CI =& get_instance();
		$CI->load->helper('maths');
		$CI->load->helper('language');
		$CI->load->helper('draw');

		return;
	}

	function Generate($level) {

		$start 	= rand(4*$level, 5*$level)*100;
		$end	= rand(1*$level, 3*$level)*100;
		$rate 	= rand(75, 95)/100;
		$x		= (log($end)/log(10)-log($start)/log(10))/(log($rate)/log(10));
		$correct = 2014+ceil($x);
		
		$question = 'Egy $2014$ végén készült előrejelzés szerint az Indiában élő tigrisek $t$ száma az elkövetkező években (az egyes évek végén) megközelítőleg a következő összefüggés szerint alakul: $t(x) = '.$start.' \cdot '.round2($rate).'^x$, ahol $x$ a $2014$ óta eltelt évek számát jelöli. Melyik évben várható, hogy a tigrisek száma $'.$end.'$ alá csökken?';

		$page[] = 'A feladat lényegében azt kérdi, hogy melyik az az $x$, amit ha behelyettesítünk a képletbe, $'.$end.'$-'.By($end).' kisebb lesz az eredmény.';
		$page[] = 'Írjuk fel ezt az egyenlőtlenséget:$$'.$start.'\cdot '.round2($rate).'^x<'.$end.'$$';
		$page[] = 'Osszuk el mindkét oldalt $'.$start.'$-'.With($start).':$$'.round2($rate).'^x<\frac{'.$end.'}{'.$start.'}$$';
		$page[] = 'Vegyük mindkét oldal $10$-es alapú logaritmusát:$$\lg\left('.round2($rate).'^x\right)<\lg\left(\frac{'.$end.'}{'.$start.'}\right)$$';
		$hints[] = $page;

		$page = [];
		$page[] = '<div class="alert alert-info"><strong>Logaritmus azonosság:</strong><br />Egy hányados logaritmusát úgy kapjuk meg, hogy a számláló logaritmusából kivonjuk a nevező logaritmusát:$$\lg\left(\frac{a}{b}\right)=\lg a - \lg b$$</div>';
		$page[] = 'A fenti azonosságot felhasználva írjuk a jobb oldalt:$$x\cdot\lg\left('.round2($rate).'\right)<\lg'.$end.'-\lg'.$start.'$$';
		$hints[] = $page;

		$page = [];
		$page[] = '<div class="alert alert-info"><strong>Logaritmus azonosság:</strong><br />Ha egy hatványnak vesszük a logaritmusát, akkor a kitevőt a kifejezés elé írhatjuk:$$\lg\left(a^b\right)=b\cdot\lg a$$</div>';
		$page[] = 'A fenti azonosságot felhasználva írjuk az $x$-et a bal oldali kifejezés elé:$$x\cdot\lg'.round2($rate).'<\lg'.$end.'-\lg'.$start.'$$';
		$page[] = 'Osszuk el mindkét oldalt $\lg'.round2($rate).'$-'.With($rate).'!<div class="alert alert-danger"><b>Figyelem!</b><br />Az $1$-nél kisebb számok logaritmusa <b>negatív</b> lesz, ezért a relációs jel iránya megfordul!$$x>\frac{\lg'.$end.'-\lg'.$start.'}{\lg'.round2($rate).'}\approx{'.round2($x).'}$$</div>';
		$page[] = 'A fenti kifejezés azt jelenti, hogy a $2014$ után legalább $'.ceil($x).'$ évnek kell eltelnie ahhoz, hogy a tigrisek száma $'.$end.'$ alá csökkenjen, vagyis a megoldás $2014+'.ceil($x).'=$<span class="label label-success">$'.$correct.'$<span>.';
		$hints[] = $page;

		$solution = '$'.$correct.'$';

		return array(
			'question'  => $question,
			'correct'   => $correct,
			'solution'  => $solution,
			'hints'		=> $hints
		);
	}
}

?>