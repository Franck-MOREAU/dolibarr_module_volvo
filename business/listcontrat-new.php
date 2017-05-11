<?php
/* Copyright (C) 2001-2004 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@capnetworks.com>
 * Copyright (C) 2013      Cédric Salvador      <csalvador@gpcsolutions.fr>
 * Copyright (C) 2014      Juanjo Menent        <jmenent@2byte.es>
 * Copyright (C) 2015	   Claudio Aschieri		<c.aschieri@19.coop>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 * Copyright (C) 2016      Ferran Marcet        <fmarcet@2byte.es>
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

/**
 *       \file       htdocs/contrat/list.php
 *       \ingroup    contrat
 *       \brief      Page liste des contrats
 */


$res = @include '../../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

// Security check
if (! $user->rights->volvo->contrat)
	accessforbidden();

require_once DOL_DOCUMENT_ROOT . '/volvo/class/table_template.class.php';
require_once (DOL_DOCUMENT_ROOT."/contrat/class/contrat.class.php");

$staticcontrat=new Contrat($db);
$staticcontratligne=new ContratLigne($db);
$table = New Dyntable($db);

$langs->load("contracts");
$langs->load("products");
$langs->load("companies");
$langs->load("compta");

$action = GETPOST('action');
$element = GETPOST('element');
$id=GETPOST('id');

$table->title = $langs->trans("ListOfContracts");
$table->default_sortfield = 'c.ref';
$table->export_name = 'liste_contrat_new';
$table->context = 'contractlist';
$table->search_button = 1;
$table->remove_filter_button = 1;
$table->export_button = 1;
$table->select_fields_button = 1;
$table->mode = 'sql_methode';
$table->limit = $conf->liste_limit;

$field= new Dyntable_fields($db);
$field->name='ref';
$field->label = 'N° de Contrat';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'c.ref';
$field->group =1;
$field->align = 'left';
$field->alias = 'ref';
$field->post_traitement = array('link', '/contrat/card.php','?id=','cid');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_ref';
$tool->filter = 'c.ref';
$tool->size = 7;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='immat';
$field->label = 'Immat';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'c.ref_customer';
$field->group =1;
$field->align = 'center';
$field->alias = 'ref_customer';
$field->post_traitement = array('none');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_immat';
$tool->filter = 'c.ref_customer';
$tool->size = 4;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='vin';
$field->label = 'N° de Chassis';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 'c.ref_supplier';
$field->group =1;
$field->align = 'center';
$field->alias = 'ref_supplier';
$field->post_traitement = array('none');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_vin';
$tool->filter = 'c.ref_supplier';
$tool->size = 4;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='client';
$field->label = 'Client';
$field->checked = 1;
$field->sub_title = 0;
$field->field = 's.nom';
$field->group =1;
$field->align = 'center';
$field->alias = 'name';
$field->post_traitement = array('link', '/societe/soc.php','?socid=','societe');
$tools=array();
$tool = new Dyntable_tools($db);
$tool->type = 'text';
$tool->title = '';
$tool->html_name = 'search_client';
$tool->filter = 's.nom';
$tool->size = 26;
$tools['1'] = $tool;
$field->filter = $tools;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='comm';
$field->label = 'Commercial';
$field->checked = 1;
$field->sub_title = 0;
$field->field = "CONCAT(u.firstname,'',u.lastname)";
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
$field->name='cid';
$field->enabled = false;
$field->alias = 'cid';
$field->field = 'c.rowid';
$field->group =1;
$table->arrayfields[$field->name] = $field;

$field= new Dyntable_fields($db);
$field->name='societe';
$field->enabled = false;
$field->alias = 'socid';
$field->field = 's.rowid';
$field->group =1;
$table->arrayfields[$field->name] = $field;


$table->sql_from.= MAIN_DB_PREFIX . "contrat AS c ";
$table->sql_from.= "INNER JOIN " . MAIN_DB_PREFIX . "societe AS s ON s.rowid = c.fk_soc ";
$table->sql_from.= "LEFT JOIN " . MAIN_DB_PREFIX . "user AS u ON u.rowid = c.fk_commercial_suivi ";
$table->sql_from.= "INNER JOIN " . MAIN_DB_PREFIX . "contratdet AS cd ON c.rowid = cd.fk_contrat ";
$table->sql_from.= "LEFT JOIN " . MAIN_DB_PREFIX . "contrat_extrafields AS ef ON c.rowid = ef.fk_object";

$table->sql_where = 'c.entity IN ('.getEntity('contract', 1).')';

/*
 * Action
 */



if($action=='confirm_set_date'){
	$contrat = New Contrat($db);
	$contrat->fetch($id);
	$contrat->array_options['options_' . $element]=dol_mktime(0, 0, 0, GETPOST('date_actionmonth'), GETPOST('date_actionday'), GETPOST('date_actionyear'));
	$contrat->insertExtraFields();
}

/*
 * View
 */
$table->post();

$table->data_array();



$table->header();

if ($action == 'set_date') {
	$form = new Form($db);
	$formconfirm = $form->formconfirm($_SERVER["PHP_SELF"] . '?id='. $id . '&element=' . $element . $param, "Valider et passer a l'étape suivante", '', 'confirm_set_date', array(array(
			'type' => 'date',
			'name' => 'date_action',
			'label'=> "date de l'action"
	)), '', 1);
}

print $table->sql;

if(!empty($formconfirm)) print $formconfirm;

$table->draw_tool_bar();

$table->draw_table_head();

$table->draw_data_table();

$table->end_table();