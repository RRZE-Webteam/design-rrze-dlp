<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$defaultoptions = array(
    'version'   => '2.0',
    'firsttab'	=> 'sichtbarkeit',
    'datafields' => array(    
	    'service'			    =>  array(
		 'label'=> 'Service',
		 'desc'  => 'Kurzbeschreibung der DL (1 Satz)',
		 'id'    => 'service',
		 'public'    => 1,
		 'type'  => 'textarea'

	     ),
	     'beschreibung'		    => array(
		 'label'=> 'Beschreibung',
		 'desc'  => 'ausführliche, erläuternde Beschreibung der DL (3-10 Sätze) Leitsätze: Wie kann man die DL treffend und umfänglich mit einem Satz beschreiben? Was kann der Kunde damit tun? Welche Voraussetzungen werden an die Nutzung der DL gestellt? Welchen Nutzen zieht der Kunde aus der Benutzung der DL? Jeder Bestandteil der DL ist in einem vollständigen Satz zu formulieren. Der Text ist so formulieren, dass ein technischer Laie es versteht (Layer 8 Kompatibilität!). Fachbegriffe erklären und ins Glossar verlinken. Zuständig für abschließende Formulierung: Redaktion',
		 'id'    => 'beschreibung',
		 'public'    => 1,   
		 'type'  => 'textarea'
	     ),
	     'umfang'    =>array(
		 'label'=> 'Umfang',
		 'desc'  => 'Was bekommt der Kunde - differenziert nach Standard-Kundengruppen (diese sind definiert unter DLP:Kundengruppen) Für Studierende? Für Beschäftigte? Für Sonstige?',
		 'id'    => 'umfang',
		 'public'    => 1,
		 'type'  => 'textarea'
	     ),
	     'links_zu_dokumentation'	    =>array(
		 'label'=> 'Links zu Dokumentation',
		 'desc'  => 'Hier bitte die Seiten im Wiki verlinken, die den Kunden die Benutzung der DL erläutern. Hier gehören auch Links auf die Dokumentation der zur Erbringung der DL benötigten Server hin! Außerdem die zur Erbringung notwendige Software. Sollte die Software von uns selbst paketiert werden, so bitte auf die entsprechenden Projekte auf dem rembo (Windows) oder OBS (OpenSUSE Build Service, Linux) verlinken. notfalls erstmal Links auf entsprechende RRZE-Webseiten. Diese dann aber bei Zeiten ins RRZE-Wiki überführen!',
		 'id'    => 'links_zu_dokumentation',
		 'public'    => 1,
		 'type'  => 'textarea'
	     ),
	     'basisdienstleistungen'	    => array(
		 'label'=> 'Basisdienstleistungen',
		 'desc'  => 'Was ist für die Erbringung dieser DL direkt notwendig - Zuständig: Gruppe/Abteilung bzw. Person - Abteilung - Zuständig: Abteilungen, besser Gruppen - nur bei wirklich sehr personenbezogenen Zuständigkeiten, das Namenskürzel der Person inkl. Angabe der Abteilung',
		 'id'    => 'basisdienstleistungen',
		     'public'    => 0,
		 'type'  => 'textarea'
	     ),
	     'preis_basisdienstleistungen' => array(
		 'label'=> 'Preis Basisdienstleistungen',
		 'desc'  => '«TODO: sollte hier ein kalkulatorische Preis stehen, wenn die DL für den Nutzer kostenlos ist?»',
		 'id'    => 'preis_basisdienstleistungen',
		     'public'    => 0,
		 'type'  => 'textarea'
	     ),
	     'leistungserweiterungen'	    =>array(
		'label'=> 'Leistungserweiterungen',
		'desc'  => 'Welche Erweiterungen zur DL sind verfügbar? (optional) Zuständig: Gruppe/Abteilung bzw. Person - Abteilung - Zuständig: Abteilungen, besser Gruppen - nur bei wirklich sehr personenbezogenen Zuständigkeiten, das Namenskürzel der Person inkl. Angabe der Abteilung',
		'id'    => 'leistungserweiterungen',
		'public'    => 0,
		'type'  => 'textarea'
	     ),
		'preis_leistungserweiterungen'  => array(
		'label'=> 'Preis Leistungserweiterungen',
		'desc'  => 'Aus WIKI: «TODO: Sollte hier ein kalkulatorischer Preis stehen, wenn die DL für den Nutzer kostenlos ist oder sind Erweiterungen immer kostenpflichtig?»',
		'id'    => 'preis_leistungserweiterungen',
		'public'    => 0,
		'type'  => 'textarea'
	     ),
	     'kontakt'			    => array(
		'label'=> 'Kontakt',
		'desc'  => 'ansonsten: DL-spezifisches Funktionspostfach',
		'id'    => 'kontakt',
		'public'    => 1,
		'type'  => 'textarea'
	     ),
	     'abhaengigkeiten'		    =>array(
		'label'=> 'Abhängigkeiten',
		'desc'  => 'andere DL von denen diese (als Vorbedingung) abhängig ist] Aus WIKI: «TODO: Abhängigkeiten klären durchgängiges Nummerierungsssystem - angelehnt an die KLR?»',
		'id'    => 'abhaengigkeiten',
		'public'    => 0,
		'type'  => 'textarea'
	     )
    )
);    

$setoptions = array(
   'rrze_dlp_theme_options'   => array(
       
       'sichtbarkeit'   => array(
           'tabtitle'   => __('Sichtbarkeit', 'rrze-dlp'),
           'fields' => array(  ),
	)   
    )
);



foreach ($defaultoptions['datafields'] as $key => $value) {
    $name = 'display_'.$key;
    $setoptions['rrze_dlp_theme_options']['sichtbarkeit']['fields'][$name] = array(
	'type'    => 'bool',
	'title'   => $defaultoptions['datafields'][$key]['label'],
	'default' => $defaultoptions['datafields'][$key]['public'],
	'label'   => '&Ouml;ffentlich',
    );
}

