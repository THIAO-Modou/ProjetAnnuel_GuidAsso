if (typeof jQuery == 'undefined') {
    console.log(" jQuery N'EST PAS chargé !");
} else {
    console.log(" jQuery EST chargé !");
}

console.log(" Le fichier assoc_prediction.js est bien chargé !");

$(document).ready(function(){

    function setupAutocomplete(inputSelector, suggestionsBoxSelector, ajaxURL) {

        $(document).on('keyup', inputSelector, function(){
            const query = $(this).val().trim();

            if(query !== ''){
                $.ajax({
                    url: ajaxURL,
                    method: "POST",
                    data: {query: query},
                    success: function(data){
                        $(suggestionsBoxSelector).fadeIn().html(data);
                    }
                });
            } else {
                $(suggestionsBoxSelector).fadeOut();
            }
        });

        $(document).on('click', suggestionsBoxSelector + ' li', function(){

            const data = $(this).data(); // récupère toutes les data-*

            // Remplit le champ concerné (association OU commune selon le champ)
            $(inputSelector).val(data.nom);

            $(suggestionsBoxSelector).fadeOut();

            // Émet l’événement correct
            $(document).trigger("associationSelected", data);
        });


        $(document).on('mouseover', suggestionsBoxSelector + ' li', function(){
            $(this).css('background-color', '#abcdf0');
        });

        $(document).on('mouseout', suggestionsBoxSelector + ' li', function(){
            $(this).css('background-color', '');
        });

        $(document).on('focusout', inputSelector, function(){
            setTimeout(() => $(suggestionsBoxSelector).fadeOut(), 200);
        });
    }
    // Appliquer l'autocomplétion aux champs d'association
    //setupAutocomplete('#association', '#associationSuggestions', '/../controllers/predict_PHP/recup_assoc.php'); // Nom association

    // Autocomplétion association
    setupAutocomplete('#association', '#associationSuggestions', '/../controllers/predict_PHP/api_association.php');

    setupAutocomplete('#association_fiche', '#associationSuggestions_fiche', '/../controllers/predict_PHP/recup_assoc.php'); // Fiche Asso 
});
