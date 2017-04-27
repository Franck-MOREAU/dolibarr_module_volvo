<?php
/*
 * Copyright (C) 2014 Florian HENRY <florian.henry@open-concept.pro>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/volvo/class/lead.extend.class.php';
$object = new Leadext($db);

$title = 'Suivis d\'activité VN volvo';

// Security check
if (! $user->rights->volvo->activite)
	accessforbidden();

// Search criteria
$search_commercial = GETPOST("search_commercial", 'int');
$search_periode = GETPOST("search_periode");
$year = GETPOST('year');
$sortorder = GETPOST('sortorder', 'alpha');
$sortfield = GETPOST('sortfield', 'alpha');
$page = GETPOST('page', 'int');


// Do we click on purge search criteria ?
if (GETPOST("button_removefilter_x")) {
 	$search_commercial = '';
 	$search_periode = '';
 	$year = dol_print_date(dol_now(),'%Y');
}

$search_commercial_disabled = 0;
if (empty($user->rights->volvo->stat_all)){
	$search_commercial = $user->id;
	$search_commercial_disabled = 1;
}

$user_included=array();
$sqlusers = "SELECT fk_user FROM " . MAIN_DB_PREFIX . "usergroup_user WHERE fk_usergroup = 1";
$resqlusers  = $db->query($sqlusers);
if($resqlusers){
	while ($users = $db->fetch_object($resqlusers)){
		$user_included[] = $users->fk_user;
	}
}

if(empty($year)) $year = dol_print_date(dol_now(),'%Y');

$var = true;

if(!empty($search_periode)){
	switch($search_periode){
		case 13:
			$monthlist = '1,2,3';
			break;
		case 14:
			$monthlist = '4,5,6';
			break;
		case 15:
			$monthlist = '7,8,9';
			break;
		case 16:
			$monthlist = '10,11,12';
			break;
		case 17:
			$monthlist = '1,2,3,4,5,6';
			break;
		case 18:
			$monthlist = '7,8,9,10,11,12';
			break;
		default:
			$monthlist = $search_periode;
	}
}

$filter = array();
$filter['PORT'] = 1;

if (! empty($search_periode)) {
	$filter['MONTH_IN'] = $monthlist;
	$option .= '&search_periode=' . $search_periode;
}
if (! empty($search_commercial) && $search_commercial != -1) {
	$filter['lead.fk_user_resp'] = $search_commercial;
	$option .= '&search_commercial=' . $search_commercial;
}

if (! empty($year)) {
	$filter['YEAR_IN'] = $year;
	$option .= '&year=' . $year;
}

$offset = ($conf->liste_limit+1) * $page;

if (empty($sortorder))
	$sortorder = "ASC";
if (empty($sortfield))
	$sortfield = "dt_sortie";

$nbtotalofrecords = 0;
$array_display=array();

$nbtotalofrecords = $object->fetchAllfolow('', '', 0, 0, $filter);
$nbtotalofrecords = count($object->business);

$resql = $object->fetchAllfolow($sortorder, $sortfield, $conf->liste_limit, $offset, $filter);
print $resql;
if ($resql != - 1) {
	$num = $resql;
	$var = true;

	foreach ($object->business as $line) {
		$array_display[]=array(
				'class' => $bc[$var],
				'class_td' => '',
				'comm' => $line->comm,
				'dossier' => $line->commande,
				'om' => $line->numom,
				'client' => $line->socnom,
				'dt_cmd' => dol_print_date($line->dt_env_usi,'day'),
				'dt_liv' => dol_print_date($line->dt_liv_dem_cli,'day'),
				'dt_liv_usi' => dol_print_date($line->dt_sortie,'day'),
				'vin' => substr($line->vin, -7),
				'mois' => dol_print_date($line->dt_sortie,'%m'),
				'type' => $line->gamme,
				'genre' => $line->genre,
				'sil' => $line->silouhette,
				'pv' => price($line->pv)
		);
	}
}



$arrayfields=array(
		'comm'=>array(
				'label'=>'Commercial',
				'checked'=>1,
				'sub_title'=>0,
				'field' => 'comm',
				'align'=>'center'
		),
		'dossier'=>array(
				'label'=>'Dossier',
				'checked'=>1,
				'sub_title'=>0,
				'field'=> 'com.ref',
				'align'=>'center'
		),
		'om'=>array(
				'label'=>'N° O.M.',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'ef.numom',
				'align'=>'center'
		),
		'client'=>array(
				'label'=>'Client',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'socnom',
				'align'=>'center'
		),
		'dt_cmd'=>array(
				'label'=>'Date de Commande',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'cf.date_commande',
				'align'=>'center'
		),
		'dt_liv'=>array(
				'label'=>'Date de livraison',
				'checked'=>1,
				'sub_title'=>0,
				'field' => 'com.date_livraison',
				'align'=>'center'
		),
		'dt_liv_usi'=>array(
				'label'=>'Date de sortie d\'usine',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'dt_sortie',
				'align'=>'center'
		),
		'vin'=>array(
				'label'=>'N° de Chassis',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'ef.vin',
				'align'=>'center'
		),
		'mois'=>array(
				'label'=>'Mois',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'dt_sortie',
				'align'=>'center'
		),
		'type'=>array(
				'label'=>'type',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'genre'=>array(
				'label'=>'genre',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'sil'=>array(
				'label'=>'silouhette',
				'checked'=>1,
				'sub_title'=>0,
				'align'=>'center'
		),
		'pv'=>array(
				'label'=>'Prix de vente',
				'checked'=>1,
				'sub_title'=>0,
				'field'=>'com.total_ht',
				'unit' => '€',
				'align'=>'center'
		),

);

$periodarray= array(
		1 => 'Janvier',
		2 => 'Fevrier',
		3 => 'Mars',
		4 => 'Avril',
		5=> 'Mai',
		6=> 'Juin',
		7 => 'Juillet',
		8 => 'Aout',
		9 => 'Septembre',
		10 => 'Octobre',
		11 => 'Novembre',
		12 => 'Décembre',
		13=>'1er Trimestre',
		14=> '2eme Trimestre',
		15=>'3eme Trimestre',
		16=>'4eme Trimestre',
		17=>'1er Semestre',
		18=>'2eme Semestre'

);


$extra_tools=array(
		1 => array(
				'type' => 'select_year',
				'title' => 'Année: ',
				'value' => $year,
				'html_name' => 'year',
				'use_empty' => 0,
				'min_year' => 5,
				'max_year' => 0
		),
		2 => array(
				'type' => 'select_user',
				'title' => 'Commercial: ',
				'value' => $search_commercial,
				'html_name' => 'search_commercial',
				'use_empty' => 1,
				'disabled' => $search_commercial_disabled,
				'excluded' => array(),
				'included' => $user_included
		),
		3 => array(
				'type' => 'select_array',
				'title' => 'Periode: ',
				'value' => $search_periode,
				'html_name' => 'search_periode',
				'use_empty' => 1,
				'array' => $periodarray,
				'value' => $search_periode,
		)
);

$tools=array(
		'search_button' => 1,
		'remove_filter_button' => 1,
		'export_button' => 1,
		'select_fields_button' => 1,
		'extra _tools' => $extra_tools
);

$list_config=array(
		'title' =>	 'Suivis d\'activité VN volvo',
		'sortfield' => $sortfield,
		'sortorder' => $sortorder,
		'page' => $page,
		'num' => $num,
		'nbtotalofrecords' => $nbtotalofrecords,
		'option' => $option,
		'tools_active' =>1,
		'tools' => $tools,
		'array_fields' => $arrayfields,
		'array_data' => $array_display,
		'export_name' => 'portefeuille',
		'context' => 'portefeuille',
);

dol_include_once('/volvo/class/table_template.php');





