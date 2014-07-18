<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$defaultoptions = array(
    'version'	=> '2.1',
    'jsversion'	=> '20140703',
);



//Field Array
$custom_meta_fields = array(
    array(
        'label'=> 'Service',
        'desc'  => 'Kurzbeschreibung der DL (1 Satz)',
        'id'    => 'service',
        'type'  => 'text'
    ),
	array(
        'label'=> 'Beschreibung',
        'desc'  => 'ausführliche, erläuternde Beschreibung der DL (3-10 Sätze) Leitsätze: Wie kann man die DL treffend und umfänglich mit einem Satz beschreiben? Was kann der Kunde damit tun? Welche Voraussetzungen werden an die Nutzung der DL gestellt? Welchen Nutzen zieht der Kunde aus der Benutzung der DL? Jeder Bestandteil der DL ist in einem vollständigen Satz zu formulieren. Der Text ist so formulieren, dass ein technischer Laie es versteht (Layer 8 Kompatibilität!). Fachbegriffe erklären und ins Glossar verlinken. Zuständig für abschließende Formulierung: Redaktion',
        'id'    => 'beschreibung',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Umfang',
        'desc'  => 'Was bekommt der Kunde - differenziert nach Standard-Kundengruppen (diese sind definiert unter DLP:Kundengruppen) Für Studierende? Für Beschäftigte? Für Sonstige?',
        'id'    => 'umfang',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Links zu Dokumentation',
        'desc'  => 'Hier bitte die Seiten im Wiki verlinken, die den Kunden die Benutzung der DL erläutern. Hier gehören auch Links auf die Dokumentation der zur Erbringung der DL benötigten Server hin! Außerdem die zur Erbringung notwendige Software. Sollte die Software von uns selbst paketiert werden, so bitte auf die entsprechenden Projekte auf dem rembo (Windows) oder OBS (OpenSUSE Build Service, Linux) verlinken. notfalls erstmal Links auf entsprechende RRZE-Webseiten. Diese dann aber bei Zeiten ins RRZE-Wiki überführen!',
        'id'    => 'links_zu_dokumentation',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Basisdienstleistungen',
        'desc'  => 'Was ist für die Erbringung dieser DL direkt notwendig - Zuständig: Gruppe/Abteilung bzw. Person - Abteilung - Zuständig: Abteilungen, besser Gruppen - nur bei wirklich sehr personenbezogenen Zuständigkeiten, das Namenskürzel der Person inkl. Angabe der Abteilung',
        'id'    => 'basisdienstleistungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Preis Basisdienstleistungen',
        'desc'  => 'Angabe des Preises oder verweis auf die zentrale Preisübersicht.',
        'id'    => 'preis_basisdienstleistungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Leistungserweiterungen',
        'desc'  => 'Welche Erweiterungen zur DL sind verfügbar? (optional) Zuständig: Gruppe/Abteilung bzw. Person - Abteilung - Zuständig: Abteilungen, besser Gruppen - nur bei wirklich sehr personenbezogenen Zuständigkeiten, das Namenskürzel der Person inkl. Angabe der Abteilung',
        'id'    => 'leistungserweiterungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Preis Leistungserweiterungen',
        'desc'  => 'Aus WIKI: «TODO: Sollte hier ein kalkulatorischer Preis stehen, wenn die DL für den Nutzer kostenlos ist oder sind Erweiterungen immer kostenpflichtig?»',
        'id'    => 'preis_leistungserweiterungen',
        'type'  => 'textarea'
    ),
	array(
        'label'=> 'Kontakt',
        'desc'  => 'ansonsten: DL-spezifisches Funktionspostfach',
        'id'    => 'kontakt',
        'type'  => 'textarea',
    ),
	array(
        'label'=> 'Abhängigkeiten',
        'desc'  => 'andere DL von denen diese (als Vorbedingung) abhängig ist]',
        'id'    => 'abhaengigkeiten',
        'type'  => 'textarea'
    )
);


$display_field = array(
	    'service'			    => 1,
	    'beschreibung'		    => 1,
	    'umfang'			    => 1,
	    'links_zu_dokumentation'	    => 1,
	    'basisdienstleistungen'	    => 0,
	    'preis_basisdienstleistungen'   => 0,
	    'leistungserweiterungen'	    => 0,
	    'preis_leistungserweiterungen'  => 0,
	    'kontakt'			    => 1,
	    'abhaengigkeiten'		    => 0,
	);
/*
 *	Absprache vom 2.10.2013:
  	    Folgende Abschnitte werden NICHT allgemein angezeigt:
	    - Basisdienstleistungen
	    - Preis Basisdienstleistungen
	    - Leistungserweiterungen
	    - Preis Leistungserweiterungen
	    - Abhängigkeiten
 */

	$field_label = array(
	    'service'			    => 'Service',
	    'beschreibung'		    => 'Beschreibung',
	    'umfang'			    => 'Umfang',
	    'links_zu_dokumentation'	    => 'Links zur Dokumentation',
	    'basisdienstleistungen'	    => 'Basisdienstleistungen',
	    'preis_basisdienstleistungen'   => 'Preis Basisdienstleistungen',
	    'leistungserweiterungen'	    => 'Leistungserweiterungen',
	    'preis_leistungserweiterungen'  => 'Preis Leistungserweiterungen',
	    'kontakt'			    => 'Kontakt',
	    'abhaengigkeiten'		    => 'Abhängigkeiten',
	);
	
	$kontaktdata = array(
	    "kontakt_name"  => array(
		'type'	=>  'text',
		'title'	=>  'Name', 
	    ),
	    "kontakt_telefon"  => array(
		'type'	=>  'text',
		'title'	=>  'Telefon', 
	    ),
	    "kontakt_email"  => array(
		'type'	=>  'email',
		'title'	=>  'E-Mail-Adresse', 
	    ),
	    "kontakt_url"  => array(
		'type'	=>  'url',
		'title'	=>  'Webseite (URL)', 
	    ),
	    "kontakt_addresse"  => array(
		'type'	=>  'textarea',
		'title'	=>  'Postanschrift', 
	    ),

	);

	$kostendata = array(
	    'artikelnummer' => array(
		'type'	=> 'text',
		'title'	=> 'Artikelnummer'
	    ),
	    'einheit' => array(
		'type'	=> 'number',
		'title'	=> 'Abrechnungseinheit'
	    ),
	    'einheitbez' => array(
		'type'	=> 'text',
		'title'	=> 'Einheitenbezeichnung (Monat, Jahr, einmalig, ...)'
	    ),
	    'titel' => array(
		'type'	=> 'text',
		'title'	=> 'Beschreibung'
	    ),
	    'desc' => array(
		'type'	=> 'textarea',
		'title'	=> 'Beschreibung'
	    ),
	    'preis_kg1'	=> array(
		'type'	=> 'text',
		'title'	=> 'Preis Kostengruppe I'
	    ),
	     'preis_kg2'	=> array(
		'type'	=> 'text',
		'title'	=> 'Preis Kostengruppe II'
	    ),
	     'preis_kg3'	=> array(
		'type'	=> 'text',
		'title'	=> 'Preis Kostengruppe III'
	    ),
	     'preis_kg4'	=> array(
		'type'	=> 'text',
		'title'	=> 'Preis Kostengruppe IV'
	    ),
	    
	);