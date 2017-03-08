<?php
/* Copyright (C)    2013    Cédric Salvador    <csalvador@gpcsolutions.fr>
 * Copyright (C)    2015    Marcos García      <marcosgdf@gmail.com>
 * Copyright (C)    2015    Ferran Marcet      <fmarcet@2byte.es>
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
 * or see http://www.gnu.org/
 */


// TODO This is an action include, not a presentation template.
// Move this file into htdocs/core/actions_document.inc.php


// Variable $upload_dir must be defined when entering here
// Variable $upload_dirold may also exists.

// Send file/link
if (GETPOST('sendit'))
{
	
    if ($reprise->id)
    {
			dol_add_file_process($upload_dir, 0, 1, 'userfile', '');
    }
}

// Delete file/link
if ($action == 'confirm_deletefile' && $confirm == 'yes')
{
    if ($object->id)
    {
        $urlfile = GETPOST('urlfile', 'alpha');	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).
        if (GETPOST('section')) $file = $upload_dir . "/" . $urlfile;	// For a delete of GED module urlfile contains full path from upload_dir
        else															// For documents pages, upload_dir contains already path to file from module dir, so we clean path into urlfile.
		{
       		$urlfile=basename($urlfile);
			$file = $upload_dir . "/" . $urlfile;
			if (! empty($upload_dirold)) $fileold = $upload_dirold . "/" . $urlfile;
		}
        $linkid = GETPOST('linkid', 'int');	// Do not use urldecode here ($_GET and $_REQUEST are already decoded by PHP).

        if ($urlfile)
        {
	        $dir = dirname($file).'/';     // Chemin du dossier contenant l'image d'origine
	        $dirthumb = $dir.'/thumbs/';   // Chemin du dossier contenant la vignette

            $ret = dol_delete_file($file, 0, 0, 0, $object);
            if (! empty($fileold)) dol_delete_file($fileold, 0, 0, 0, $object);     // Delete file using old path
            
	        // Si elle existe, on efface la vignette
	        if (preg_match('/(\.jpg|\.jpeg|\.bmp|\.gif|\.png|\.tiff)$/i',$file,$regs))
	        {
		        $photo_vignette=basename(preg_replace('/'.$regs[0].'/i','',$file).'_small'.$regs[0]);
		        if (file_exists(dol_osencode($dirthumb.$photo_vignette)))
		        {
			        dol_delete_file($dirthumb.$photo_vignette);
		        }

		        $photo_vignette=basename(preg_replace('/'.$regs[0].'/i','',$file).'_mini'.$regs[0]);
		        if (file_exists(dol_osencode($dirthumb.$photo_vignette)))
		        {
			        dol_delete_file($dirthumb.$photo_vignette);
		        }
	        }

            if ($ret) setEventMessages($langs->trans("FileWasRemoved", $urlfile), null, 'mesgs');
            else setEventMessages($langs->trans("ErrorFailToDeleteFile", $urlfile), null, 'errors');
        }
        elseif ($linkid)
        {
            require_once DOL_DOCUMENT_ROOT . '/core/class/link.class.php';
            $link = new Link($db);
            $link->id = $linkid;
            $link->fetch();
            $res = $link->delete($user);

            $langs->load('link');
            if ($res > 0) {
                setEventMessages($langs->trans("LinkRemoved", $link->label), null, 'mesgs');
            } else {
                if (count($link->errors)) {
                    setEventMessages('', $link->errors, 'errors');
                } else {
                    setEventMessages($langs->trans("ErrorFailedToDeleteLink", $link->label), null, 'errors');
                }
            }
        }
        header('Location: ' . $_SERVER["PHP_SELF"] . '?id=' . $object->id.(!empty($withproject)?'&withproject=1':''));
        exit;
    }
}
elseif ($action == 'confirm_updateline' && GETPOST('save') && GETPOST('link', 'alpha'))
{
    require_once DOL_DOCUMENT_ROOT . '/core/class/link.class.php';
    $langs->load('link');
    $link = new Link($db);
    $link->id = GETPOST('linkid', 'int');
    $f = $link->fetch();
    if ($f)
    {
        $link->url = GETPOST('link', 'alpha');
        if (substr($link->url, 0, 7) != 'http://' && substr($link->url, 0, 8) != 'https://')
        {
            $link->url = 'http://' . $link->url;
        }
        $link->label = GETPOST('label', 'alpha');
        $res = $link->update($user);
        if (!$res)
        {
            setEventMessages($langs->trans("ErrorFailedToUpdateLink", $link->label), null, 'mesgs');
        }
    }
    else
    {
        //error fetching
    }
}

