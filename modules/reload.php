<?php

/*
 * LMS version 1.0-cvs
 *
 *  (C) Copyright 2001-2003 LMS Developers
 *
 *  Please, see the doc/AUTHORS for more information about authors!
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License Version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307,
 *  USA.
 *
 *  $Id$
 */

// Agggrrr. Nie zwracajcie uwagi na styl pisania *TEGO* kawa�ka kodu :)
// Jest 7:35 a ja ca�� noc nie spa�em :)

$layout[$pagetitle]="Prze�adowanie ustawie�";

$SMARTY->display("header.html");

?><H1>Prze�adowanie ustawie�</H1><?

$_RELOAD_TYPE = (! $_CONFIG[phpui]['reload_type'] ? "sql" : $_CONFIG[phpui]['reload_type']);
$_EXECCMD = (! $_CONFIG[phpui]['reload_execcmd'] ? "/bin/true" : $_CONFIG[phpui]['reload_execcmd']);

switch($_RELOAD_TYPE)
{
	case "exec":
		$execlist = explode(";",$_EXECCMD);
		echo '<TABLE WIDTH="100%" BGCOLOR="#F4F0EC" CELLPADDING="5"><TR><TD CLASS="FALL">';
		foreach($execlist as $execcmd)
		{
			echo "<P><B>".$execcmd."</B>:</P>";
			echo "<PRE>";
			passthru($execcmd);
			echo "</PRE>";
		}
		echo '</TD></TR></TABLE>';
	break;

	case "lmsd-tcp":
		
		// hrhrhrhr :> Nieudokoumentowana opcja lmsd-tcp :>
		// czyli kompatybilno�� z tym starym poczciwym lmsd-0.1 z lms'a 0.4

		$better_token = md5(uniqid(rand(),1));
		$auth_string = md5($better_token);
		$ADB->Execute("INSERT INTO tokens (id, auth) VALUES (?, ?)",array($better_token,$auth_string));
		echo "<P class=\"hd1\"><B>Prze�adowanie ustawie�</B></p>\n";
		$service_port = (! $_CONFIG[tcplmsd][port] ? "2345" : $_CONFIG[tcplmsd][port]);
		$service_address = (! $_CONFIG[tcplmsd][address] ? "2345" : $_CONFIG[tcplmsd][address]);
		$address = gethostbyname ($service_address);
		$socket = socket_create (AF_INET, SOCK_STREAM, 0);
		if ($socket < 0)
			echo "socket_create() failed: reason: " . socket_strerror ($socket) . "\n";
		$result = socket_connect ($socket, $address, $service_port);
		if ($result < 0)
			echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
		$in = "SESSION $better_token\nAUTH $auth_string\nDHCP\nFRWL$lms_frwltype\nODNT\nQUIT\n";
		$out = '';
		socket_write ($socket, $in, strlen ($in));
		while ($out = socket_read ($socket, 2048)) 
			$obf .= $out;
		$out = $obf;
		socket_close ($socket);
		list($r_hello,$r_session,$r_auth,$r_dhcp,$r_frwl,$r_odnt) = explode("\n",$out);
		list($r_r,$r_time)=explode(" ",$r_hello);
		echo "Czas na zdalnej maszynie: ".date("Y/m/d H:i",$r_time)."<br>";
		if($r_session=="+OK"&&$r_auth=="+OK")
			echo "Autoryzacja si� powiod�a.<br>";
		else
			echo "Autoryzacja si� nie powiod�a.<br>";
		if($r_dhcp=="+OK")
			echo "Prze�adowanie DHCP: OK<br>";
		else
			echo "Prze�adowania DHCP: zablokowane!<br>";
		if($r_frwl=="+OK")
			echo "Prze�adowanie firewalla: OK<br>";
		else
			echo "Prze�adowania firewalla: zablokowane!<br>";
		if($r_odnt=="+OK")
			echo "Prze�adowanie oidnetd: OK<br>";
		else
			echo "Prze�adowania oidentd: zablokowane!<br>";
		$ADB->Execute("DELETE FROM tokens WHERE id=?",array($better_token));

	break;
		

	case "sql":
		if(isset($_CONFIG[phpui]['reload_sqlquery']))
		{
			$sqlqueries = explode(";",($_CONFIG[phpui]['reload_sqlquery']));
			echo '<TABLE WIDTH="100%" BGCOLOR="#F4F0EC" CELLPADDING="5"><TR><TD CLASS="FALL">';
			foreach($sqlqueries as $query)
			{
				$query = str_replace("%TIME%",$LMS->sqlTSfmt(),$query);
				echo "<P><B>Wykonuje:</B></P>";
				echo "<PRE>".$query."</PRE>";
				$ADB->Execute($query);
			}
			echo '</TD></TR></TABLE>';
		}else{
			if(isset($_GET[cancel]))
			{
				echo 'Usuni�to zlecenie prze�adowania konfiguracji.';
				$LMS->DeleteTS("_force");
			}else
				if($reloadtimestamp = $LMS->GetTS("_force"))
				{
					echo 'Wykryto zlecenie prze�adowania konfiguracji z '.date("Y/m/d H:i",time()).'.<BR>';
					echo 'Kliknij <A HREF="?m=reload&cancel">tutaj</a> aby anulowa� to zlecenie.';
				}else{
					echo 'Zapisano zlecenie prze�adowania konfiguracji w bazie danych.';
					$LMS->SetTS("_force");
				}
		}
	break;

	default:

		echo "<P><B><FONT COLOR=\"RED\">B��d! Niepoprawny typ reloadu: '".$_RELOAD_TYPE."' !</FONT></B></P>";
	break;

}

$SMARTY->display("footer.html");

?>
