/*
*Ce fichier contient le JavaScript qui concerne l'application EENE
* 07 Juin 2016
*/

 $(document).ready(function() {

   
   //$("#btnAnalyze").attr("onclick",analyzeText());
     //utilisé en page de visualisation
     //navigation entre le contenu de fichier Text et les entités
     // $('#tabs').tab();

     //le datatable de page de l'accueil my files
     $('#tablefiles').DataTable(
    {  
       responsive: true,
       //par default ordre selon date de création de fichier
       order: [[ 1, "desc" ]],
     //last column not orderable 
       columnDefs: [
       {orderable: false, targets: 10 },
       {className: "dt-center", "targets":'_all'}],
       "oLanguage": {
        //message par default si pas d'enregistrement
        "sEmptyTable": "No files available on your account."
      }
    }
      );
      
       //le datatable de page de détail des entités 
     $('#tableentities').DataTable(
    { 
       responsive: true,
       //pour centrer les données de dataTable
     //  columnDefs: [
       //{className: "dt-center", "targets":'_all'}],
     
    }
      );
      //boutton close sur div qui affiche les traces de geolocalisation
   /* $('#btnClose').click(function(){
          //mettre le div parent caché
            $('#btnClose').parent().hide();
            alert("ccc");
            //redirection vers la page de my files
             window.location = Routing.generate('user_myfiles');
       });//fermer div d traces  *
     document.getElementById("btnClose").onclick = function () {
      alert('bbb');
        window.location = Routing.generate('user_myfiles');
    };*/
});