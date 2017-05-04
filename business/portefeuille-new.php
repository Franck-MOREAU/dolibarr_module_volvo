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
require_once DOL_DOCUMENT_ROOT . '/volvo/class/table_template.class.php';

$object = new Leadext($db);



// Security check
if (! $user->rights->volvo->port)
	accessforbidden();


$table = new Dyntable($db);

$table->title = 'Suivis d\'activité VN volvo';

$field= new Dyntable_fields($db);
$field->name='comm';
$field->label = 'Commercial';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'comm';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dossier';
$field->label = 'Dossier';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'com.ref';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='om';
$field->label = 'N° O.M.';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'ef.numom';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='client';
$field->label = 'Client';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'socnom';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dt_cmd';
$field->label = 'Date de Commande';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'cf.date_commande';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dt_liv';
$field->label = 'Date de livraison';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'com.date_livraison';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dt_liv_usi';
$field->label = 'Date de sortie d\'usine';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'dt_sortie';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vin';
$field->label = 'N° de Chassis';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'ef.vin';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='mois';
$field->label = 'Mois';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'dt_sortie';
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='genre';
$field->label = 'genre';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='type';
$field->label = 'type';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='sil';
$field->label = 'sil';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='pv';
$field->label = 'Prix de vente';
$field->checked = 1;
$field->sub_title = 0;
$field->align = 'center';
$table->arrayfields[$field->name] = $field;

$tools =array();

$tool = new Dyntable_tools($db);
$tool->type = 'select_year';
$tool->title = 'Année: ';
$tool->value = $year;
$tool->html_name = 'year';
$tool->use_empty = 0;
$tool->min_year = 5;
$tool->max_year = 0;
$tool->default = dol_print_date(dol_now(),'%Y');
$tool->filter = 'YEAR_IN';
$tools['1'] = $tool;

$tool = new Dyntable_tools($db);
$tool->type = 'select_user';
$tool->title = 'Commercial: ';
$tool->value = $search_commercial;
$tool->html_name = 'search_commercial';
$tool->use_empty = 1;
$tool->see_all = $user->rights->volvo->stat_all;
$tool->limit_to_group = '1';
$tool->filter = 'lead.fk_user_resp';
$tools['2'] = $tool;

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

$tool = new Dyntable_tools($db);
$tool->type = 'select_array';
$tool->title = 'Periode: ';
$tool->value = $search_periode;
$tool->html_name = 'search_periode';
$tool->use_empty = 1;
$tool->array = $periodarray;
$tool->value = $search_periode;
$tool->filter = 'MONTH_IN';
$tools['3'] = $tool;

$table->extra_tools =$tools;
$table->sortorder = GETPOST('sortorder', 'alpha');
$table->sortfield = GETPOST('sortfield', 'alpha');
$table->page = GETPOST('page', 'int');

$table->offset = ($conf->liste_limit+1) * $page;

if (empty($table->sortorder))
	$table->sortorder = "ASC";
if (empty($sortfield))
	$table->sortfield = "dt_sortie";

$table->export_name = 'portefeuille';
$table->context = 'portefeuille';
$table->search_button = 1;
$table->remove_filter_button = 1;
$table->export_button = 1;
$table->select_fields_button = 1;
$table->mode = 'object_methode';
$table->include = '/volvo/class/lead.extend.class.php';
$table->object = 'Leadext';
$table->result = 'business';
$table->limit = $conf->liste_limit;
$table->method = 'fetchAllfolow';
$table->param0 = 'sortorder';
$table->param1 = 'sortfield';
$table->param2 = 'limit';
$table->param3 = 'offset';
$table->param4 = 'filter';
$table->filter = array();
$table->filter['PORT'] = 1;

$table->header();

$table->draw_tool_bar();

$table->draw_table_head();

$table->data_array();

$table->end_table();







