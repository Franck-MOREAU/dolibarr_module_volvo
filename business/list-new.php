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

require_once DOL_DOCUMENT_ROOT . '/volvo/class/table_template.class.php';


// Security check
if (! $user->rights->volvo->business)
	accessforbidden();

$table = new Dyntable($db);
$table->title = 'Suivis des affaires en cours';
$table->default_sortfield = 'cf.date_livraison';
$table->export_name = 'suivi_business_new';
$table->context = 'suivi_business';
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
$table->param5 = 'filter_mode';
$table->filter_mode = 'AND';
$table->filter_line = 1;

$field= new Dyntable_fields($db);
$field->name='comm';
$field->label = 'Commercial';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'comm';
$field->align = 'left';
$field->alias = 'comm';
$field->post_traitement = array('link', '/user/card.php','?id=','commercial');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'select_user';
$tool->title = '';
$tool->html_name = 'search_commercial';
$tool->filter = 'lead.fk_user_resp';
$tool->use_empty = 1;
$tool->see_all = $user->rights->volvo->stat_all;
$tool->default = $user->id;
$tool->limit_to_group = '1';
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='om';
$field->label = 'N° O.M.';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'ef.numom';
$field->align = 'center';
$field->alias = 'numom';
$field->post_traitement = array('link', '/fourn/commande/card.php','?id=','fournid');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_numom';
$tool->filter = 'ef.numom';
$tool->size = 5;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='dossier';
$field->label = 'Dossier';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'com.ref';
$field->align = 'center';
$field->alias = 'commande';
$field->post_traitement = array('link', '/volvo/commande/card.php','?id=','com');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_ana';
$tool->filter = 'com.ref';
$tool->size = 3;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='affaire';
$field->label = 'Affaire';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'lead.ref';
$field->align = 'center';
$field->alias = 'leadref';
$field->post_traitement = array('link', '/custom/lead/lead/card.php','?id=','lead');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_lead';
$tool->filter = 'lead.ref';
$tool->size = 6;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='client';
$field->label = 'Client';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'soc.nom';
$field->align = 'center';
$field->alias = 'socnom';
$field->post_traitement = array('link', '/societe/soc.php','?socid=','societe');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_client';
$tool->filter = 'soc.nom';
$tool->size = 26;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vin';
$field->label = 'N° de Chassis';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'ef.vin';
$field->align = 'center';
$field->alias = 'vin';
$field->post_traitement = array('substr', -7,2000);
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_vin';
$tool->filter = 'ef.vin';
$tool->size = 4;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='immat';
$field->label = 'Immat';
$field->checked = 1;
$field->sub_title = 1;
$field->field = 'ef.immat';
$field->align = 'center';
$field->alias = 'immat';
$field->post_traitement = array('none');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_immat';
$tool->filter = 'ef.immat';
$tool->size = 5;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;






$tools =array();
$tool = new Dyntable_tools($db);
$tool->type = 'check';
$tool->title = 'Selection uniquement sur les affaires en cours ? ';
$tool->html_name = 'search_run';
$tool->filter = 'search_run';
$tool->default = 1;
$tool->see_all =1;
$tools['1'] = $tool;

$table->extra_tools =$tools;
$table->sub_title = array(1=>'Références',2=>'Commande Usine',3=>'Commande Client',4=>'Délais et retards');

$table->post();

$table->data_array();

$table->header();

$table->draw_tool_bar();

$table->draw_table_head();

$table->draw_data_table();

$table->end_table();