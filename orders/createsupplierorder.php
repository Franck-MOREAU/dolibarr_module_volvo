<?php
/* Volvo
 * Copyright (C) 2014-2015 Florian HENRY <florian.henry@open-concept.pro>
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
$res = @include '../main.inc.php'; // For root directory
if (! $res)
	$res = @include '../../main.inc.php'; // For "custom" directory
if (! $res)
	die("Include of main fails");

require_once DOL_DOCUMENT_ROOT . '/commande/class/commande.class.php';
require_once DOL_DOCUMENT_ROOT . '/product/class/product.class.php';
dol_include_once('/volvo/class/html.formvolvo.class.php');
dol_include_once('/volvo/class/commandevolvo.class.php');

$langs->load('orders');
$langs->load('companies');
$langs->load('products');
$langs->load('volvo@volvo');

$orderid = GETPOST('orderid', 'int');
$action = GETPOST('action', 'alpha');
$idlines = GETPOST('idlines', 'alpha');

$error = 0;

$order = new Commande($db);
$product = new Product($db);
$formvolvo = new FormVolvo($db);

$result = $order->fetch($orderid);
if ($result < 0) {
	setEventMessages($order->error, null, 'errors');
}

$linedisplay_array = array();
if (is_array($order->lines) && count($order->lines)) {
	foreach ( $order->lines as $line ) {

		if ($line->product_type == 9 && $line->qty == 1) {

			if (count($linedisplay_array) > 0) {
				// Test if previous element is also an Sub Total
				$lastelement = end($linedisplay_array);
				if ($lastelement->product_type == 9) {
					// in this case remove last element because no need to display empty subtotal lines
					array_pop($linedisplay_array);
				}
			}
			$linedisplay_array[$line->id] = $line;
		} elseif ($line->product_type != 9) {
			if (! empty($line->fk_product)) {
				$prod = new Product($db);
				$result = $prod->fetch($line->fk_product);
				if ($result < 0) {
					setEventMessages(null, array(
							'Error Fetch Product'
					), 'errors');
				} elseif (! empty($prod->array_options['options_supplierorderable'])) {
					// Do not include product that cannot be updated
					$linedisplay_array[$line->id] = $line;
				}
			}
		}
	}
	$lastelement = end($linedisplay_array);
	if ($lastelement->product_type == 9) {
		// in this case remove last element because no need to display empty subtotal lines
		array_pop($linedisplay_array);
	}
}

top_htmlhead('', '');

if ($action == 'createsupplerorder') {
	$price_qty_array = array();
	$errors = array();
	$lineupdate_array = explode(',', $idlines);
	foreach ( $order->lines as $line ) {

		if (in_array($line->id, $lineupdate_array)) {
			$priceid = GETPOST('fournprice_' . $line->id);
			if (! empty($priceid)) {
				$price_qty_array[$priceid] = array('qty'=>$line->qty,'desc'=>$line->desc,'px'=>$line->pa_ht);
			}
		}
	}
	if (count($price_qty_array) > 0) {
		$cmdv = new CommandeVolvo($db);
		$result = $cmdv->createSupplierOrder($user, $price_qty_array, $order->id);
		if ($result < 0) {
			$error ++;
			$errors = $cmdv->errors;
		}
	}

	if (! empty($error)) {
		setEventMessages(null, $errors, 'errors');
	} else {
		print '<script type="text/javascript">' . "\n";
		print '	$(document).ready(function () {' . "\n";
		print '	window.parent.$(\'#popSupplierOrder\').dialog(\'close\');' . "\n";
		print '	window.parent.$(\'#popSupplierOrder\').remove();' . "\n";
		print '});' . "\n";
		print '</script>' . "\n";
	}
}

print '<form name="createsupplerorder" action="' . $_SERVER["PHP_SELF"] . '" method="POST">';
print '<input type="hidden" name="token" value="' . $_SESSION['newtoken'] . '">';
print '<input type="hidden" name="orderid" value="' . $orderid . '">';
print '<input type="hidden" name="action" value="createsupplerorder">';

print '<table class="border" width="100%">';
print '<tr class="liste_titre">';
print '<th align="center">' . $langs->trans('Description') . '</th>';
print '<th align="center">' . $langs->trans('PriceUHT') . '</th>';
print '<th align="center">' . $langs->trans('Qty') . '</th>';
print '<th align="center">' . $langs->trans('SupplierPrice') . '</th>';
print '</tr>';

if (is_array($order->lines) && count($order->lines)) {
	$lineupdate_array = array();
	foreach ( $order->lines as $line ) {
		$var = ! $var;

		if ($line->product_type != 9) {
			if (array_key_exists($line->id, $linedisplay_array)) {
				$lineupdate_array[] = $line->id;

				$line->fetch_optionals($line->id, $extralabelslines);

				print '<tr ' . $bc[$var] . '>';

				if (! empty($line->fk_product)) {
					$productdesc = $line->product_ref . ' - ' . $line->product_label;
				} else {
					$productdesc = $line->label . ' ' . $line->description;
				}

				print '<td align="center">' . $productdesc . '</td>';
				print '<td align="center">' . price($line->total_ht) . '</td>';
				print '<td align="center">' . $line->qty . '</td>';
				print '<td align="center">' . $formvolvo->selectFournPrice('fournprice_' . $line->id, GETPOST('fournprice_' . $line->id), $line->fk_product) . '</td>';
				print '</tr>';
			}
		} elseif ($line->qty == 1 && array_key_exists($line->id, $linedisplay_array)) {
			print '<tr style="background-color:#adadcf;">';
			print '<td style="font-weight:bold;   font-style: italic;" colspan="6">' . $line->description . '</td>';
			print '</tr>';
		}
	}
}
print '</table>';
print '<div class="tabsAction">';
print '<input type="hidden" name="idlines" value="' . implode(',', $lineupdate_array) . '"/>';
print '<input type="submit" align="center" class="button" value="' . $langs->trans('Save') . '" name="save" id="save"/>';
print '</div>';
print '</form>';

llxFooter();
$db->close();