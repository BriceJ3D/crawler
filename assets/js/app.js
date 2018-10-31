/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.css');
require('../css/global.scss');
require('bootstrap');
require( 'datatables.net-bs4' );
require( 'datatables.net-fixedheader-bs4' );
require( 'datatables.net-responsive-bs4');
require('@fortawesome/fontawesome-free/js/all.js');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
//var $ = require('jquery');
//var dt = require( 'datatables.net' )();

$(document).ready(function() {
	$('.dataTable').DataTable({
    language: {
        processing:     "Traitement en cours...",
        search:         "Rechercher&nbsp;:",
        lengthMenu:    "Afficher _MENU_ &eacute;l&eacute;ments",
        info:           "Affichage de l'&eacute;lement _START_ &agrave; _END_ sur _TOTAL_ &eacute;l&eacute;ments",
        infoEmpty:      "Affichage de l'&eacute;lement 0 &agrave; 0 sur 0 &eacute;l&eacute;ments",
        infoFiltered:   "(filtr&eacute; de _MAX_ &eacute;l&eacute;ments au total)",
        infoPostFix:    "",
        loadingRecords: "Chargement en cours...",
        zeroRecords:    "Aucun &eacute;l&eacute;ment &agrave; afficher",
        emptyTable:     "Aucune donnée disponible dans le tableau",
        paginate: {
            first:      "Premier",
            previous:   "Pr&eacute;c&eacute;dent",
            next:       "Suivant",
            last:       "Dernier"
        },
        aria: {
            sortAscending:  ": activer pour trier la colonne par ordre croissant",
            sortDescending: ": activer pour trier la colonne par ordre décroissant"
        }
    }
  });
	   //copie de textarea des domaines dans le presse papier
   	$('#clipboard').click(function(){
      $('#textareaToCopy').select();
      document.execCommand('copy');
   	});

    $('.buttonToCopy').click(function(){
      $(this).parent().children('.domainToCopy').select();
      document.execCommand('copy');
      $(this).parent().children('.domainToCopy').blur()
    });

    //ajout des villes dans la textarea des tags
   	$('#tags_city').click(function(){
      let keyword = $('#tags_keywords').val().trim();
      if (keyword != ''){
   			let result = keyword;
	   		$('.city').each(function(){
	   			result += '\r\n' + keyword + ' ' + $(this).val();
	   		});
	   		$('#tags_keywords').val(result);
	   	}
   	});
});

